<?php
//ce fichier fait la recherche sur la liste des villes, avec remplacement des caractères pour correspondre au fichier de l'insee. 

//récupération de la liste des villes et mise en tableau
$tmp = file_get_contents('src/villes_index.inc');
$villes = str_replace('"', '', explode(',', $tmp));
unset($tmp);

//normalisation du mot saisi
$a_remplacer = array ( 'á', 'â', 'ç', 'é', 'è', 'ê', 'ë', 'î', 'ï', 'ö', 'ô', 'ü', 'û', '-', '\'');
$remplacants = array ( 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u', ' ', ' ');
$regex_a_remplacer = '/^saint/';
$regex_remplacant = 'st';

$term = (isset($_GET['term'])) ? $_GET['term'] : '';
$term = str_replace($a_remplacer, $remplacants, $term);
$term = preg_replace($regex_a_remplacer, $regex_remplacant, $term);

//on récupère les villes correspondantes au mot saisi (soit il commence par la saisie, soit c'est le code postal)
$matches  = preg_grep ('/^('.$term.'.*)|(.* '.$term.'\d*)$/i', $villes);
unset($villes);

//on ne retourne que 10 résultats pour éviter de charger la page d'accueil
echo json_encode(array_slice($matches,0,10)); 