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
		define('DB_PREFIX', 'bsl_');
		break;
	case ENV_GUILLAUME :
		define('DB_HOST', 'localhost');
		define('DB_USERNAME', 'root');
		define('DB_PASSWD', '');
		define('DB_NAME', 'boussole');
		define('DB_PREFIX', '_');
		break;
	case ENV_TEST :
		define('DB_HOST', '???');
		define('DB_USERNAME', '???');
		define('DB_PASSWD', '???');
		define('DB_NAME', '???');
		define('DB_PREFIX', '_test_');
		break;
	case ENV_BETA :
		define('DB_HOST', '???');
		define('DB_USERNAME', '???');
		define('DB_PASSWD', '???');
		define('DB_NAME', '???');
		define('DB_PREFIX', '_beta_');
		break;
	case ENV_PROD :
		define('DB_HOST', '???');
		define('DB_USERNAME', '???');
		define('DB_PASSWD', '???');
		define('DB_NAME', '???');
		define('DB_PREFIX', '');
		break;
}

$path_extranet = "/extranet";
$path_from_extranet_to_web = "..";