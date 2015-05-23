<?php

global $language;
global $config;

/*
      
*/
      
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title><?php echo $language['wtfbattles']; ?></title>
		<link rel="stylesheet" href="main.css">
        <meta name=viewport content="width=device-width, initial-scale=1">
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta property="og:title" content="<?php echo ($this->altTitle!='' ? ($this->altTitle.' - '):'').($language['wtfbattles'].' BETA');?>">
		<meta property="og:type" content="game">
		<meta property="og:image" content="<?php echo $config['facebookImageLink'];?>">
		<meta property="og:url" content="<?php echo $this->altUrl!='' ? $this->altUrl:$config['facebookDefaultUrl']; ?>">
		<meta property="og:site_name" content="Calmarius' website">
		<meta property="og:description" content="<?php echo $this->altDescription!='' ? $this->altDescription:$language['shortdescription']; ?>">
		<meta property="fb:admins" content="100001813890066">
		<noscript>
			<?php
				if (!strstr($_SERVER['SCRIPT_NAME'],'error.php'))
				{
					?>
						<meta http-equiv="refresh" content="2; url=nojs.php">
					<?php
				}
			?>
		</noscript>
	</head>
	<body onload="<?php echo $this->loadScript; ?>">
		<?php echo $this->content; ?>
		<?php
			if (is_array($this->scripts))
			{
				foreach($this->scripts as $key =>$value)
				{
					echo '<script type="text/javascript" src="'.$value.'"></script>';
				}
			}
		?>
	</body>
</html>
