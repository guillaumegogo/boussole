<?php
define('ENV_LBA', 'lba');
define('ENV_GUILLAUME', 'guillaume');
define('ENV_PROD', 'prod');
define('ENVIRONMENT', ENV_LBA);

define('DEBUG', true);

//Définition des variables de connexion en fonction de l'environnement
switch (ENVIRONMENT)
{
    case ENV_LBA :
        define('DB_HOST', 'srv-sql');
        define('DB_USERNAME', 'apache');
        define('DB_PASSWD', 'cpvaprplf');
        define('DB_NAME', 'boussole');
        break;
    case ENV_GUILLAUME :
        define('DB_HOST', 'localhost');
        define('DB_USERNAME', 'root');
        define('DB_PASSWD', '');
        define('DB_NAME', 'boussole');
        break;
    case ENV_PROD :
        define('DB_HOST', '???');
        define('DB_USERNAME', '???');
        define('DB_PASSWD', '???');
        define('DB_NAME', '???');
        break;
}

$titredusite = "la boussole des droits";
$version = "Version du 01 août 2017";
$message_erreur_bd = "L'application a rencontré un problème. Ta demande n'a pas pu être enregistrée. Merci de contacter l'administrateur du site si le problème persiste.";
$url_admin = "/admin";