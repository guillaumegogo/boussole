<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_THEME);

//********* variable
$msg = "";
$libelle_theme_choisi = "";

$id_theme_choisi = 1;
if (isset($_POST['choix_theme'])) $id_theme_choisi = $_POST['choix_theme'];

if (isset($_POST["maj_id_theme"])) {
    $result = false;

    //********** mise à jour/création du theme
    if (isset($_POST["submit_theme"])) {
        $req = "UPDATE `bsl_theme` SET `libelle_theme`=\"" . $_POST["libelle_theme"] . "\", `actif_theme`=\"" . $_POST["actif"] . "\" WHERE `id_theme`=" . $_POST["maj_id_theme"];
        $result = mysqli_query($conn, $req);
    }
    //********** mise à jour des sous themes
    if (isset($_POST["submit_liste_sous_themes"])) {
        foreach ($_POST['sthemes'] as $selected_option => $foo) {
            foreach ($foo as $selected_option) {
                $rreq = "UPDATE `bsl_theme` SET `libelle_theme`=\"" . $foo[1] . "\", `ordre_theme`=\"" . $foo[2] . "\", `actif_theme`=\"" . $foo[3] . "\" WHERE `id_theme`=" . $foo[0];
                $result = mysqli_query($conn, $rreq);
            }
        }
    }
    //********** mise à jour/création du theme
    if (isset($_POST["submit_nouveau_sous_theme"])) {
        $req = "INSERT INTO `bsl_theme` (`libelle_theme`, `id_theme_pere`, `actif_theme`) VALUES ( \"" . $_POST["libelle_nouveau_sous_theme"] . "\", '" . $_POST["maj_id_theme"] . "', '0')";
        $result = mysqli_query($conn, $req);
    }

    if ($result) {
        $msg = "Modification bien enregistrée.";
    }
}

//********* liste déroulante des thèmes (en haut à droite)
$select_theme = "";
$sql = "SELECT * FROM `bsl_theme` WHERE `id_theme_pere` IS NULL";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($rows = mysqli_fetch_assoc($result)) {
        $select_theme .= "<option value=\"" . $rows['id_theme'] . "\" ";
        if ($rows['id_theme'] == $id_theme_choisi) {
            $select_theme .= "selected";
            $libelle_theme_choisi = $rows['libelle_theme'];
            $actif_theme_choisi = $rows['actif_theme'];
        }
        $select_theme .= ">" . $rows['libelle_theme'] . "</option>";
    }
}

//si theme selectionné
$tableau = "";
$i = 0;
if ($id_theme_choisi) {
    $sql2 = "SELECT * FROM `bsl_theme` 
		WHERE `id_theme_pere`=" . $id_theme_choisi . " 
		ORDER BY actif_theme DESC, ordre_theme";
    $result_st = mysqli_query($conn, $sql2);
} else {
    $msg = "Merci de sélectionner un thème dans la liste.";
}

//view
require 'view/theme.tpl.php';