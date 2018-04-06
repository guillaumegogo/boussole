<?php
//********* remplacement des caractères effacés du fichier de l'INSEE
function format_insee($saisie)
{
	$tab_aremplacer = array("-", "_", "  ", "à", "é", "è", "ï", "ö", "ü", "saint ", "Saint ");
	$tab_rempace = array(" ", " ", " ", "a", "e", "e", "i", "o", "st ", "st ");
	return str_replace($tab_aremplacer, $tab_rempace, $saisie);
}

function sans_accent($str)
{
	$url = $str;
    $url = preg_replace('#à|â#', 'a', $url);
    $url = preg_replace('#è|é|ê#', 'e', $url);
    $url = preg_replace('#ï#', 'i', $url);
    $url = preg_replace('#ô#', 'o', $url);
    $url = preg_replace('#ù|û#', 'u', $url);
    $url = preg_replace('#ç#', 'c', $url);
    return ($url);
}

//********** raccourcit un texte
function raccourci($str, $taille){
	$txt = ((strlen($str) > $taille ) && (strpos($str,' ', $taille))) ? substr($str,0,strpos($str,' ', $taille)).'…' : $str;
    return ($txt);
}

//********* fonction de présentation (un peu crado) des critères du jeune
function liste_criteres($tab_criteres, $separateur = ", ")
{
	$txt_criteres = '';
	foreach ($tab_criteres as $index => $valeur) {
		$txt = '';
		if ($valeur) {
			$txt = str_replace("_", " ", $index) . " : ";
			if (is_array($valeur)) {
				foreach ($valeur as $index2 => $valeur2)
					$txt .= $valeur2 . "/";
				$txt = substr($txt, 0, -1);
			} else {
				$txt .= $valeur;
			}
			$txt_criteres .= xssafe($txt) . $separateur;
		}
	}
	$txt_criteres = substr($txt_criteres, 0, -strlen($separateur));
	return $txt_criteres;
}

//******** présentation des questions du formulaire
function ouverture_ligne($ele)
{ //affichage ligne préalable si le type le demande
	//Voir si implémentation xssafe possible ou si ça fait sauter les "name"
	$t = '';
	switch ($ele['type']) {
		case 'radio':
		case 'checkbox':
			$t = '<div style="display:inline-table;">';
			break;
		case 'select':
			$r = ($ele['obl']) ? 'required':'';
			$t = '<select name="' . $ele['name'] . '" ' . $r . ' ><option value="" label=""> ';
			break;
		case 'multiple':
			$r = ($ele['obl']) ? 'required':'';
			$t = '<select multiple name="' . $ele['name'] . '[]" size="' . $ele['tai'] . '" ' . $r . ' >';
			break;
	}
	return $t;
}

//Voir si implémentation xssafe possible ou si ça fait sauter les "name"
function affiche_valeur($ele, $type, $requis)
{ //affichage valeur
	$t = '';
	switch ($type) {
		case 'radio':
			$r = ($requis) ? 'required':'';
			$t = '<input type="radio" name="' . $ele['name'] . '" value="' . $ele['val'] . '" id="' . $ele['val'] . $ele['name'] . '" ' . $r ;
			if (isset($_SESSION['web']['critere'][$ele['name']])) {
				if ($_SESSION['web']['critere'][$ele['name']] == $ele['val']) $t .= ' checked ';
			} else if ($ele['def'] == 1) $t .= ' checked ';
			$t .= '> ' . '<label for="' . $ele['val'] . $ele['name'] .'">' . xssafe($ele['lib']). '</label>' . "\n";
			break;
		case 'checkbox':
			$t = '<input type="checkbox" name="' . $ele['name'] . '[]" value="' . $ele['val'] . '" ' . '" id="' . $ele['val'] . $ele['name'] . '" ';
			if (isset($_SESSION['web']['critere'][$ele['name']])) {
				if (in_array($ele['val'], $_SESSION['web']['critere'][$ele['name']])) $t .= ' checked ';
			} else if ($ele['def'] == 1) $t .= ' checked ';
			$t .= '> ' . '<label for="' . $ele['val'] . $ele['name'] .'">' . xssafe($ele['lib']) . '</label>' . '</br>' . "\n";
			break;
		case 'select':
			$t = '<option value="' . $ele['val'] . '" ';
			if (isset($_SESSION['web']['critere'][$ele['name']])) {
				if ($_SESSION['web']['critere'][$ele['name']] == $ele['val']) $t .= ' selected ';
			} else if ($ele['def'] == 1) $t .= ' selected ';
			$t .= '> ' . xssafe($ele['lib']) . '</option>' . "\n";
			break;
		case 'multiple':
			$t = '<option value="' . $ele['val'] . '" ';
			if (isset($_SESSION['web']['critere'][$ele['name']])) {
				if (in_array($ele['val'], $_SESSION['web']['critere'][$ele['name']])) $t .= ' selected ';
			} else if ($ele['def'] == 1) $t .= ' selected ';
			$t .= '> ' . xssafe($ele['lib']) . '</option>' . "\n";
			break;
	}
	return $t;
}

function cloture_ligne($ele)
{
	$t = '';
	switch ($ele['type']) {
		case 'radio':
		case 'checkbox':
			$t = '</div>' . $t;
			break;
		case 'select':
		case 'multiple':
			$t = '</select>' . $t;
			break;
	}
	
	//une fois le critère affiché (avec la préselection), on vide la session du critere correspondant
	//unset($_SESSION['web']['critere'][$ele['name']]);
	
	return $t;
}

