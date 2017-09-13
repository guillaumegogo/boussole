<?php
/**
 * Fonction permettant d'echapper les variables à inclure dans les requetes
 * @param mysqli $conn
 * @param mixed $value
 * @return int|string
 */
function securite_bdd(mysqli $conn, $value){

	// On regarde si le type de string est un nombre entier (int)
	if(ctype_digit($value)) {
		$value = (int) $value;
	}
	// Pour tous les autres types
	else {
		$value = mysqli_real_escape_string($conn, (string) $value);
		// $value = addcslashes($value, '%_'); // note de Guillaume : j'ai mis en commentaire car cette ligne fait pêter la requête de get_liste_offres (bcp de noms de critères contiennent des "_", qui du coup sont échappés)
	}

	return $value;
}

/**
 * Fonction de gestion des erreurs mysqli
 * @param mysqli $conn
 * @throws Exception
 */
function check_mysql_error(mysqli $conn)
{
	if (mysqli_error($conn))
		throw new Exception('MySQL error : ' . mysqli_error($conn));
}

/**
 * Sécurisation des chaines de caractère pour empêcher les injection xss
 * @param mixed $data
 * @param string $encoding
 * @return string
 */
function xssafe($data, $encoding = 'UTF-8')
{
	return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
}

/**
 * Print d'une chaine de caractère xss safe
 * @param mixed $data
 */
function xecho($data)
{
	echo xssafe($data);
}


/**
 * préparation d'une requête préparée
 * @return stmt
 */
function query_prepare($query,$terms,$types){
	
	global $conn;
	$stmt = mysqli_prepare($conn, $query);
	if(count($terms) > 0) {
		$query_params = [];
		$query_params[] = $types;
		foreach ($terms as $id => $term) {
			$query_params[] = &$terms[$id];
		}
		call_user_func_array(array($stmt, 'bind_param'), $query_params);
	}
	return $stmt;
}

/**
 * execution d'une requête préparée
 * @return int
 */
function query_do($stmt){
	
	global $conn;
	$affected = null;

	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$affected = mysqli_stmt_affected_rows($stmt) > 0;
		mysqli_stmt_close($stmt);
	}
	return $affected;
}

/**
 * execution d'une requête préparée
 * @return array
 */
function query_get($stmt){
	
	global $conn;
	$rows = [];
	check_mysql_error($conn);
	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}
		mysqli_stmt_close($stmt);
	}
	return $rows;
}