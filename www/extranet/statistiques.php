<?php

include('../src/admin/bootstrap.php');
$check = secu_check_login(DROIT_DEMANDE);
$perimetre_lecture = $check['lecture'];

$stats = array();
$ecrans = null;

/*
si $perimetre_lecture = PERIMETRE_NATIONAL, alors on fait 3 pages : une par territoire, une par organisme, une par offre
si $perimetre_lecture = PERIMETRE_ZONE, 2 pages : recherche + par organisme, par offre, limité au territoire
si $perimetre_lecture = PERIMETRE_PRO, 2 pages : par organisme, par offre, limité à l'organisme
*/
switch($perimetre_lecture){
	
	case PERIMETRE_NATIONAL :
		$ecrans = array('Par territoire'=>'pt','Par organisme'=>'po','Par offre de service'=>'ps');
		$ecran = (isset($_GET['e']) && in_array($_GET['e'], $ecrans)) ? $_GET['e'] : 'pt'; //on affiche l'écran indiqué si GET valide. sinon par territoire.
		if($ecran == 'pt'){
			$stats[] = ['caption' => 'Nombre de recherches par mois (<a href="recherche_liste.php">détail</a>)', 'data' => get_stat_nb_recherches_par_mois()];
			$stats[] = ['caption' => 'Nombre de demandes déposées par mois', 'data' => get_stat_nb_demandes_par_mois('toutes', 'territoire')];
			$stats[] = ['caption' => 'Nombre de demandes traitées par mois', 'data' => get_stat_nb_demandes_par_mois('traitees', 'territoire')];
		}else if($ecran == 'po'){
			$stats[] = ['caption' => 'Nombre de demandes déposées par mois et par organisme', 'data' => get_stat_nb_demandes_par_mois('toutes', 'pro')];
			$stats[] = ['caption' => 'Nombre de demandes traitées par mois et par organisme', 'data' => get_stat_nb_demandes_par_mois('traitees', 'pro')];
		}else if($ecran == 'ps'){
			$stats[] = ['caption' => 'Nombre de demandes déposées par mois et par offre', 'data' => get_stat_nb_demandes_par_mois('toutes', 'offre')];
			$stats[] = ['caption' => 'Nombre de demandes traitées par mois et par offre', 'data' => get_stat_nb_demandes_par_mois('traitees', 'offre')];
		}
		break;
	
	case PERIMETRE_ZONE :
		if($ecran == 'po'){
			$stats[] = ['caption' => 'Nombre de recherches par mois (<a href="recherche_liste.php">détail</a>)', 'data' => get_stat_nb_recherches_par_mois()];
			$stats[] = ['caption' => 'Nombre de demandes déposées par mois et par organisme', 'data' => get_stat_nb_demandes_par_mois('toutes', 'pro')];
			$stats[] = ['caption' => 'Nombre de demandes traitées par mois et par organisme', 'data' => get_stat_nb_demandes_par_mois('traitees', 'pro')];
		}else if($ecran == 'ps'){
			$stats[] = ['caption' => 'Nombre de demandes déposées par mois et par offre', 'data' => get_stat_nb_demandes_par_mois('toutes', 'offre')];
			$stats[] = ['caption' => 'Nombre de demandes traitées par mois et par offre', 'data' => get_stat_nb_demandes_par_mois('traitees', 'offre')];
		}
		break;
	
	case PERIMETRE_PRO :
		if($ecran == 'po'){
			$stats[] = ['caption' => 'Nombre de demandes déposées par mois et par organisme', 'data' => get_stat_nb_demandes_par_mois('toutes', 'pro')];
			$stats[] = ['caption' => 'Nombre de demandes traitées par mois et par organisme', 'data' => get_stat_nb_demandes_par_mois('traitees', 'pro')];
		}else if($ecran == 'ps'){
			$stats[] = ['caption' => 'Nombre de demandes déposées par mois et par offre', 'data' => get_stat_nb_demandes_par_mois('toutes', 'offre')];
			$stats[] = ['caption' => 'Nombre de demandes traitées par mois et par offre', 'data' => get_stat_nb_demandes_par_mois('traitees', 'offre')];
		}
		break;

}

foreach($stats as &$tableau){
	$tableau['total']=0;
	$i=0;

	foreach ($tableau['data'] as $mois=>$tab) {
		$tableau['entetes'][] = format_mois($mois);
		$tableau['totaux_mois'][format_mois($mois)] = 0;
		
		foreach ($tab as $terr=>$val) {
			$tableau['valeurs'][$terr][format_mois($mois)] = $val;
			
			if(!isset($tableau['totaux_territoriaux'][$terr])) $tableau['totaux_territoriaux'][$terr]=0;
			$tableau['totaux_territoriaux'][$terr] += $val;
			$tableau['totaux_mois'][format_mois($mois)] += $val;
			$tableau['total'] += $val;
		}
		$i++;
	}
	while($i<12){
		$tableau['entetes'][] = '';
		$i++;
	}
	if(isset($tableau['totaux_territoriaux']["Hors territoire"])) { //déplacement des hors-territoire à la fin
		$tableau['totaux_territoriaux']["Hors territoire"] = array_shift($tableau['totaux_territoriaux']); 
	}
}
unset($tableau);

//view
require 'view/statistiques.tpl.php';