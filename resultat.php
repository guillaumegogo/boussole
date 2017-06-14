<?php
/************************ todo 24/5/2017 : 
- remonter besoin/thème/sousthème en BD
- trier les offres par proximité géographique (nécessaite d'importer latitude/longitude en BD)
****************************************/

include('secret/connect.php');
include('inc/functions.php');

//********* permet de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire'); 

//********* valeur de sessions
session_start();
if (isset($_POST["temps_plein"])) { $_SESSION['temps_plein'] = securite_bdd($conn, $_POST["temps_plein"]); }
if (isset($_POST["experience"])) { $_SESSION['experience'] = securite_bdd($conn, $_POST["experience"]); }
//pas de securite_bdd() pour ces 3 là car ce sont des tableaux - on passe la fonction plus bas
if (isset($_POST["secteur"])) { $_SESSION['secteur'] = $_POST["secteur"]; }
if (isset($_POST["type_emploi"])) { $_SESSION['type_emploi'] = $_POST["type_emploi"]; }
if (isset($_POST["inscription"])) { $_SESSION['inscription'] = $_POST["inscription"]; }

//********* conversion besoin/thème
$theme = null;
$tab_besoins = ["Trouver un emploi" => "emploi"];
if (isset($tab_besoins[$_SESSION["besoin"]])) $theme = $tab_besoins[$_SESSION["besoin"]];

$soustheme=null;
$soustheme_tab = ["techniques" => "Rendre ma recherche d'emploi plus efficace par la maitrise des techniques", "information" => "Être informé sur les salons, forums, évènements et actualités utiles à ma recherche d'emploi"];

//************ message
if (!isset($_SESSION['ville_habitee'])) {
    $message = "J'habite je ne sais où et je ne sais quoi. <a href=\"index.php\">Recommence</a>.";
    
} else {    
    if (!isset($_SESSION['besoin'])) {
        $message = "J'habite à ".$_SESSION['ville_habitee']." et je sais pas trop où j'ai cliqué.";
     
    } else {    
        $message = "J'habite à <b>".$_SESSION['ville_habitee']."</b> et je souhaite <b>".strtolower ($_SESSION['besoin'])."</b>.";
    }
}

//***************** liste des critères
$txt_criteres=null;
foreach($_SESSION as $index=>$valeur){
	$tab_criteres_a_afficher = array("ville_habitee", "besoin", "age", "sexe", "europeen", "jesais", "situation", "etudes", "diplome", "permis", "handicap", "type_emploi", "temps_plein", "secteur", "experience", "inscription");
	
	if(in_array($index, $tab_criteres_a_afficher)){
		$txt = str_replace("_", " ", $index)." : ";
		if(is_array($valeur)){
			foreach($valeur as $index2=>$valeur2)
				$txt .= $valeur2." /";
			$txt = substr($txt, 0, -1);
		}else{
			$txt .= $valeur;
		}
		$txt_criteres .= $txt.'<br/>';
	}
} 

//************ construction de LA requête
$sql = "SELECT * FROM (
	SELECT `bsl_offre`.*,
		GROUP_CONCAT( if(nom_critere= 'age_min', valeur_critere, NULL ) separator '|') age_min, 
		GROUP_CONCAT( if(nom_critere= 'age_max', valeur_critere, NULL ) separator '|') age_max, 
		GROUP_CONCAT( if(nom_critere= 'villes', valeur_critere, NULL ) separator '|') villes, 
		GROUP_CONCAT( if(nom_critere= 'jesais', valeur_critere, NULL ) separator '|') jesais, 
		GROUP_CONCAT( if(nom_critere= 'situation', valeur_critere, NULL ) separator '|') situation, 
		GROUP_CONCAT( if(nom_critere= 'europeen', valeur_critere, NULL ) separator '|') europeen, 
		GROUP_CONCAT( if(nom_critere= 'permis', valeur_critere, NULL ) separator '|') permis, 
		GROUP_CONCAT( if(nom_critere= 'handicap', valeur_critere, NULL ) separator '|') handicap, 
		GROUP_CONCAT( if(nom_critere= 'experience', valeur_critere, NULL ) separator '|') experience, 
		GROUP_CONCAT( if(nom_critere= 'type_emploi', valeur_critere, NULL ) separator '|') type_emploi, 
		GROUP_CONCAT( if(nom_critere= 'temps_plein', valeur_critere, NULL ) separator '|') temps_plein, 
		GROUP_CONCAT( if(nom_critere= 'inscription', valeur_critere, NULL ) separator '|') inscription, 
		GROUP_CONCAT( if(nom_critere= 'etudes', valeur_critere, NULL ) separator '|') etudes, 
		GROUP_CONCAT( if(nom_critere= 'diplome', valeur_critere, NULL ) separator '|') diplome, 
		GROUP_CONCAT( if(nom_critere= 'secteur', valeur_critere, NULL ) separator '|') secteur
	FROM bsl_offre_criteres 
	JOIN `bsl_offre` ON bsl_offre.id_offre=bsl_offre_criteres.id_offre
	WHERE `bsl_offre`.actif_offre=1 
	GROUP BY bsl_offre_criteres.id_offre
) as t
WHERE t.debut_offre <= CURDATE() AND t.fin_offre >= CURDATE() AND t.theme_offre='".$theme."' AND (t.zone_selection_villes='0' OR t.villes LIKE '%".$_SESSION["code_insee"]."%') AND t.age_min <= ".$_SESSION["age"]." AND t.age_max >= ".$_SESSION["age"]."
AND t.situation LIKE '%".$_SESSION["situation"]."%' AND t.etudes LIKE '%".$_SESSION["etudes"]."%' AND t.diplome LIKE '%".$_SESSION["diplome"]."%'  AND t.temps_plein LIKE '%".$_SESSION["temps_plein"]."%'
"; //todo zone_selection_villes & villes...
if (isset($_SESSION["jesais"])) $sql .= " AND t.jesais LIKE '%".$_SESSION["jesais"]."%'";
if (isset($_SESSION["europeen"])) $sql .= " AND t.europeen LIKE '%".$_SESSION["europeen"]."%'";
if (isset($_SESSION["handicap"])) $sql .= " AND t.handicap LIKE '%".$_SESSION["handicap"]."%'";
if (isset($_SESSION["permis"])) $sql .= " AND t.permis LIKE '%".$_SESSION["permis"]."%'";
if (isset($_SESSION["experience"])) $sql .= " AND t.experience LIKE '%".$_SESSION["experience"]."%'";
$boutdesql = "";
if (isset($_SESSION['secteur'])){
	foreach ($_SESSION['secteur'] as $selected_option) {
		$boutdesql .= " t.secteur LIKE '%".securite_bdd($conn, $selected_option)."%' OR";
	}
	$sql .= " AND (". $boutdesql. " FALSE)";
}
$boutdesql = "";
if (isset($_SESSION['type_emploi'])){
	foreach ($_SESSION['type_emploi'] as $selected_option) {
		$boutdesql .= " t.type_emploi LIKE '%".securite_bdd($conn, $selected_option)."%' OR";
	}
	$sql .= " AND (". $boutdesql. " FALSE)";
}
$boutdesql = "";
if (isset($_SESSION['inscription'])){
	foreach ($_SESSION['inscription'] as $selected_option) {
		$boutdesql .= " t.inscription LIKE '%".securite_bdd($conn, $selected_option)."%' OR";
	}
	$sql .= " AND (". $boutdesql. " FALSE)";
}
$sql .= " ORDER BY sous_theme_offre";

