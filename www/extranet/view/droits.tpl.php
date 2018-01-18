<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head.php'); ?>
	<script type="text/javascript" language="javascript" src="../src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function () {
			$('#sortable').dataTable( {
				paging: false,
				"searching": false
			} );
		});
	</script>
	<style>
	td.style3 {
		background-color: #00AAA9;
		color: white;
	}
	td.style2 {
		background-color: #7fd4d4;
	}
	td.style1 {
		background-color: #cceeed;
	}
	td.style0 {
	}
	</style>
</head>

<body>
<?php include('view/inc.bandeau.php'); ?>

<div class="container">

	<h2><small><a href="accueil.php">Accueil</a> ></small> 
		Gestion des droits</h2>

	<p>Accès aux données en lecture et écriture, par profil et domaine :</p>
	
<?php 
	if (count($droits) > 0) {
?>
		<table id="sortable" class="display compact" style="font-size:80%">
			<thead>
			<tr>
				<th rowspan=2>Profil</th>
				<th colspan=2>Demande</th>
				<th colspan=2>Offre</th>
				<th colspan=2>Mesure</th>
				<th colspan=2>Professionnel</th>
				<th colspan=2>Utilisateur</th>
				<th colspan=2>Formulaire</th>
				<th colspan=2>Theme</th>
				<th colspan=2>Territoire</th>
			</tr>
			<tr>
				<th>Lecture</th>
				<th>Ecriture</th>
				<th>Lecture</th>
				<th>Ecriture</th>
				<th>Lecture</th>
				<th>Ecriture</th>
				<th>Lecture</th>
				<th>Ecriture</th>
				<th>Lecture</th>
				<th>Ecriture</th>
				<th>Lecture</th>
				<th>Ecriture</th>
				<th>Lecture</th>
				<th>Ecriture</th>
				<th>Lecture</th>
				<th>Ecriture</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ($droits as $row) {
			?>
			<tr>
				<th><?= $row['libelle_statut'] ?></th>
				<td class="style<?=$row['demande_r']?>"><?= $traduction[$row['demande_r']] ?></td>
				<td class="style<?=$row['demande_w']?>"><?= $traduction[$row['demande_w']] ?></td>
				<td class="style<?=$row['offre_r']?>"><?= $traduction[$row['offre_r']] ?></td>
				<td class="style<?=$row['offre_w']?>"><?= $traduction[$row['offre_w']] ?></td>
				<td class="style<?=$row['mesure_r']?>"><?= $traduction[$row['mesure_r']] ?></td>
				<td class="style<?=$row['mesure_w']?>"><?= $traduction[$row['mesure_w']] ?></td>
				<td class="style<?=$row['professionnel_r']?>"><?= $traduction[$row['professionnel_r']] ?></td>
				<td class="style<?=$row['professionnel_w']?>"><?= $traduction[$row['professionnel_w']] ?></td>
				<td class="style<?=$row['utilisateur_r']?>"><?= $traduction[$row['utilisateur_r']] ?></td>
				<td class="style<?=$row['utilisateur_w']?>"><?= $traduction[$row['utilisateur_w']] ?></td>
				<td class="style<?=$row['formulaire_r']?>"><?= $traduction[$row['formulaire_r']] ?></td>
				<td class="style<?=$row['formulaire_w']?>"><?= $traduction[$row['formulaire_w']] ?></td>
				<td class="style<?=$row['theme_r']?>"><?= $traduction[$row['theme_r']] ?></td>
				<td class="style<?=$row['theme_w']?>"><?= $traduction[$row['theme_w']] ?></td>
				<td class="style<?=$row['territoire_r']?>"><?= $traduction[$row['territoire_r']] ?></td>
				<td class="style<?=$row['territoire_w']?>"><?= $traduction[$row['territoire_w']] ?></td>
			</tr>
			<?php
			}
			?>
			</tbody>
		</table>
<?php 
	}
	/*  [id_statut] => 1
            [libelle_statut] => administrateur
            [demande_r] => 3
            [demande_w] => 3
            [offre_r] => 3
            [offre_w] => 3
            [mesure_r] => 3
            [mesure_w] => 3
            [professionnel_r] => 3
            [professionnel_w] => 3
            [utilisateur_r] => 3
            [utilisateur_w] => 3
            [formulaire_r] => 3
            [formulaire_w] => 3
            [theme_r] => 3
            [theme_w] => 3
            [territoire_r] => 3
            [territoire_w] => 3*/
?>

</div>
</body>
</html>