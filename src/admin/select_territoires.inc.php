<?php
$select_territoire = "";
$nom_territoire_choisi = "";
if (secu_check_role(ROLE_ADMIN) || secu_check_role(ROLE_ANIMATEUR)) {

    $result = get_territoires();

    if (count($result) > 0) {
        $select_territoire = '<label for="choix_territoire">Territoire :</label>
		<select name="choix_territoire" onchange="this.form.submit()" ';
        if (!secu_check_role(ROLE_ADMIN)) {
            $select_territoire .= ' disabled';
        }
        $select_territoire .= '><option value="0" >National</option>';

        foreach ($result as $row) {
            $select_territoire .= '<option value="' . $row['id_territoire'] . '" ';
            if (isset($_SESSION['territoire_id'])) {
                if ($row['id_territoire'] == $_SESSION['territoire_id']) {
                    $select_territoire .= 'selected';
                    $nom_territoire_choisi = $row['nom_territoire'];
                }
            }
            $select_territoire .= ">" . $row['nom_territoire'] . "</option>\r\n";
        }
        $select_territoire .= '</select>';
    }
    $select_territoire = '<form method="post" class="liste_territoire">' . $select_territoire . '</form>';
}