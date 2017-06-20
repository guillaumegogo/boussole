<?php
require('secret/connect.php');
include('inc/functions.php');
session_start();

//********* verif des droits
if (!isset($_SESSION['user_id'])) header('Location: index.php');

//********* variables
$id_demande = null;
$msg = "";

if(isset($_POST["id_traite"])) {
    $sql2 = "UPDATE `bsl_demande` SET `date_traitement` = NOW(), `commentaire` = \"".securite_bdd($conn, $_POST["commentaire"])."\", `user_derniere_modif`=\"".$_SESSION["user_id"]."\" 
	WHERE `bsl_demande`.`id_demande` =".$_POST["id_traite"];
	$result = mysqli_query($conn, $sql2);
    if ($result) {
        $msg = "La demande a été mise à jour.";
    }
	$msg = "<div class=\"soustitre\">".$msg."</div>";
}

if(isset($_GET["id"])) {
	$id_demande=$_GET["id"];
    $sql = "SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `profil`, `commentaire`, bsl_offre.nom_offre, bsl_professionnel.nom_pro   
    FROM `bsl_demande` JOIN bsl_offre ON bsl_offre.id_offre=bsl_demande.id_offre JOIN bsl_professionnel ON bsl_offre.id_professionnel=bsl_professionnel.id_professionnel  
	WHERE id_demande=".$id_demande;
	$result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style_backoffice.css" />
    <link rel="icon" type="image/png" href="img/compass-icon.png" />
	<title>Boussole des jeunes</title>
</head>

<body>
<h1 class="bandeau"><a href="accueil.php">Administration de la boussole des jeunes</a></h1>
<div class="statut"><?php echo $_SESSION['accroche']; ?> (<a href="index.php">déconnexion</a>)</div> 

<div class="container">
<h2>Détail d'une demande</h2>
<?php echo $msg; ?>

<?php
if (mysqli_num_rows($result) > 0) {
?>

<table>
<tr>
	<th>N° demande</th><td><?php echo $row["id_demande"]; ?></td>
</tr>
<tr>
	<th>Date demande</th><td><?php echo date_format(date_create($row["date_demande"]), 'd-m-Y à H\hi'); ?></td>
</tr>
<tr>
	<th>Coordonnées du demandeur</th><td><?php 
    if (filter_var($row["contact_jeune"], FILTER_VALIDATE_EMAIL)) {
        echo "<a href=\"mailto:\"". $row["contact_jeune"].">". $row["contact_jeune"]."</a>"; 
    } else {
        echo $row["contact_jeune"]; 
    } 
?></td>
</tr>
<tr>
	<th>Offre de service</th><td><?php echo $row["nom_offre"]; ?></td>
</tr>
<tr>
	<th>Professionnel</th><td><?php echo $row["nom_pro"]; ?></td>
</tr>
<tr>
	<th>Profil</th><td><?php echo str_replace(",", "<br/>",$row["profil"]); ?></td>
</tr>
<tr>
	<th>Traité</th><td>
<?php
    if ($row["date_traitement"]) {
        $traite="Traité le ".date_format(date_create($row["date_traitement"]), 'd-m-Y à H\hi')."<br/>Commentaire : ".$row["commentaire"];
    } else {
        $traite="<form method=\"post\" class=\"detail\"><input type=\"hidden\" name=\"id_traite\" value=\"".$id_demande."\" /><textarea name=\"commentaire\"   style=\"width:100%\"  rows=\"5\" placeholder=\"Conditions et suites données à l'échange (...)\"></textarea> <input type=\"submit\" value=\"Marquer comme traité\"></form> ";  
    }
    echo $traite;
?>
</tr>
</table>

<?php
} else {
    echo "N° de demande non valide.";
}
?>

<div class="button"><a href="demande_liste.php">Retour à la liste des demandes</a></div>

</div>
<?php 
if ($ENVIRONNEMENT=="LOCAL") {
	echo "<pre>"; echo @$sql."<br/>".@$sql2."<br/>"; print_r($_POST); echo "</pre>"; 
}
?>
</body>
</html>