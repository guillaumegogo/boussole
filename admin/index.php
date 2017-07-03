<?php
session_start();
session_unset(); 

require('secret/connect.php');
include('inc/functions.php');

/* gestion de l'authentification (todo) */
//si post du formulaire interne
if (isset($_POST['courriel']&&isset($_POST['motdepasseactuel'])) {

	$sql = "SELECT `id_statut` FROM `bsl_utilisateur` 
		WHERE `email`=".$_POST['courriel']." AND `motdepasse`=\"".password_hash($_POST["motdepasseactuel"], PASSWORD_DEFAULT)."\"";
	$result = mysqli_query($conn, $sql);
	if (!mysqli_num_rows($result)) {
		$msg = 'Le mot de passe indiqué n\'est pas le bon.';
	}else {//mdp actuel correct
		$msg = 'Good !';
}

//view
require 'view/index.tpl.php';