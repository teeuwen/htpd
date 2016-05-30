<!DOCTYPE html>

<?php
	include($_SERVER["DOCUMENT_ROOT"] . "/include/php/include.php");

	verify_login(GID_TEACHER);

	if (isset($_POST["path"])) {
		$file = fopen($_POST["path"], "w") or
			die("Er is een fout opgeteden tijdens het opslaan.");
		fwrite($file, $_POST["data"]);
		fclose($file);
	} else {
		$path = URL_USERS . $_GET["uid"] . "/" . $_GET["pid"] . "/";
		$epath = $path . PRJ_FILES[1];
		$vpath = $path . PRJ_FILES[0];
	}
?>

<html>
	<head>
		<meta charset="UTF-8">

		<link rel="stylesheet" href="/include/lib/bootstrap/bootstrap.css">
		<link rel="stylesheet" href="/include/lib/summernote/summernote.css">
		<link rel="stylesheet" href="/include/css/main.css">
		<link rel="stylesheet" href="/include/css/content.css">
		<link rel="stylesheet" href="/include/css/editor.css">

		<script type="text/javascript"
				src="/include/lib/jquery/jquery.js"></script>
		<script type="text/javascript"
				src="/include/lib/bootstrap/bootstrap.js"></script>
		<script type="text/javascript"
				src="/include/lib/summernote/summernote.js"></script>
		<script type="text/javascript"
				src="/include/js/summernote.js"></script>
		<script type="text/javascript"
				src="/include/lib/html-docx-js/html-docx.js"></script>
		<script type="text/javascript"
				src="/include/lib/filesaver/filesaver.js"></script>

		<script type="text/javascript">
			$(document).ready(function() 
			{
				$("[data-tooltip='true']").tooltip({
					container: "body",
					trigger: "hover"
				});

				//if ($("#viewer").html()
				//		.indexOf("<!-- project: finished -->") == -1) //TODO Handle as well
				edit(0, true);
			});

			function save(reopen)
			{
				$("#editor").summernote("destroy");

				var data = new FormData();
				data.append("path",
						<?php echo("\"" . $epath . "\""); ?>);
				data.append("data", $("#editor").html());

				var req = new XMLHttpRequest();
				req.open("post", "teacher.php", true);
				req.send(data);

				if (reopen) edit(0, true);
			}
		</script>
	</head>
	<body>
		<div class="wrapper">
			<?php
				$avail = true;

				if (file_exists($vpath)) {
					$file = fopen($vpath, "r") or
						die("Er is een fout opgetreden!");
					if (filesize($vpath) > 0) {
						echo("
							<div id='optionbaralt'>
								<div class='btn-group'>
									<button
										class='btn btn-default btn-sm'
										title='Downloaden'
										data-tooltip='true'
										data-placement='bottom'
										onclick=\"download(-1, 'viewer')\">
										<span class='glyphicon
												glyphicon-download-alt'>
										</span>
									</button>
								</div>
							</div>
							<div id='viewer'>
						");
						echo(fread($file, filesize($vpath)));
						echo("</div>");

						echo("<div id='editor'>");
						if (file_exists($epath)) {
							$file = fopen($epath, "r") or
								die("Er is een fout opgetreden!");
							if (filesize($epath) > 0)
								echo(fread($file, filesize($epath)));
							else
								echo("<br>");
							fclose($file);
						} else {
							touch($epath);
							echo("<br>");
						}
						echo("</div>");
					} else {
						$avail = false;
					}

					fclose($file);
				} else {
					$avail = false;
				}

				if (!$avail)
					echo("
						<div id='viewer'>
							Reflectie is nog niet beschikbaar.
						</div>
					");
			?>
		</div>
	</body>
</html>
