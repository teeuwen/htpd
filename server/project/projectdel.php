<?php
	include($_SERVER["DOCUMENT_ROOT"] . "/include/php/include.php");

	verify_login(GID_TEACHER, isset($_POST["submit"]));

	echo("Verbinding maken met SQL database... ");
	$dbconn = new mysqli(DB_URL . ":" . DB_PORT, DB_USER, DB_PASS, DB_NAME);
	check($dbconn, !$dbconn->connect_error);

	$pids = $_POST["pids"];

	for ($i = 0; $i < count($pids); $i++) {
		$pid = $dbconn->real_escape_string($pids[$i]);
		echo("Project wordt verwijderd... ");
		$project =
			sprintf("DELETE FROM %s WHERE pid=%s", DB_PROJECTS, $pid);
		check($dbconn, $dbconn->query($project));

		//TODO Option to delete files as well, maybe only as admin?

		echo("Groepen worden verwijderd... ");
		$groups =
			sprintf("DELETE FROM %s WHERE pid=%s", DB_GROUPS, $pid);
		check($dbconn, $dbconn->query($groups));
	}

	$dbconn->close();

	header("Location: " . $_SERVER["HTTP_REFERER"]);
?>
