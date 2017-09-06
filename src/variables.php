<?php
define('ENV_LBA', 'lba');
define('ENV_GUILLAUME', 'guillaume');
define('ENV_TEST', 'test');
define('ENV_BETA', 'beta');
define('ENV_PROD', 'prod');
define('ENVIRONMENT', ENV_GUILLAUME);

define('DEBUG', true);

//Définition des variables de connexion en fonction de l'environnement
switch (ENVIRONMENT)
{
	case ENV_LBA :
		define('DB_HOST', 'srv-sql');
		define('DB_USERNAME', 'apache');
		define('DB_PASSWD', 'cpvaprplf');
		define('DB_NAME', 'boussole');
		define('DB_NAME', 'boussole');
		define('DB_PREFIX', '');
		break;
	case ENV_GUILLAUME :
		define('DB_HOST', 'localhost');
		define('DB_USERNAME', 'root');
		define('DB_PASSWD', '');
		define('DB_NAME', 'boussole');
		define('DB_PREFIX', '');
		break;
	case ENV_TEST :
		define('DB_HOST', '???');
		define('DB_USERNAME', '???');
		define('DB_PASSWD', '???');
		define('DB_NAME', '???');
		define('DB_PREFIX', 'test__');
		break;
	case ENV_BETA :
		define('DB_HOST', '???');
		define('DB_USERNAME', '???');
		define('DB_PASSWD', '???');
		define('DB_NAME', '???');
		define('DB_PREFIX', 'beta_');
		break;
	case ENV_PROD :
		define('DB_HOST', '???');
		define('DB_USERNAME', '???');
		define('DB_PASSWD', '???');
		define('DB_NAME', '???');
		define('DB_PREFIX', '');
		break;
}

$titredusite = "la boussole des jeunes";
$version = "Version du 01 août 2017";
$message_erreur_bd = "L'application a rencontré un problème. Ta demande n'a pas pu être enregistrée. Merci de contacter l'administrateur du site si le problème persiste.";