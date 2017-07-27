<?php
define('ENV_LOCAL', 'local');
define('ENV_PROD', 'prod');
define('ENVIRONMENT', ENV_LOCAL);

$titredusite = "la boussole des droits";
$version = "Version du 01 août 2017";
$message_erreur_bd = "L'application a rencontré un problème. Ta demande n'a pas pu être enregistrée. Merci de contacter l'administrateur du site si le problème persiste.";
$url_admin = "/admin";

//Sur la prod on désactive l'affichage des erreurs
if (ENVIRONMENT === ENV_PROD)
    error_reporting(0);