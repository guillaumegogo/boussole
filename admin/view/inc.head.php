<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Boussole des jeunes</title>
<link rel="icon" type="image/png" href="img/compass-icon.png"/>
<link rel="stylesheet" href="css/style_backoffice.css"/>
<link rel="stylesheet" href="css/jquery.dataTables.min.css"/>

<?php
if((isset($_SESSION['admin']['lba']) && $_SESSION['admin']['lba']=="oui") || (isset($_GET['lba']) && $_GET['lba']=="oui")){
	$_SESSION['admin']['lba'] = "oui";
?>
<link rel="stylesheet" href="css/style_lba.css"/>
<?php } ?>