<?php
//ce fichier fait la recherche sur la liste des villes, avec remplacement des caractères pour correspondre au fichier de l'insee. 
//il ne retourne que 10 résultats pour éviter de faire planter la page d'accueil.

//liste villes
$tmp = file_get_contents('src/villes_index.inc');
$villes = str_replace('"', '', explode(',', $tmp));
unset($tmp);

//normalisation
$a_remplacer = array ( 'á', 'â', 'ç', 'é', 'è', 'ê', 'ë', 'î', 'ï', 'ö', 'ô', 'ü', 'û', '-', '\'');
$remplacants = array ( 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u', ' ', ' ');
$regex_a_remplacer = '/^saint/';
$regex_remplacant = 'st';

$term = (isset($_GET['term'])) ? $_GET['term'] : '';
$term = str_replace($a_remplacer, $remplacants, $term);
$term = preg_replace($regex_a_remplacer, $regex_remplacant, $term);

$matches  = preg_grep ('/^('.$term.'.*)|(.* '.$term.')$/i', $villes);
unset($villes);

echo json_encode(array_slice($matches,0,10)); 