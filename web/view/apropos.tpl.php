<!DOCTYPE html>
<html>
<head>
	<?php include('../src/web/head-min.inc.php'); ?>
</head>
<body>
<div class="apropos">
	<?php include('../src/web/header.inc.php'); ?>

	<div class="container" style="padding:1em 0;">
	
		<div class="row">
			<div class="col-md-3"></div>
			<div class="col-md-6">
			<?= (isset($msg)) ? $msg : '' ?>
			</div>
		</div>
	
		<?php if($contenu) include($contenu); ?>
	</div>
</div>

	<?php include('../src/web/footer.inc.php'); ?>
</body>
</html>

