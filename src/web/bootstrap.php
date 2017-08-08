<?php
include __DIR__.'/../bootstrap.php';
include __DIR__.'/modele.php';
include __DIR__.'/functions.php';

//Gestion du cache (censé permettre de revenir sur les formulaires sans recharger)
header('Cache-Control: no cache');
session_cache_limiter('private_no_expire');