function envoi_mails_demande($courriel_offre, $nom_offre, $coordonnees, $criteres=null, $token)
{
	global $path_extranet;
	$resultat = null;

    //au professionnel
    $to = 'boussole@yopmail.fr';
    if (ENVIRONMENT === ENV_PROD) {
        $to = $courriel_offre;
    }
    $subject = mb_encode_mimeheader('Une demande a été déposée sur la Boussole des jeunes');
    $message = "<html><p>Bonjour, un jeune est intéressé par l'offre <b>" . $nom_offre . "</b>.</p>"
        . "<p>Il a déposé une demande de contact le " . utf8_encode(strftime('%d %B %Y &agrave; %H:%M')) . "</p>";
	if($criteres) {
		$message .= "<p>Son profil est le suivant :<br/>" . liste_criteres($criteres, '<br/>') . "</p>";
	}else{
		$message .= "<p>Il n'a pas indiqué son profil (accès direct à l'offre).</p>";
	}
    $message .= "<p>Les coordonnées indiquées sont les suivantes : <b>" . $coordonnees . "</b></p>"
        . "<p>Merci d'indiquer la suite donnée à la demande dans l'<a href=\"http://" . $_SERVER['SERVER_NAME'] . $path_extranet . "/demande_detail.php?hash=".$token."\">espace de gestion de la Boussole</a></p></html>";
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: La Boussole des jeunes <noreply@boussole.jeunes.gouv.fr>' . "\r\n";
    if (ENVIRONMENT !== ENV_PROD) {
        $headers .= 'Cc: guillaume.gogo@jeunesse-sports.gouv.fr' . "\r\n";
    }

    $envoi_mail = mail($to, $subject, $message, $headers);
	//todo : check http://foundationphp.com/tutorials/email.php 

    //accusé d'envoi au demandeur par mail
    if (filter_var($coordonnees, FILTER_VALIDATE_EMAIL)) {

        $to = $coordonnees;
        $subject = mb_encode_mimeheader('Vous avez déposé une demande de contact sur la Boussole des jeunes');
		$message = "<html><p>Bonjour!</p>"
            . "<p>Votre demande de contact a bien été enregistrée par la Boussole des jeunes.</p>"
            . "<p>L'offre de service que vous avez choisie est <b>" . $nom_offre . "</b>.</p>";
		if($criteres) {
			//$message .= "<p>Les informations que vous avez indiqué sont les suivantes : <b>" . liste_criteres($criteres, '<br/>') . "</b>.</p>";
		}
        $message .= "<p>L'organisme en charge de cette offre prendra contact avec vous sous peu !</p>"
            . "<p>A bientôt</p>"
            . "<p>L’équipe Boussole des jeunes</p>";
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: La Boussole des jeunes <noreply@boussole.jeunes.gouv.fr>' . "\r\n";
        if (ENVIRONMENT !== ENV_PROD) {
            $headers .= 'Cc: guillaume.gogo@jeunesse-sports.gouv.fr' . "\r\n";
        }
        $envoi_accuse = mail($to, $subject, $message, $headers);
		
    //accusé d'envoi au demandeur par sms (réservé à la beta en attendant compte de prod)
    }else if (preg_match('/^(0[67]([[\d]){8})$/', $coordonnees) && ENVIRONMENT === ENV_BETA) {
		
		$soapclient = new SoapClient(SMS_WSDL);
		
		$message = "Bonjour! Ta demande pour \"" . raccourci($nom_offre,50). "\" a bien été reçue par la Boussole. A bientôt!";
		$num_tel = preg_replace('/^0/', '33', $_POST['coordonnees']);
		$params = array(
			'correlationId' => '#NULL#', 
			'originatingAddress' => '#NULL#',
			'originatorTON' => '-1', 
			'destinationAddress' => $num_tel, 
			'messageText' => $message,
			'maxConcatenatedMessages' => '-1',
			'PID' => '-1', 
			'relativeValidityTime' => '-1',
			'deliveryTime' => '#NULL#', 
			'statusReportFlags' => '-1',
			'accountName' => '#NULL#',
			'referenceId' => '#NULL#', 
			'serviceMetaData' => '#NULL#',
			'campaignName' => '#NULL#', 
			'username' => SMS_USERNAME,
			'password' => SMS_PASSWORD
			);

		$response = $soapclient->sendText($params);
	}

    if ($envoi_mail) {
        $resultat = true;
    }
	return $resultat;
}


function envoi_mail_contact($nom, $sujet, $email, $message) {
	
	$resultat = null;

    $to = 'boussole@jeunesse-sports.gouv.fr';
    $subject = mb_encode_mimeheader('Contact via la boussole : '.$sujet);
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: La Boussole des jeunes <noreply@boussole.jeunes.gouv.fr>' . "\r\n" 
		. 'Reply-To: <' .$email. ">\r\n" ;
	$message = $message . "<br/><br/>Adresse mail de l'expéditeur : ". $email ;
    $envoi_mail = mail($to, $subject, $message, $headers);
    if ($envoi_mail) {
        $resultat = true;
    }
	return $resultat;
}