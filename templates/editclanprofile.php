<?php

global $language;
global $config;

?>

<table class="canvascontainer">
	<tr>
		<td class="canvascontainer">
			<div class="standardbox">
				<h1><?php echo $language['editguildprofile'];?></h1>
				<form method="POST" action="doeditguildprofile.php">
					<p>
						<textarea cols="40" rows="20" name="guildprofile"><?php echo $this->profile; ?></textarea>
					</p>
					<p><input type="submit" value="<?php echo $language['saveedits']; ?>"></p>
				</form>
			</div>
		</td>
	</tr>
</table>
