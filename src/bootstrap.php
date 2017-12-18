<?php
//Ouverture de la session
session_name('sec_boussole_session_id');
session_start();
session_regenerate_id();

//Inclusion communes
include __DIR__.'/variables.php';

//Sur la prod et beta on désactive l'affichage des erreurs, sur les autres environnement on autorise le debug si demandé
if (ENVIRONMENT === ENV_PROD || ENVIRONMENT === ENV_BETA) {
	error_reporting(0);
} else if(DEBUG === true) {
	error_reporting(E_ALL);
}

//Inclusions communes
include __DIR__.'/functions.php';
include __DIR__.'/connect.php';