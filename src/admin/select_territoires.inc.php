<?php
$select_territoire = "";
$nom_territoire_choisi = "";
if (secu_check_role(ROLE_ADMIN) || secu_check_role(ROLE_ANIMATEUR)) {

    $sql = "SELECT `id_territoire`, `nom_territoire`, `code_territoire` FROM `bsl_territoire` WHERE 1 ";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $select_territoire = "<label for=\"choix_territoire\">Territoire :</label>
		<select name=\"choix_territoire\" onchange=\"this.form.submit()\" ";
        if (!secu_check_role(ROLE_ADMIN)) {
            $select_territoire .= " disabled";
        }
        $select_territoire .= "><option value=\"0\" >National</option>";

        while ($row = mysqli_fetch_assoc($result)) {
            $select_territoire .= "<option value=\"" . $row['id_territoire'] . "\" ";
            if (isset($_SESSION['territoire_id'])) {
                if ($row['id_territoire'] == $_SESSION['territoire_id']) {
                    $select_territoire .= "selected";
                    $nom_territoire_choisi = $row['nom_territoire'];
                }
            }
            $select_territoire .= ">" . $row['nom_territoire'] . "</option>";
        }
        $select_territoire .= "\r\n</select>\r\n";
    }
    $select_territoire = "<form method=\"post\" class=\"liste_territoire\">" . $select_territoire . "</form>";
}