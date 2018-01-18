<?php
//appel des ressources communes web & admin
include __DIR__.'/../bootstrap.php';

//appel des fonctions spécifiques web
include __DIR__.'/modele.php'; // requetes en base
include __DIR__.'/functions.php';

//Gestion du cache (permet de revenir sur les formulaires sans recharger)
header('Cache-Control: no cache');
session_cache_limiter('private_no_expire');