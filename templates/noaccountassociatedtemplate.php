<?php

global $language;

?>

<table class="canvascontainer borderlesscells">
	<tr>
		<td class="canvascontainer">
			<div class="standardbox information">
				<h1><?php echo xprintf($language['youraccountunderdeletion'],array($this->deleteTime)); ?></h1>
				<p><a href="docanceldeletion.php"><?php echo $language['wouldyouliketocanceldeletion']; ?></a></p>
				<p><a href="doreset.php"><?php echo $language['logout']; ?></a></p>
			</div>
		</td>
	</tr>
</table>
