<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head-min.php'); ?>
	<link rel="stylesheet" href="src/js/jquery-ui.min.css">
	<script type="text/javascript" language="javascript" src="src/js/external/jquery/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="src/js/jquery-ui.min.js"></script>
	<script type="text/javascript" language="javascript" src="src/js/bootstrap.min.js"></script>
	<script>$( function() {
			$( "#villes" ).autocomplete({
				minLength: 2,
				source: 'ville.php',
				select: function(event, ui) {
					$("#villes").val(ui.item.label);
					$("#searchForm").submit();
				}
			});
<?php if ($_SESSION['web']['redir_prod']) { ?>
			$( "#myModal").modal('show');
<?php } ?>
		} );
	</script>
<?php if ($_SESSION['web']['redir_prod']) { ?>
	<style>
	.modal-backdrop.in { opacity: 0.8; }
	</style>
	<meta http-equiv="Refresh" content="10;url=https://boussole.jeunes.gouv.fr">
<?php } ?>
</head>
<body>
	
<?php if ($_SESSION['web']['redir_prod']) { ?>
<!-- Modal jeunes.gouv -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
        <h4 class="modal-title">La <b>Boussole des Jeunes</b> est en ligne depuis le 31 janvier !</h4>
      </div>
      <div class="modal-body">
        <p>L'adresse du service est dorénavant <b><a href="https://boussole.jeunes.gouv.fr">boussole.jeunes.gouv.fr</a></b>. Pense à mettre à jour tes favoris ! &#128521;</p>
		<p>Tu vas être redirigé(e) dans un instant (sinon, <a href="https://boussole.jeunes.gouv.fr">clique ici</a>).</p> 
		<p>A très bientôt</p>
		<p>L'équipe de la Boussole des Jeunes</p>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<div id="main">
	<header id="bandeau-home-page">
		<div class="wrapper">
			<div class="wrapper-bandeau-homepage">

				<img src="img/marianne.png" alt="Ministère de l'éducation nationale" class="logo-ministere-homepage">
				<img src="img/logo-jeunesse.png" alt="DJEPVA" class="logo-jeunesse-homepage">
				<a  href="index.php"><img src="img/logo-boussole.svg" alt="logo la boussole des jeunes" class="logo-boussole-homepage"></a>

<?php if (isset($message_haut)){ ?>
				<div class="row">
					<p class="message_haut"><?= $message_haut ?></p>
	<?php if(isset($liste_crij)){ ?>
					<p> 
		<?php foreach($liste_crij as $crij){ ?>
			<b><?= ($crij['url']) ? $crij['url'] : '' ?><?= $crij['nom_pro'] ?><?= ($crij['url']) ? '</a>' : '' ?></b>, <?= $crij['adresse_pro'].' '.$crij['code_postal_pro'].' '.$crij['ville_pro'] ?><?= ($crij['visibilite_coordonnees']) ? ', '.$crij['telephone_pro'] : '' ?><br/>
		<?php } ?>
					</p>
	<?php } ?>
				</div>
<?php } ?>

				<div class="container bonhomme-section-header">
					<div class="row">
						<div class="col-md-4 col-sm-4 col-xs-4">
							<img src="img/bonhomme1.svg" alt="">
						</div>

						<div class="col-md-4 col-sm-4 col-xs-4">
							<img src="img/bonhomme2.svg" alt="">
						</div>

						<div class="col-md-4 col-sm-4 col-xs-4">
							<img src="img/bonhomme3.svg" alt="">
						</div>
					</div>
				</div>

			</div>
		</div>
	</header>
		<div class="wrapper container accueil-recherche">
			<div class="row">
				<form action="jesouhaite.php" class="joli accueil" method="post" id="searchForm">
					<div class="col-md-3 col-sm-3 col-xs-12">
						<div class="wrapper-jhabite">
							<img src="img/localisation-gris.svg" alt="">
							<label for="ville_selectionnee">J'habite à</label>
						</div>						
					</div>
					<div class="col-md-6 col-sm-5 col-xs-12">
						<div class="wrapper-input-ville">
							<input type="text" id="villes" name="ville_selectionnee" class="input-villes" placeholder="ville ou code postal">
						</div>
                        <?php if (isset($message_bas)) { ?>
                            <p class="message"><?= $message_bas ?> </p>
                        <?php } ?>
					</div>
					<div class="col-md-3 col-sm-4 col-xs-12">
						<div class="wrapper-submit-ville">
							<input type="submit" value="Rechercher" class="submit-ville">
						</div>
					</div>
					&nbsp;
				</form>
			</div>
		</div>

	<div class=" wrapper soustitre">
		<h1>Rencontre un professionnel près de chez toi, pour trouver un emploi, un métier, une formation, un logement...</h1>
	</div>
	
	<div class="wrapper div123">
		<div class="container">
			<div class="row">
				<div class="col-md-4 col-sm-4 col-xs-12 block123 ">
					<img src="img/icon-clock.svg">
					<p>En 5 minutes je trouve le bon professionnel.</p>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12 block123 ">
					<img src="img/icon-contact.svg">
					<p>Je suis contacté(e) dans les jours qui suivent.</p>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12 block123 ">
					<img src="img/icon-calendar.svg">
					<p>J'obtiens une réponse à ma demande et un rendez vous si nécessaire.</p>
				</div>
			</div>
		</div>
	</div>
	<?php include('view/inc.footer.php'); ?>
</div>
</body>
</html>