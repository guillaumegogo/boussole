<?php
//appel des ressources communes web & admin
include __DIR__.'/../bootstrap.php';

//appel des fonctions spécifiques admin 
include __DIR__.'/modele.php'; // requetes en base
include __DIR__.'/functions.php';

require __DIR__.'/secu.php'; // vérification des droits