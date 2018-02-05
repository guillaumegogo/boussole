<?php

include('../src/admin/bootstrap.php');
if (!secu_check_role(ROLE_ADMIN)) {
	header('Location: accueil.php');
}

$ecran = 'national';
$tableaux_stats = array();
$entetes = array();
$valeurs = array();
$totaux_territoriaux = array();
$totaux_mois = array();
$totaux = array();

// quel tableau de stats
if(isset($_GET['e'])){
	switch ($_GET['e']) {
		case 't':
			$ecran = 'territorial';
			break;
	}
}

if($ecran=='national'){
	
	$tableaux_stats[] = ['Nombre de recherches par mois (<a href="recherche_liste.php">détail</a>)', get_stat_nb_recherches_par_mois()];
	$tableaux_stats[] = ['Nombre de demandes déposées par mois', get_stat_nb_demandes_par_mois('toutes', 'territoire')];
	$tableaux_stats[] = ['Nombre de demandes traitées par mois', get_stat_nb_demandes_par_mois('traitees', 'territoire')];
	
}else if($ecran=='territorial'){
	
	$tableaux_stats[] = ['Nombre de demandes déposées par mois et par organisme', get_stat_nb_demandes_par_mois('toutes', 'pro')];
	$tableaux_stats[] = ['Nombre de demandes traitées par mois et par organisme', get_stat_nb_demandes_par_mois('traitees', 'pro')];
	
}
	
foreach($tableaux_stats as $k=>$tableau){

	$i=0;
	$totaux[$k]=0;
	foreach ($tableau[1] as $mois=>$tab) {
		$entetes[$k][] = format_mois($mois);
		$totaux_mois[$k][format_mois($mois)] = 0;
		
		foreach ($tab as $terr=>$val) {
			$valeurs[$k][$terr][format_mois($mois)] = $val;
			
			if(!isset($totaux_territoriaux[$k][$terr])) $totaux_territoriaux[$k][$terr]=0;
			$totaux_territoriaux[$k][$terr] += $val;
			$totaux_mois[$k][format_mois($mois)] += $val;
			$totaux[$k] += $val;
		}
		$i++;
	}
	while($i<12){
		$entetes[$k][] = '';
		$i++;
	}
	if(isset($totaux_territoriaux[$k]["Hors territoire"])) { //déplacement des hors-territoire à la fin
		$totaux_territoriaux[$k]["Hors territoire"] = array_shift($totaux_territoriaux[$k]); 
	}
}

//view
require 'view/statistiques.tpl.php';