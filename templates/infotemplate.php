<?php

global $language;
?>

<table class="canvascontainer">
	<tr>
		<td class="canvascontainer">
			<div class="standardbox information">
				<h1><?php echo $this->title; ?></h1>
				<p><?php echo $this->content; ?></p>
				<p><a href="javascript:history.back()"><?php echo $language['gobackprevious']?></a></p>
				<p><a href="javascript:void(parent._('<?php echo $_SESSION['parentdivid'];?>').close())"><?php echo $language['closewindow'];?></a></p>
			</div>
		</td>
	</tr>
</table>
