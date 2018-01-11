<a href="../web/" target="_blank"><img src="img/external-link.png" class="retour_boussole"></a>

<h1 class="bandeau"><img src="../web/img/marianne.png" width="93px"> Administration de la boussole 
	<?= (ENVIRONMENT === ENV_BETA) ? '<span style="color:red; background:white;">BETA</span>' : '' ?>
	<?= (ENVIRONMENT === ENV_TEST) ? '<span style="color:red; background:white;">TEST</span>' : '' ?></h1>

<div class="statut">
	<?php if( isset($_SESSION['admin']['accroche']) ) { 
		echo $_SESSION['admin']['accroche']; ?> 
	<a href="index.php" style="margin-left:1em">Déconnexion</a>
	<?php } else { // cas de l'acces direct à la demande depuis le mail ?>
	<a href="index.php">Connexion</a>
	<?php } ?>
</div>