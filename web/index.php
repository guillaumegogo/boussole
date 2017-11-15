<?php

include('../src/web/bootstrap.php');

if(isset($_SESSION['erreur'])) {
    if ($_SESSION['erreur'] == 1) {
        $message = "Nous ne trouvons pas de ville correspondante. Recommence s'il te plaît.";
    } else if ($_SESSION['erreur'] == 2) {
        $message = "Plusieurs villes correspondent à ta saisie. Recommence s'il te plaît.";
    }
    unset($_SESSION['erreur']);
}

//view
require 'view/index.tpl.php';