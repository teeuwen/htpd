<?php
	include($_SERVER["DOCUMENT_ROOT"] . "/include/php/include.php");

	if (!verify_login(USER_ADMIN))
		header("Location: /user/logout.php");

	echo("Verbinding maken met SQL database... ");
	$dbconn = new mysqli(DB_URL . ":" . DB_PORT, DB_USER, DB_PASS, DB_NAME);
	check($dbconn, !$dbconn->connect_error);

	$uids = $_POST["cb"];

	for ($i = 0; $i < count($uids); $i++) {
		echo("Gebruikers informatie wordt opgehaald... ");
		$columns = "SELECT gid FROM " . DB_USERS . " WHERE uid = '" .
				$uids[$i] . "'";
		$result = $dbconn->query($columns);
		check($dbconn, $result->num_rows);
		$row = $result->fetch_assoc();

		echo("Gebruiker wordt verwijderd... ");
		$user =
			"DELETE FROM " . DB_USERS . " WHERE uid='" . $uids[$i] . "'";
		check($dbconn, $dbconn->query($user));

		if ($row["gid"] == 2) {
			echo("Persoonlijke bestanden worden verwijderd... ");
			$path = URL_USERS . $uids[$i];
			deldir($path);
			check($dbconn, !file_exists($path));
		}

		$result->free();
	}

	$dbconn->close();

	header("Location: " . $_SERVER["HTTP_REFERER"]);

	function deldir($j) {
		foreach(scandir($j) as $k) {
			if ("." === $k || ".." === $k)
				continue;
			elseif (is_dir("$j/$k"))
				deldir("$j/$k");
			else
				unlink("$j/$k");
		}

		rmdir($j);
	}
?>