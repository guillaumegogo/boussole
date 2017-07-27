<?php
include('../src/admin/bootstrap.php');
secu_check_login(DROIT_THEME);

$req = "SELECT * FROM `bsl_offre_criteres` WHERE `nom_critere` LIKE 'age_%' ORDER BY id_offre, nom_critere DESC";
$result = mysqli_query($conn, $req);
$min = 0;
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['nom_critere'] == "age_min") {
        $min = $row['valeur_critere'];
    } else {
        for ($i = $min; $i <= $row['valeur_critere']; $i++) {
            echo "INSERT INTO `bsl_offre_criteres`(`id_offre`, `nom_critere`, `valeur_critere`) VALUES (" . $row['id_offre'] . ", 'age'," . $i . ");<br/>";
        }
    }
}
// et après un beau "DELETE FROM `bsl_offre_criteres` WHERE `nom_critere` LIKE 'age_%'"