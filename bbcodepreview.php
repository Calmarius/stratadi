<?php

require_once('utils/htmlutils.php');
header('Content-type: text/html; charset=utf-8');

$text=parseBBCode($_GET['text']);

?>

<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" href="main.css">
	</head>
	<body>
		<div>
			<?php echo $text; ?>
		</div>
	</body>
</html>
