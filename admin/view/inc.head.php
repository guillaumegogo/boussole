<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Boussole des jeunes</title>
<link rel="icon" type="image/png" href="img/compass-icon.png"/>
<link rel="stylesheet" href="css/style_backoffice.css"/>
<link rel="stylesheet" href="css/jquery.dataTables.min.css"/>
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Quicksand" />

<?php
if(isset($_GET['lba'])){
	$_SESSION['admin']['lba'] = ($_GET['lba']=="non") ? "non" : null;
}
if(!(isset($_SESSION['admin']['lba']) && $_SESSION['admin']['lba']=="non")){
?>
<link rel="stylesheet" href="css/style_lba.css"/>
<?php 
} 
?>