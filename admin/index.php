<?php
session_start();
session_unset(); 

require('secret/connect.php');
include('inc/functions.php');

/* gestion de l'authentification (todo) */
//si post du formulaire interne
if (isset($_POST['courriel'])&&isset($_POST['motdepasseactuel'])) {

	$sql = 'SELECT * FROM `bsl_utilisateur` 
		JOIN `bsl__statut` ON `bsl__statut`.`id_statut`=`bsl_utilisateur`.`id_statut`
		WHERE `email` LIKE "'.$_POST['courriel'].'"';
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result)) {
		$row = mysqli_fetch_assoc($result);
		if (password_verify($_POST['motdepasseactuel'], $row['motdepasse'])) {
			//print_r($row);
			//Array ( [id_utilisateur] => 1 [nom_utilisateur] => Guillaume Gogo [email] => guillaume.gogo@jeunesse-sports.gouv.fr [motdepasse] => $2y$10$zcq2/8TBlj4GXqab1f/xSemlE3Q6I31308WZPz9WPKnZAFFv3P9Fi [date_inscription] => 2017-06-23 [id_statut] => 1 [id_metier] => [actif_utilisateur] => 1 [libelle_statut] => administrateur [acces_territoire] => 1 [acces_professionnel] => 1 [acces_offre] => 1 [acces_theme] => 1 [acces_utilisateur] => 1 [acces_demande] => 1 [acces_stats] => 1 [acces_critere] => 1 ) 
			
			$_SESSION['user_id'] = $row['id_utilisateur']; 
			$_SESSION['user_statut'] = $row['libelle_statut']; 
			$_SESSION['user_nom'] = $row['nom_utilisateur']; 
			if($_SESSION['user_statut']==2) $_SESSION['territoire_id'] = $row['id_metier'];
			if($_SESSION['user_statut']==3) $_SESSION['user_pro_id'] = $row['id_metier'];
			$_SESSION['user_droits'] = array('territoire' => $row['acces_territoire'], 'professionnel' => $row['acces_professionnel'], 'offre' => $row['acces_offre'], 'theme' => $row['acces_theme'], 'utilisateur' => $row['acces_utilisateur'], 'demande' => $row['acces_demande'], 'critere' => $row['acces_critere']);
			header('Location: accueil.php');
		}else{
			$msg = 'Le mot de passe indiqué n\'est pas le bon.';
		}
	}else {//mdp actuel correct
		$msg = 'Ce courriel n\'est pas connu. Si le problème persiste contactez votre administrateur.';
	}
}

//view
require 'view/index.tpl.php';