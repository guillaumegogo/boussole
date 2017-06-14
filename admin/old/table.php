<?php
include('../secret/connect.php');

//********* valeur de sessions
session_start();
$_SESSION['user_id'] = 1;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" type="image/ico" href="http://www.datatables.net/favicon.ico" />
		
		<title>DataTables example</title>
		<!--<style type="text/css" title="currentStyle">
			@import "https://www.datatables.net/release-datatables/media/css/demo_page.css";
			@import "https://www.datatables.net/release-datatables/media/css/demo_table.css";
		</style>-->
		<script type="text/javascript" language="javascript" src="https://www.datatables.net/release-datatables/media/js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="https://www.datatables.net/release-datatables/media/js/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$('#sortable').dataTable();
			} );
		</script>
	</head>
	<body>
		<div>
		
			<div>
<table id="sortable">
	<thead>
		<tr>
			<th>Nom</th>
			<th>Type</th>
			<th>Ville</th>
			<th>Thème(s)</th>
			<th>Compétence géographique</th>
			<th>Actif</th>
		</tr>
	</thead>
	<tbody>
	
<?php
$sql = "SELECT * FROM `bsl_professionnel` WHERE 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        if ($row["actif_pro"]==1) $actif="oui"; else $actif="non";
		echo "<tr><td><a href=\"professionnel_detail.php?id=". $row["id_professionnel"]."\">". $row["nom_pro"]. "</a></td><td>" . $row["type_pro"]. "</td><td>" . $row["ville_pro"]. " (" . $row["code_postal_pro"]. ")</td><td>" . $row["theme_pro"]. "</td><td>" . $row["competence_geo"]. "</td><td>" . $actif . "</td></tr>";
    }
}
?>
	</tbody>
</table>
			</div>
			<div class="spacer"></div>
		</div>
	</body>
</html>