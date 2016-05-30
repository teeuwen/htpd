<!DOCTYPE html>

<?php
	include($_SERVER["DOCUMENT_ROOT"] . "/include/php/include.php");

	verify_login(GID_TEACHER);
?>

<html>
	<head>
		<meta charset="UTF-8">

		<link rel="stylesheet" type="text/css"
				href="/include/lib/bootstrap/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="/include/css/main.css">
		<link rel="stylesheet" type="text/css" href="/include/css/content.css">
		<link rel="stylesheet" type="text/css" href="/include/css/manager.css">

		<script type="text/javascript"
				src="/include/lib/jquery/jquery.js"></script>
		<script type="text/javascript"
				src="/include/lib/bootstrap/bootstrap.js"></script>
		<script type="text/javascript"
				src="/include/lib/tablesorter/tablesorter.js"></script>
		<script type="text/javascript"
				src="/include/js/table.js"></script>

		<script type="text/javascript">
			$(document).ready(function()
			{
				$("[data-tooltip='true']").tooltip({
					container: "body",
					trigger: "hover"
				});

				//$("#grouplist").tablesorter();
				//$("#userlist").tablesorter();
			});
		</script>
	</head>
	<body>
		<div class="wrapper">
			<form method="post" action="groupdel.php">
				<div id="optionbar">
					<div class="btn-group">
						<button
							type="button"
							class="btn btn-default btn-sm"
							title="Toevoegen"
							data-toggle="modal"
							data-tooltip="true"
							data-placement="bottom"
							data-backdrop="static"
							data-target="#created">
							<span class="glyphicon glyphicon-plus"></span>
						</button>
					</div>
					<div class="btn-group">
						<button
							type="button"
							class="btn btn-default btn-sm"
							id="edit"
							title="Wijzigen"
							data-tooltip="true"
							data-placement="bottom" disabled>
							<span class="glyphicon glyphicon-pencil"></span>
						</button>
						<button
							type="submit"
							class="btn btn-default btn-sm"
							id="delete"
							name="delete"
							title="Verwijderen"
							data-tooltip="true"
							data-placement="bottom" disabled>
							<span class="glyphicon glyphicon-trash"></span>
						</button>
					</div>
				</div>
				<div class="datacontainer">
					<table
						class="table table-striped tablesorter"
						id="grouplist">
						<thead>
							<tr>
								<th>
									<input
										type="checkbox"
										id="sall"
										onclick="select_all(this)">
								</th>
								<th>ID</th>
								<th>Naam</th>
								<th>Groep</th>
							</tr>
						</thead>
						<?php
							echo("
								<input
									type='hidden'
									name='pid'
									value='" . $_GET["pid"] . "'>
							");

							$users = array();

							$dbconn = new mysqli(DB_URL . ":" . DB_PORT,
							DB_USER, DB_PASS, DB_NAME);
							check($dbconn, !$dbconn->connect_error, false);

							$qgtable = sprintf("SELECT pid, grp, uid FROM %s
									WHERE pid=%s", DB_GROUPS, $_GET["pid"]);
							$grows = $dbconn->query($qgtable);
							check($dbconn, $grows, false);

							$qutable = sprintf("SELECT uid, gid, name FROM %s",
									DB_USERS);
							$urows = $dbconn->query($qutable);
							check($dbconn, $urows, false);

							while ($grow = $grows->fetch_array())
								$users[$grow["uid"]] = $grow["grp"];

							while ($urow = $urows->fetch_array()) {
								if (array_key_exists($urow["uid"], $users)) {
									echo("<tr>");
									echo("
										<td>
											<input
												type='checkbox'
												class='cb'
												name='cb[]'
												value='" . $urow["uid"] . "'
												onclick='row_set(this)'>
										</td>
									");
									echo("<td>" . $urow["uid"] . "</td>");
									echo("<td>" . $urow["name"] . "</td>");
									echo("<td>");
									if ($users[$urow["uid"]] == -1) {
										if ($urow["gid"] == GID_TEACHER)
											echo(GIDS[1]);
										else //TODO Also if higher than max grps
											echo("Geen");
									} else {
										echo($users[$urow["uid"]] + 1);
									}
									echo("</td>");
									echo("</tr>");
								}
							}

							$grows->close();
							$dbconn->close();
						?>
					</table>
				</div>
			</form>
			<div class="modal" id="created" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button
								type="button"
								class="close"
								data-dismiss="modal">
								&times;
							</button>
							<h4 class="modal-title">Leerlingen toevoegen</h4>
						</div>
						<div class="modal-body">
							<form method="post" action="groupadd.php">
								<table
									class="table table-striped tablesorter"
									id="userlist">
									<thead>
										<tr>
											<th>
												<input
													type="checkbox"
													id="sall1"
													onclick="select_all(this)">
											</th>
											<th>ID</th>
											<th>Naam</th>
										</tr>
									</thead>
									<?php
										$dbconn = new mysqli(DB_URL . ":" .
										DB_PORT, DB_USER, DB_PASS, DB_NAME);
										check($dbconn, !$dbconn->connect_error,
												false);

										$qurows = sprintf(
												"SELECT uid, gid, name FROM %s", 
												DB_USERS);
										$urows = $dbconn->query($qurows);
										check($dbconn, $urows, false);

										while ($urow = $urows->fetch_array()) {
											if ($urow["gid"] == GID_STUDENT) {
												echo("<tr id='u" . $urow["uid"]
														. "'>");
												echo("
													<td>
														<input type='checkbox'
														class='cb' name='cb[]'
														value='" . $urow["uid"]
														. "'
														onclick='row_set(this)'>
													</td>
												");
												echo("<td>" . $urow["uid"] .
														"</td>");
												echo("<td>" . $urow["name"] .
														"</td>");
												echo("</tr>");
											}
										}

										$urows->close();
										$dbconn->close();
									?>
								</table>
								<div class="form-group row">
									<div class="col-sm-offset-4 col-sm-8">
										<input
											type="submit"
											class="btn btn-primary"
											value="Toevoegen">
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
