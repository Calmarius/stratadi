<?php

global $language;
global $config;
?>

<table class="canvascontainer borderlesscells">
	<tr>
		<td class="canvascontainer">
			<div class="standardbox error">
				<h1><?php echo $language['sorrybuterrorhappened'] ?></h1>
				<p><?php echo $this->errormsg; ?></p>
				<p><a href="javascript:history.back()"><?php echo $language['gobackprevious']?></a></p>
			</div>
		</td>
	</tr>
</table>
