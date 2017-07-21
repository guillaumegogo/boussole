<footer>
	<div class="version"><?= $version ?></div>
	<ul>
		<?php if ($ENVIRONNEMENT=="LOCAL") {?><li><a href="<?= $url_admin ?>" target="_blank">Administration</a> <?php }?>
		<li><a href="mentions.php">Mentions légales</a> 
		<li><a href="#">Contact</a> 
		<li>Un service proposé par le <a href="http://jeunes.gouv.fr" target="_blank">Ministère chargé de la Jeunesse</a>.
	</ul>
</footer>