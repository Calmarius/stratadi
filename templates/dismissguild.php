<?php

global $language;

?>

<table class="canvascontainer">
	<tr>
		<td class="canvascontainer">
			<div class="standardbox">
				<h1><?php echo $language['dismissguild'];?></h1>
				<form action="dodismissguild.php" method="POST">
					<p><?php echo $language['usepasswordtodismiss']; ?></p>
					<p><?php echo $language['password']; ?> <input type="password" name="password"></p>
					<p><input type="submit" value="<?php echo $language['dismissguild']; ?>"></p>
				</form>
			</div>
		</td>
	</tr>
</table>
