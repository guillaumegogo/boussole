<?php
include('secret/connect.php');
include('inc/functions.php');

//********* permet de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire'); 

//********* valeur de sessions
session_start();

$soustheme_encours = "";
$affichage="";
$aucune_offre="";

//************ si accès direct à la page, renvoi vers l'accueil
if (!isset($_SESSION['ville_habitee']) || !isset($_SESSION['besoin'])) {
	header('Location: index.php');
} else {    
	$message = "J'habite à <b>".$_SESSION['ville_habitee']."</b> et je souhaite <b>".strtolower ($_SESSION['besoin'])."</b>.";
}

//***************** liste des critères (valeurs de session affichées à l'écran)
$txt_criteres=null;
foreach($_SESSION as $index=>$valeur){
	$tab_criteres_a_afficher = array("ville_habitee", "besoin", "age", "sexe", "nationalite", "jesais", "situation", "etudes", "diplome", "permis", "handicap", "type_emploi", "temps_plein", "secteur", "experience", "inscription"); 
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
//todo zone_selection_villes & villes...
$sql = "SELECT t.*, `bsl_theme`.libelle_theme AS `sous_theme_offre`, `theme_pere`.libelle_theme AS `theme_offre` 
	FROM ( SELECT `bsl_offre`.*,   /*********** on remonte la liste des critères */
		GROUP_CONCAT( if(nom_critere= 'age_min', valeur_critere, NULL ) separator '|') age_min, 
		GROUP_CONCAT( if(nom_critere= 'age_max', valeur_critere, NULL ) separator '|') age_max, 
		GROUP_CONCAT( if(nom_critere= 'villes', valeur_critere, NULL ) separator '|') villes, 
		GROUP_CONCAT( if(nom_critere= 'sexe', valeur_critere, NULL ) separator '|') sexe, 
		GROUP_CONCAT( if(nom_critere= 'jesais', valeur_critere, NULL ) separator '|') jesais, 
		GROUP_CONCAT( if(nom_critere= 'situation', valeur_critere, NULL ) separator '|') situation, 
		GROUP_CONCAT( if(nom_critere= 'nationalite', valeur_critere, NULL ) separator '|') nationalite, 
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
JOIN `bsl_theme` ON `bsl_theme`.id_theme=`t`.id_sous_theme
JOIN `bsl_theme` AS `theme_pere` ON `theme_pere`.id_theme=`bsl_theme`.id_theme_pere
JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`t`.id_professionnel /* s'il n'y a pas une liste de villes propre à l'offre (zone_selection_villes=0), alors il faut aller chercher celles du pro, d'où les jointures en dessous ↓ */
LEFT JOIN `bsl_territoire` ON t.zone_selection_villes=0 AND `bsl_professionnel`.competence_geo='territoire' AND `bsl_territoire`.`id_territoire`=`bsl_professionnel`.`id_competence_geo` 
LEFT JOIN `bsl_territoire_villes` ON `bsl_territoire_villes`.`id_territoire`=`bsl_territoire`.`id_territoire`
LEFT JOIN `bsl__departement` ON `bsl_professionnel`.competence_geo='departemental' AND `bsl__departement`.`id_departement`=`bsl_professionnel`.`id_competence_geo`
LEFT JOIN `bsl__region` ON `bsl_professionnel`.competence_geo='regional' AND `bsl__region`.`id_region`=`bsl_professionnel`.`id_competence_geo`
LEFT JOIN `bsl__departement` as `bsl__departement_region` ON `bsl__departement_region`.`id_region`=`bsl__region`.`id_region`
	
WHERE t.debut_offre <= CURDATE() AND t.fin_offre >= CURDATE() 
AND `bsl_professionnel`.actif_pro=1
AND `theme_pere`.libelle_theme='".$_SESSION['besoin']."' 
AND ((t.zone_selection_villes=1 AND t.villes LIKE '%".$_SESSION["code_insee"]."%') /* soit il y a une liste de villes au niveau de l'offre */
	OR (t.zone_selection_villes=0 AND ( /* sinon il faut chercher dans la zone de compétence du pro */
		`bsl_professionnel`.competence_geo='national'
		OR `bsl_territoire_villes`.code_insee='".$_SESSION['code_insee']."'
		OR `bsl__departement`.id_departement=SUBSTR('".$_SESSION['code_insee']."',1,2) 
		OR `bsl__departement_region`.id_departement=SUBSTR('".$_SESSION['code_insee']."',1,2)
	)))
AND t.age_min <= ".$_SESSION["age"]." AND t.age_max >= ".$_SESSION["age"]."
AND t.situation LIKE '%".$_SESSION["situation"]."%' AND t.etudes LIKE '%".$_SESSION["etudes"]."%' AND t.diplome LIKE '%".$_SESSION["diplome"]."%'  AND t.temps_plein LIKE '%".$_SESSION["temps_plein"]."%'
"; 
if (isset($_SESSION["sexe"])) $sql .= " AND t.sexe LIKE '%".$_SESSION["sexe"]."%'";
if (isset($_SESSION["jesais"])) $sql .= " AND t.jesais LIKE '%".$_SESSION["jesais"]."%'";
if (isset($_SESSION["nationalite"])) $sql .= " AND t.nationalite LIKE '%".$_SESSION["nationalite"]."%'";
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
$sql .= " ORDER BY `bsl_theme`.ordre_theme";

$result = mysqli_query($conn, $sql);
$nb_offres = mysqli_num_rows($result);
if ($nb_offres>1) {
	$msg=$nb_offres." offres correspondent à ta recherche.";
}else if ($nb_offres==1) {
	$msg="Une offre correspond à ta recherche.";
}else{
	$msg="Aucune offre ne correspond à ta recherche.";
}

if ($nb_offres > 0) {
	while($row = mysqli_fetch_assoc($result)) {        

		//*********** séparation par sous thèmes
		if ($row["sous_theme_offre"]!=$soustheme_encours){
			if ($soustheme_encours) $affichage .= "</fieldset>"; /*tweak */
			$soustheme_encours=$row["sous_theme_offre"];            
			$affichage .= "<fieldset class=\"resultat\"><legend>".$soustheme_encours."</legend>\n<div style=\"width:100%; margin:auto;\" />";     
		}

		//*********** affichage des offres 
		/*$affichage .= "<div class=\"resultat_offre\"><div class=\"coeur\">&#9825;</div><b><a href=\"offre.php?id=".$row["id_offre"]."\">".$row["nom_offre"]."</a></b><br/><small>";
		$affichage .= (strlen($row["description_offre"]) > 50 ) ? substr($row["description_offre"],0,strpos($row["description_offre"]," ",50))."..." : $row["description_offre"] ; 
		$affichage .= "</small></div>";*/
		
		$affichage .= "<div class=\"resultat_offre\"><div class=\"coeur\">&#9825;</div><a href=\"offre.php?id=".$row["id_offre"]."\"><b>".$row["nom_offre"]."</b><br/><small>";
		$affichage .= (strlen($row["description_offre"]) > 50 ) ? substr($row["description_offre"],0,strpos($row["description_offre"]," ",50))."..." : $row["description_offre"] ; 
		$affichage .= "</small></a></div>";
	}
	$affichage .= "</fieldset>";
}

if ($nb_offres > 0) {
	$titre_criteres = "<p onclick='masqueCriteres()'>".$message."<span id=\"fleche_criteres\">&#9661;</span></p>";
	$aucune_offre = "<a href=\"#\">Aucune offre ne m'intéresse</a>";
}else{
	$titre_criteres = "<p>".$message."</p>";
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
		<?php echo $titre_criteres; ?>
		<div id="criteres" style="display:<?php echo ($nb_offres) ? "none":"block"; ?>">
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
	echo $affichage;
?>
	</fieldset>
</form>

<div class="lienenbas">
<?php
	echo $aucune_offre;
?>
</div>

<div style="height:2em;">&nbsp;</div>  <!--tweak css-->

<!--
<?php echo $sql."<br/>"; print_r($_POST); echo "<br/>"; print_r($_SESSION); ?>
-->

<?php include('inc/footer.inc.php'); ?>
</div>
</body>
</html>