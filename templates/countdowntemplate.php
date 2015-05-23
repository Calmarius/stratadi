<?php

global $language;
global $config;
?>

<table class="canvascontainer borderlesscells">
	<tr>
		<td class="canvascontainer">
			<div class="standardbox information">
				<h3><?php echo $language['notstartedyet'] ?></h3>
				<h1><span class="countdown"><?php echo $this->time; ?></span></h1>
				<p><a href="javascript:history.back()"><?php echo $language['gobackprevious']?></a></p>
			</div>
		</td>
	</tr>
</table>
