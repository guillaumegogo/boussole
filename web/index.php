<?php
session_start();

include('secret/connect.php');
include('inc/functions.php');
include('inc/variables.php');

//********* censé permettre de revenir sur les formulaires sans recharger
header('Cache-Control: no cache'); 
session_cache_limiter('private_no_expire');

//********* variables utilisées dans ce fichier
$liste_villes_possibles = null;
$nb_villes = 0;
$themes = array();

//********* l'utilisateur a relancé le formulaire
if (isset($_POST['ville_selectionnee'])) {
	//********* on efface les valeurs de session pour une recherche propre
	session_unset();  

	//********* requête des codes insee (avec concat des codes postaux) et droits liés à la ville 
	//test si saisie avec le autocomplete (auquel cas ça se termine par des chiffres)
	if(is_numeric(substr($_POST['ville_selectionnee'], -3))){
		$ville = substr($_POST['ville_selectionnee'], 0, -6);
		$cp = substr($_POST['ville_selectionnee'], -5);
		$sql = "SELECT `nom_ville`, `code_insee`, GROUP_CONCAT(`code_postal` SEPARATOR ', ') as `codes_postaux` 
			FROM `bsl__ville` 
			WHERE `nom_ville` LIKE ? AND `code_postal` LIKE ?
			GROUP BY `nom_ville`, `code_insee`";
		$stmt = mysqli_prepare($conn, $sql);
		mysqli_stmt_bind_param($stmt, 'ss', $ville, $cp);
	}else{
		$ville = format_insee($_POST['ville_selectionnee']).'%'; //conversion des accents, etc.
		$cp='';
		$sql = "SELECT `nom_ville`, `code_insee`, GROUP_CONCAT(`code_postal` SEPARATOR ', ') as `codes_postaux` 
			FROM `bsl__ville` 
			WHERE `nom_ville` LIKE ?
			GROUP BY `nom_ville`, `code_insee`";
		$stmt = mysqli_prepare($conn, $sql);
		mysqli_stmt_bind_param($stmt, 's', $ville);
	}

	if (mysqli_stmt_execute($stmt)) {
		mysqli_stmt_store_result($stmt);
		$nb_villes=mysqli_stmt_num_rows($stmt);

		if($nb_villes==0){
			$message = "Nous ne trouvons pas de ville correspondante. Recommence s'il te plait.";
		}else if($nb_villes>1){
			$message = "Plusieurs villes correspondent à ta saisie. Recommence s'il te plait.";
		}else {
			mysqli_stmt_bind_result($stmt, $row['nom_ville'], $row['code_insee'], $row['codes_postaux']);
			mysqli_stmt_fetch($stmt); 
			$_SESSION['ville_habitee'] = $row['nom_ville'];
			$_SESSION['code_insee'] = $row['code_insee'];
			if ($cp) {
				$_SESSION['code_postal'] = $cp;
			}else{
				$_SESSION['code_postal'] = $row['codes_postaux'];
			}

			//********* affichage des thèmes disponibles en fonction de la ville choisie 
			$sqlt = "SELECT DISTINCT `bsl_theme`.id_theme, `bsl_theme`.`libelle_theme`, `bsl_theme`.`actif_theme`
				FROM `bsl_theme`
				JOIN `bsl_professionnel_themes` ON `bsl_professionnel_themes`.id_theme=`bsl_theme`.id_theme
				JOIN `bsl_professionnel` ON `bsl_professionnel`.id_professionnel=`bsl_professionnel_themes`.id_professionnel
				LEFT JOIN `bsl_territoire` ON `bsl_professionnel`.competence_geo=\"territoire\" AND `bsl_territoire`.`id_territoire`=`bsl_professionnel`.`id_competence_geo` 
				LEFT JOIN `bsl_territoire_villes` ON `bsl_territoire_villes`.`id_territoire`=`bsl_territoire`.`id_territoire`
				LEFT JOIN `bsl__departement` ON `bsl_professionnel`.competence_geo=\"departemental\" AND `bsl__departement`.`id_departement`=`bsl_professionnel`.`id_competence_geo`
				LEFT JOIN `bsl__region` ON `bsl_professionnel`.competence_geo=\"regional\" AND `bsl__region`.`id_region`=`bsl_professionnel`.`id_competence_geo`
				LEFT JOIN `bsl__departement` as `bsl__departement_region` ON `bsl__departement_region`.`id_region`=`bsl__region`.`id_region`
				WHERE `bsl_theme`.actif_theme=1 AND `bsl_professionnel`.actif_pro=1 AND (`bsl_professionnel`.competence_geo=\"national\" OR code_insee=? OR `bsl__departement`.id_departement=SUBSTR(?,1,2) OR `bsl__departement_region`.id_departement=SUBSTR(?,1,2))
				UNION
				SELECT DISTINCT `bsl_theme`.id_theme, `bsl_theme`.`libelle_theme`, `bsl_theme`.`actif_theme`
				FROM `bsl_theme`
				WHERE `id_theme_pere` IS NULL AND `actif_theme`=0";
//todo : la requête fait la vérification des thèmes des pros autorisés à travailler sur une zone géographique englobant la zone indiquée : pays, région, département ou territoire. il faudra probablement descendre au niveau des offres pour une meilleure granularité. 
/* on pourrait descendre à la granularité de l'offre, mais la requête serait encore plus complexe :
(...) JOIN `bsl_offre` ON `bsl_offre`.id_professionnel=`bsl_professionnel`.id_professionnel
JOIN `bsl_theme` as theme_offre ON bsl_offre.id_sous_theme=theme_offre.id_theme
WHERE actif_offre=1 AND debut_offre <= CURDATE() AND fin_offre >= CURDATE() (...)*/

			$stmtt = mysqli_prepare($conn, $sqlt);
			mysqli_stmt_bind_param($stmtt, 'sss', $_SESSION['code_insee'], $_SESSION['code_insee'], $_SESSION['code_insee']);
			if (mysqli_stmt_execute($stmtt)) {
				mysqli_stmt_bind_result($stmtt, $id_theme, $libelle_theme, $actif_theme);

				while (mysqli_stmt_fetch($stmtt)) {
					$themes[] = array('libelle'=>$libelle_theme, 'actif'=>$actif_theme);
				}
			}
			mysqli_stmt_close($stmtt);
		}
	}
	mysqli_stmt_close($stmt);
}

//view
require 'view/index.tpl.php';