$result = mysqli_query($conn, $sql);
$nb_offres = mysqli_num_rows($result);
if ($nb_offres>1) {
	$msg=$nb_offres." offres correspondent à ta recherche.";
}else if ($nb_offres==1) {
	$msg="Une offre correspond à ta recherche.";
}else{
	$msg="Aucune offre ne correspond à ta recherche.";
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title>Boussole des jeunes</title>
	<script>
function masqueCriteres(){
	var x = document.getElementById('criteres');
    var y = document.getElementById('fleche_criteres');
	
	if(x.style.display === 'none') {
		x.style.display = 'block';
		y.innerHTML = "&#9651;"; 
	} else {
		x.style.display = 'none';
		y.innerHTML = "&#9661;"; 
	}
}
	</script>
</head>

<body><div id="main">
<div class="bandeau"><a href="index.php">La boussole des jeunes</a></div>
<div class="soustitre" style="margin-top:3%"><?php echo $msg; ?></div>

<form class="joli resultat">
<fieldset class="resultat">
	<legend>Rappel de mes informations</legend>
	<div>
		<p onclick='masqueCriteres()'><?php echo $message; ?> <span id="fleche_criteres">&#9661;</span></p>
		<div id="criteres" style="display:none;">
			<div class="colonnes">
				<?php echo $txt_criteres; ?>  <abbr title="A mettre en forme...">&#9888;</abbr>
			</div>
			<div class="enbasadroite">
				<a href="javascript:location.href='formulaire.php'">Revenir au formulaire</a>
			</div>
		</div>
	</div>
</fieldset>
</form>

<form class="joli resultat" style="margin-top:1%;">
<?php
if ($nb_offres > 0) {
	$affichage="";
    while($row = mysqli_fetch_assoc($result)) {        
        
//*********** séparation par sous thèmes
        if ($row["sous_theme_offre"]!=$soustheme){
            if ($soustheme) $affichage .= "</fieldset>"; /*tweak */
			$soustheme=$row["sous_theme_offre"];
            $tmp = $soustheme;
            if (isset($soustheme_tab[$soustheme]))
                $tmp = $soustheme_tab[$soustheme];
            
            $affichage .= "<fieldset class=\"resultat\"><legend>".$tmp."</legend>
            <div style=\"width:100%; margin:auto;\" />";     
        }

//*********** affichage des offres 
        $affichage .= "<div class=\"resultat_offre\"><div class=\"coeur\">&#9825;</div><b><a href=\"offre.php?id=".$row["id_offre"]."\">".$row["nom_offre"]."</a></b><br/><small>";

        if(strlen($row["description_offre"]) > 50 ){
			$affichage .= substr($row["description_offre"],0,strpos($row["description_offre"]," ",50))."...";
		}else {
			$affichage .= $row["description_offre"];
		}
        $affichage .= "</small></div>";

    }
	$affichage .= "</fieldset>";
	echo $affichage;
}
?>
	</fieldset>
</form>

<div class="lienenbas">
	<a href="#">Aucune offre ne m'intéresse</a>
</div>

<div style="height:2em;">&nbsp;</div>  <!--tweak css-->

<!--
<?php echo $sql."<br/>"; print_r($_POST); echo "<br/>"; print_r($_SESSION); ?>
-->

<?php include('inc/footer.inc'); ?>
</div>
</body>
</html>