<?php
//Ouverture de la session
session_start();

//Inclusions PHP
include('../src/variables.php');
include('../src/connect.php');
include('../src/web/modele.php');
include('../src/web/functions.php');

//Gestion du cache (censé permettre de revenir sur les formulaires sans recharger)
header('Cache-Control: no cache');
session_cache_limiter('private_no_expire');