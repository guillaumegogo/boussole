<?php

include('../src/admin/bootstrap.php');
secu_check_login(DROIT_UTILISATEUR);

//********* territoire sélectionné
if (isset($_POST['choix_territoire'])) {
    $_SESSION['territoire_id'] = securite_bdd($conn, $_POST['choix_territoire']);
}
include('../src/admin/select_territoires.inc.php');

//********page actif ou désactivés ?
$flag_actif = (isset($_GET['actif']) && $_GET['actif'] == 'non') ? 0 : 1;

//********* affichage liste résultats 
//tous les utilisateurs actifs, du territoire le cas échéant
$sql = 'SELECT `id_utilisateur`, `bsl_utilisateur`.`id_statut`, `nom_utilisateur`, `email`, `libelle_statut`, `nom_pro`, `nom_territoire` '
    . ' FROM `bsl_utilisateur` '
    . ' JOIN `bsl__statut` ON `bsl__statut`.`id_statut`=`bsl_utilisateur`.`id_statut` '
    . ' LEFT JOIN `bsl_territoire` ON `bsl_territoire`.`id_territoire`=`bsl_utilisateur`.`id_metier` '
    . ' LEFT JOIN `bsl_professionnel` ON `bsl_professionnel`.`id_professionnel`=`bsl_utilisateur`.`id_metier` '
    . ' WHERE `actif_utilisateur`=' . $flag_actif;
if ($_SESSION['territoire_id']) {
    $sql .= ' AND (`bsl_utilisateur`.`id_statut`=2 AND `id_metier`= ' . $_SESSION['territoire_id'] . ') '
        . ' OR (`bsl_utilisateur`.`id_statut`=3 AND `bsl_professionnel`.`competence_geo`="territoire" AND `id_competence_geo`=' . $_SESSION['territoire_id'] . ')';
}
$sql .= ' ORDER BY `bsl_utilisateur`.`id_statut` ASC,`id_metier` ASC';
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $tableau = '<table id=\'sortable\'><thead> <tr><th>Nom</th><th>Courriel</th><th>Statut</th><th>Attache</th></tr></thead> <tbody> ';

    while ($row = mysqli_fetch_assoc($result)) {
        //attache = colonne 'compétence géographique'
        $attache = '';
        switch ($row['id_statut']) {
            case '2':
                $attache = $row['nom_territoire'];
                break;
            case '3':
                $attache = $row['nom_pro'];
                break;
        }
        $tableau .= '<tr><td><a href=\'utilisateur_detail.php?id=' . $row['id_utilisateur'] . '\'>' . $row['nom_utilisateur'] . '</a></td><td>' . $row['email'] . '</td><td>' . $row['libelle_statut'] . '</td><td>' . $attache . '</td></tr>';
    }
    $tableau .= '</tbody></table>';

} else {
    $tableau = '<div style=\'margin:1em;text-align:center\'>Aucun résultat</div>';
}

//********** lien actifs/désactivés
$lien_desactives = ($flag_actif) ? '<a href=\'utilisateur_liste.php?actif=non\'>Liste des utilisateurs désactivés</a>' : '<a href=\'utilisateur_liste.php\'>Liste des utilisateurs actifs</a>';

//view
require 'view/utilisateur_liste.tpl.php';