<?php
//********* remplacement des caractères effacés du fichier de l'INSEE
function format_insee($saisie)
{
    $tab_aremplacer = array("-", "_", "  ", "à", "é", "è", "ï", "ö", "ü", "saint ", "Saint ");
    $tab_rempace = array(" ", " ", " ", "a", "e", "e", "i", "o", "st ", "st ");
    return str_replace($tab_aremplacer, $tab_rempace, $saisie);
}

//********* fonction de présentation (un peu crado) des critères du jeune
function liste_criteres($separateur = ",")
{
    $txt_criteres = '';
    foreach ($_SESSION['critere'] as $index => $valeur) {
        $txt = "";
        if ($valeur) {
            $txt = str_replace("_", " ", $index) . " : ";
            if (is_array($valeur)) {
                foreach ($valeur as $index2 => $valeur2)
                    $txt .= $valeur2 . " /";
                $txt = substr($txt, 0, -1);
            } else {
                $txt .= $valeur;
            }
        }
        $txt_criteres .= xssafe($txt) . $separateur;
    }
    return $txt_criteres;
}

//******** présentation des questions du formulaire
function ouverture_ligne($ele)
{ //affichage ligne préalable si le type le demande
    //Voir si implémentation xssafe possible ou si ça fait sauter les "name"
    $t = '';
    switch ($ele['type']) {
        case 'radio':
        case 'checkbox':
            $t = '<div style="display:inline-table;">';
            break;
        case 'select':
            $t = '<select name="' . $ele['name'] . '">';
            break;
        case 'multiple':
            $t = '<select multiple name="' . $ele['name'] . '[]" size="' . $ele['tai'] . '">';
            break;
    }
    return $t;
}

function affiche_valeur($ele, $type)
{ //affichage valeur
    //Voir si implémentation xssafe possible ou si ça fait sauter les "name"
    $t = '';
    switch ($type) {
        case 'radio':
            $t = '<input type="radio" name="' . $ele['name'] . '" value="' . $ele['val'] . '" ';
            if (isset($_SESSION['critere'][$ele['name']])) {
                if ($_SESSION['critere'][$ele['name']] == $ele['val']) $t .= ' checked ';
            } else if ($ele['def'] == 1) $t .= ' checked ';
            $t .= '> ' . xssafe($ele['lib']) . "\n";
            break;
        case 'checkbox':
            $t = '<input type="checkbox" name="' . $ele['name'] . '[]" value="' . $ele['val'] . '" ';
            if (isset($_SESSION['critere'][$ele['name']])) {
                if (in_array($ele['val'], $_SESSION['critere'][$ele['name']])) $t .= ' checked ';
            } else if ($ele['def'] == 1) $t .= ' checked ';
            $t .= '> ' . xssafe($ele['lib']) . '</br>' . "\n";
            break;
        case 'select':
            $t = '<option value="' . $ele['val'] . '" ';
            if (isset($_SESSION['critere'][$ele['name']])) {
                if ($_SESSION['critere'][$ele['name']] == $ele['val']) $t .= ' selected ';
            } else if ($ele['def'] == 1) $t .= ' selected ';
            $t .= '> ' . xssafe($ele['lib']) . '</option>' . "\n";
            break;
        case 'multiple':
            $t = '<option value="' . $ele['val'] . '" ';
            if (isset($_SESSION['critere'][$ele['name']])) {
                if (in_array($ele['val'], $_SESSION['critere'][$ele['name']])) $t .= ' selected ';
            } else if ($ele['def'] == 1) $t .= ' selected ';
            $t .= '> ' . xssafe($ele['lib']) . '</option>' . "\n";
            break;
    }
    return $t;
}

function cloture_ligne($ele)
{
    $t = '';
    switch ($ele['type']) {
        case 'radio':
        case 'checkbox':
            $t = '</div>' . $t;
            break;
        case 'select':
        case 'multiple':
            $t = '</select>' . $t;
            break;
    }
	
	//une fois le critère affiché (avec la préselection), on vide la session du critere correspondant
	unset($_SESSION['critere'][$ele['name']]);
	
    return $t;
}