<?php

global $language;

/*print_r($_SESSION);
echo $this->name;
echo $this->mail;
echo $language['successfulregistrationdescription'];*/
?>

<table class="canvascontainer">
	<tr>
		<td class="canvascontainer">
			<div class="standardbox success">
				<h1><?php echo $language['successfulregistration']?></h1>
				<p><?php echo xprintf($language['successfulregistrationdescription'],array($this->name,$this->mail)); ?></p>
				<p><a href="doreset.php"><?php echo $language['backtomainpage'];?></a></p>
			</div>
		</td>
	</tr>
</table>
