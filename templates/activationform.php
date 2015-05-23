<?php

global $language;

?>

<table class="canvascontainer borderlesscells">
	<tr>
		<td class="canvascontainer">
			<div class="standardbox">
				<h1><?php echo $language['userisinactive']; ?></h1>
				<p><?php echo $language['userisinactivepleaseactivate']; ?></p>
				<form method="POST" action="doactivate.php">
					<p><?php echo $language['activationcode'];?>: <input type="text" name="activationcode" value="<?php echo $this->activationcode; ?>" maxlength="<?php echo $config['activationCodeLength']; ?>"><?php echo $this->activationcodeError; ?></p>
					<p><input type="submit" value="<?php echo $language['activate'];?>"></p>
				</form>
			</div>
		</td>
	</tr>
</table>
