<?php
//********* remplacement des caractères effacés du fichier de l'INSEE
function format_insee($saisie)
{
	$tab_aremplacer = array("-", "_", "  ", "à", "é", "è", "ï", "ö", "ü", "saint ", "Saint ");
	$tab_rempace = array(" ", " ", " ", "a", "e", "e", "i", "o", "st ", "st ");
	return str_replace($tab_aremplacer, $tab_rempace, $saisie);
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
			if (isset($_SESSION['critere'][$ele['name']])) {
				if ($_SESSION['critere'][$ele['name']] == $ele['val']) $t .= ' checked ';
			} else if ($ele['def'] == 1) $t .= ' checked ';
			$t .= '> ' . '<label for="' . $ele['val'] . $ele['name'] .'">' . xssafe($ele['lib']). '</label>' . "\n";
			break;
		case 'checkbox':
			$t = '<input type="checkbox" name="' . $ele['name'] . '[]" value="' . $ele['val'] . '" ' . '" id="' . $ele['val'] . $ele['name'] . '" ';
			if (isset($_SESSION['critere'][$ele['name']])) {
				if (in_array($ele['val'], $_SESSION['critere'][$ele['name']])) $t .= ' checked ';
			} else if ($ele['def'] == 1) $t .= ' checked ';
			$t .= '> ' . '<label for="' . $ele['val'] . $ele['name'] .'">' . xssafe($ele['lib']) . '</label>' . '</br>' . "\n";
			break;
		case 'select':
			$t = '<option value="' . $ele['val'] . '" ';
			if (isset($_SESSION['critere'][$ele['name']])) {
				if ($_SESSION['critere'][$ele['name']] == $ele['val']) $t .= ' selected ';
			} else if ($ele['def'] == 1) $t .= ' selected ';
			$t .= '> ' . xssafe($ele['lib']) . '</option>' . "\n";
			break;
		case 'multiple':
			$t = '<option value="' . $ele['val'] . '" ';
			if (isset($_SESSION['critere'][$ele['name']])) {
				if (in_array($ele['val'], $_SESSION['critere'][$ele['name']])) $t .= ' selected ';
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
	unset($_SESSION['critere'][$ele['name']]);
	
	return $t;
}

function envoi_mails_demande($courriel_offre, $nom_offre, $coordonnees, $token)
{
	$resultat = null;
		
    //au professionnel
    $to = 'boussole@yopmail.fr';
    if (ENVIRONMENT === ENV_PROD || ENVIRONMENT === ENV_BETA) {
        $to = $courriel_offre;
    }
    $subject = mb_encode_mimeheader('Une demande a été déposée sur la Boussole des jeunes');
    $message = "<html><p>Un jeune est intéressé par l'offre <b>" . $nom_offre . "</b>.</p>"
        . "<p>Il a déposé une demande de contact le " . utf8_encode(strftime('%d %B %Y &agrave; %H:%M')) . "</p>"
        . "<p>Son profil est le suivant : " . liste_criteres($_SESSION['critere'], '<br/>') . "</p>"
        . "<p>Les coordonnées indiquées sont les suivantes : <b>" . $coordonnees . "</b></p>"
        . "<p>Merci d'indiquer la suite donnée à la demande dans l'<a href=\"http://" . $_SERVER['SERVER_NAME'] . "/admin/demande_detail.php?hash=".$token."\">espace de gestion de la Boussole</a></p></html>";
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: La Boussole des jeunes <boussole@jeunesse-sports.gouv.fr>' . "\r\n".
		'Reply-To: no-reply@jeunesse-sports.gouv.fr'."\r\n" .
		'X-Mailer: PHP/' . phpversion();
    if (ENVIRONMENT !== ENV_PROD) {
        $headers .= 'Cc: guillaume.gogo@jeunesse-sports.gouv.fr' . "\r\n";
    }

    $envoi_mail = mail($to, $subject, $message, $headers);
	//todo : check http://foundationphp.com/tutorials/email.php 

    //accusé d'envoi au demandeur
    if (filter_var($coordonnees, FILTER_VALIDATE_EMAIL)) {

        $to = $coordonnees;
        $subject = mb_encode_mimeheader('Vous avez déposé une demande de contact sur la Boussole des jeunes');
        $message = "<html><p>Nous vous confirmons qu'un message a été transmis au professionnel avec vos coordonnées et les informations suivantes :</p>"
            . "<p>Offre <b>" . $nom_offre . "</b>.</p>"
            . "<p>Profil : " . liste_criteres($_SESSION['critere'], '<br/>') . "</p></html>";
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: La Boussole des jeunes <boussole@jeunesse-sports.gouv.fr>' . "\r\n".
			'Reply-To: no-reply@jeunesse-sports.gouv.fr'."\r\n" .
			'X-Mailer: PHP/' . phpversion();
        if (ENVIRONMENT !== ENV_PROD) {
            $headers .= 'Cc: guillaume.gogo@jeunesse-sports.gouv.fr' . "\r\n";
        }
        $envoi_accuse = mail($to, $subject, $message, $headers);
    }

    if ($envoi_mail) {
        $resultat = true;
    }
	
	return $resultat;
}


function envoi_mail_contact($nom, $sujet, $email, $message) 
{
	$resultat = null;

    $to = 'boussole@jeunesse-sports.gouv.fr';
    $subject = mb_encode_mimeheader('Demande de contact : '.$sujet);
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: La Boussole des jeunes <boussole@jeunesse-sports.gouv.fr>' . "\r\n".
		'Reply-To: no-reply@jeunesse-sports.gouv.fr'."\r\n" .
		'X-Mailer: PHP/' . phpversion();
    $envoi_mail = mail($to, $subject, $message, $headers);
    if ($envoi_mail) {
        $resultat = true;
    }
	return $resultat;
}