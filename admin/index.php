<?php

include('../src/admin/bootstrap.php');

session_unset();

$msg = '';

//authentification : post du formulaire interne
if (isset($_POST['login']) && isset($_POST['motdepasseactuel'])) {
    if (secu_login($_POST['login'], $_POST['motdepasseactuel']))
        header('Location:accueil.php');
    else
        $msg = 'Erreur lors de l\'identification.';
}

//view
require 'view/index.tpl.php';