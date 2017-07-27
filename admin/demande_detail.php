<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_DEMANDE);

//********* variables
$id_demande = null;
$msg = "";

if (isset($_POST["id_traite"])) {
    $sql2 = "UPDATE `bsl_demande` SET `date_traitement` = NOW(), `commentaire` = \"" . securite_bdd($conn, $_POST["commentaire"]) . "\", `user_derniere_modif`=\"" . $_SESSION["user_id"] . "\" 
	WHERE `bsl_demande`.`id_demande` =" . $_POST["id_traite"];
    $result = mysqli_query($conn, $sql2);
    if ($result) {
        $msg = "La demande a été mise à jour.";
    }
    $msg = "<div class=\"soustitre\">" . $msg . "</div>";
}

if (isset($_GET["id"])) {
    $id_demande = $_GET["id"];
    $sql = "SELECT `id_demande`, `date_demande`, `date_traitement`, `contact_jeune`, `profil`, `commentaire`, bsl_offre.nom_offre, bsl_professionnel.nom_pro   
    FROM `bsl_demande` JOIN bsl_offre ON bsl_offre.id_offre=bsl_demande.id_offre JOIN bsl_professionnel ON bsl_offre.id_professionnel=bsl_professionnel.id_professionnel  
	WHERE id_demande=" . $id_demande;
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
}

//view
require 'view/demande_detail.tpl.php';