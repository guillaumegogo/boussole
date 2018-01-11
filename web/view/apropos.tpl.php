<!DOCTYPE html>
<html>
<head>
	<?php include('view/inc.head-min.php'); ?>
</head>
<body>

<div class="apropos">
	<?php include('view/inc.header.php'); ?>

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

<?php include('view/inc.footer.php'); ?>

</body>
</html>

