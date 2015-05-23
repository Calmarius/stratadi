<?php

global $config;
global $language;

$vectorLength=count($this->unit['attack']);

?>

<table>
	<tbody class="center">
		<tr><th colspan="<?php echo $vectorLength+1; ?>"><?php echo $language[$this->unit['singularLanguageEntry']]; ?></th></tr>
		<tr>
			<td rowspan="2"><?php echo $language['attackpower']; ?></td>
			<?php
				for($i=0;$i<$vectorLength;$i++)
				{
					?>
						<td><?php echo $language[$config['attackVectorDescription'][$i]];?></td>
					<?php
				}
			?>
		</tr>
		<tr>
			<?php
				for($i=0;$i<$vectorLength;$i++)
				{
					?>
						<td><?php echo $this->unit['attack'][$i];?></td>
					<?php
				}
			?>		
		</tr>
		<tr>
			<td rowspan="2"><?php echo $language['defensepower']; ?></td>
			<?php
				for($i=0;$i<$vectorLength;$i++)
				{
					?>
						<td><?php echo $language[$config['defenseVectorDescription'][$i]];?></td>
					<?php
				}
			?>
		</tr>
		<tr>
			<?php
				for($i=0;$i<$vectorLength;$i++)
				{
					?>
						<td><?php echo $this->unit['defense'][$i];?></td>
					<?php
				}
			?>		
		</tr>
		<tr><td><?php echo $language['cost']; ?></td><td colspan="<?php echo $vectorLength; ?>"><?php echo xprintf($language['coststring'],array($this->unit['cost'])); ?></td></tr>
		<tr><td><?php echo $language['speed']; ?></td><td colspan="<?php echo $vectorLength; ?>"><?php echo xprintf($language['speedtext'],array($this->unit['speed'])); ?></td></tr>
		<tr><td><?php echo $language['trainedat']; ?></td><td colspan="<?php echo $vectorLength; ?>"><?php echo $language[$config['buildings'][$this->unit['trainedAt']]['languageEntry']]; ?></td></tr>
		<tr><td><?php echo $language['strength']; ?></td><td colspan="<?php echo $vectorLength; ?>"><?php echo  xprintf($language['strengthtext'],array($this->unit['strength'])); ?></td></tr>
		<tr><td><?php echo $language['trainingtime']; ?></td><td colspan="<?php echo $vectorLength; ?>"><?php echo  hourString($this->unit['trainingTime']); ?></td></tr>
	</tbody>
</table>





