<?php
/**
 * Fonction permettant d'echapper les variables à inclure dans les requetes
 * @param mysqli $conn
 * @param mixed $value
 * @return int|string
 */
function securite_bdd(mysqli $conn, $value){

    // On regarde si le type de string est un nombre entier (int)
    if(ctype_digit($value)) {
        $value = (int) $value;
    }
    // Pour tous les autres types
    else {
        $value = mysqli_real_escape_string($conn, (string) $value);
        // $value = addcslashes($value, '%_'); -> GUILLAUME : fait pêter la requête de get_liste_offres (bcp de noms de critères contiennent un "_", qui du coup est échappé)
    }

    return $value;
}

/**
 * Fonction de gestion des erreurs mysqli
 * @param mysqli $conn
 * @throws Exception
 */
function check_mysql_error(mysqli $conn)
{
    if (mysqli_error($conn))
        throw new Exception('MySQL error : ' . mysqli_error($conn));
}

/**
 * Sécurisation des chaines de caractère pour empêcher les injection xss
 * @param mixed $data
 * @param string $encoding
 * @return string
 */
function xssafe($data, $encoding = 'UTF-8')
{
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
}

/**
 * Print d'une chaine de caractère xss safe
 * @param mixed $data
 */
function xecho($data)
{
    echo xssafe($data);
}

