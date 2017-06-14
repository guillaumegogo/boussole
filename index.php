<?php
/************************ todo
- pour l'instant les thèmes ne sont pas gérés en base, seul l'emploi est activé (cf. requête mysql)
****************************************/

include('secret/connect.php');
include('inc/functions.php');

$version="Version DEV du 9 juin 2017";

//********* permet de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire');

//********* valeur de sessions
session_start();

//********* variables
$liste_villes_possibles = null;
$nb_villes = 0;

//********* l'utilisateur a relancé le formulaire
if (isset($_POST["ville_selectionnee"])) {
	//********* on efface les valeurs de session pour une recherche sans effet de bord
	session_unset();  
	
	//********* requête des codes insee (avec concat des codes postaux) et droits liés à la ville 
	//$ville = format_insee(securite_bdd($conn, $_POST["ville_selectionnee"]));
	$ville = securite_bdd($conn, substr($_POST["ville_selectionnee"], 0, -6));
	$cp = securite_bdd($conn, substr($_POST["ville_selectionnee"], -5));
	//'emploi,logement' as `themes` pour ouvrir le thème logement
	$sql = "SELECT `nom_ville`, `code_insee`, GROUP_CONCAT(`code_postal` SEPARATOR ', ') as `codes_postaux`, 'emploi' as `themes` FROM `bsl__ville` 
		WHERE nom_ville LIKE '".$ville."%' AND code_postal LIKE '".$cp."' 
		GROUP BY `nom_ville`, `code_insee`, `themes`";
	
	$result = mysqli_query($conn, $sql);
	$nb_villes=mysqli_num_rows($result);
	
	//********* si une seule ville, tout va bien
	if ($nb_villes==1){
		$row = mysqli_fetch_assoc($result);
		$_SESSION['ville_habitee'] = $row['nom_ville'];
		$_SESSION['code_insee'] = $row['code_insee'];
		$_SESSION['code_postal'] = $cp;
		$tab_themes = explode(",", $row['themes']);

	//********* sinon, pas bien
	}else{
		$message = "Nous ne trouvons pas de ville correspondante. Recommence s'il te plait.";
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
    <link rel="icon" type="image/png" href="img/compass-icon.png" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
	<script type="text/javascript" language="javascript" src="js/jquery-1.12.0.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery-ui-1.12.0.js"></script>
	  <script>
  $( function() {
    var listeVilles = [<?php include('inc/villes_index.inc');?>];
    $( "#villes" ).autocomplete({
      minLength: 2,
      source: function( request, response ) {
          //adaptation fichier insee
          request.term = request.term.replace("-", " ");
          request.term = request.term.replace(/^saint /gi, "St ");
          //recherche sur les premiers caractères de la ville ou sur le code postal
          var matcher1 = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
          var matcher2 = new RegExp( " " + $.ui.autocomplete.escapeRegex( request.term ) + "[0-9]*$", "i" );
          response( $.grep( listeVilles, function( item ){
			  return (matcher1.test( item ) || matcher2.test( item ));
          }) );
      }
    });
  } );
  </script>
	<title>Boussole des jeunes</title>
</head>

<body><div id="main">
<div class="bandeau"><a href="index.php">La boussole des jeunes</a></div>
<div class="version"><?php echo $version; ?></div> 
<div class="soustitre">Rencontrer des professionnel·le·s <b>près de chez moi</b> qui <b>m'aident</b> dans mes recherches.</div>

<?php
//********* 1er affichage de la page (ou mauvaise saise)
if ($nb_villes!=1) {
?>

<form class="joli accueil vert" method="post">
<fieldset class="accueil_choix_ville">
<?php
if (isset($message)) { echo "<p class=\"message\">".$message."</p>"; }
?>
	<label for="ville_selectionnee">J'habite à</label>
	<input type="text" id="villes" name="ville_selectionnee" onchange="this.form.submit()">
	<input type="submit" value="Valider">
</fieldset>
&nbsp;
</form>

<?php
//********* si une seule ville correspond
}else{
?>

<form action="formulaire.php" class="joli accueil vert" method="post">
<fieldset class="accueil_choix_besoin">
    <div>J'habite à <b><?php echo $_SESSION['ville_habitee']; ?> (<?php echo $_SESSION['code_postal']; ?>)</b> et je souhaite... </div>
    <div class="boutonsbesoin">
        <input type="submit" value="Trouver un emploi" name="besoin" <?php if(!in_array("emploi", $tab_themes)){ echo "disabled"; } ?> >
        <input type="submit" value="Me loger" name="besoin" <?php if(!in_array("logement", $tab_themes)){ echo "disabled"; } ?> >
        <input type="submit" value="Me soigner" name="besoin" <?php if(!in_array("soin", $tab_themes)){ echo "disabled"; } ?> >
        <!--
		<input type="submit" value="Me former" name="besoin" disabled>
        <input type="submit" value="Me rendre utile" name="besoin" disabled>
		-->
    </div>
</fieldset>
</form>
<?php
}
?>

<div class="div123">
	<div class="block123"><img src="img/ci_search.png">1. En 5 minutes je trouve le bon <!--contact--> professionnel.</div>
	<div class="block123"><img src="img/message.png">2. Je suis recontacté·e <!--par un professionnel--> dans les jours qui suivent.</div>
	<div class="block123"><img src="img/calendar.png">3. J'obtiens un rendez-vous et une réponse à mon besoin.</div>
</div>

<br/>
<br/>
<!--
<?php print_r($_SESSION); echo "<br/>".$sql; ?>
-->

<?php include('inc/footer.inc'); ?>
</div>
</body>
</html>