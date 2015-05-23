<?php

global $language;
global $config;

?>

<p>
	<?php
		echo xprintf($language['donatorvillage'],array(
			'<a href="javascript:void(parent.initMap('.$this->params['donatorVillageX'].','.$this->params['donatorVillageY'].'))">'.xprintf($language['villagetext'],array($this->params['donatorVillageName'],$this->params['donatorVillageX'],$this->params['donatorVillageY'])).'</a>',
			'<a href="viewplayer.php?id='.$this->params['donatorId'].'">'.$this->params['donatorName'].'</a>')); 
	?>
</p>
<table class="center">
	<tr>
		<?php
			foreach($config['units'] as $key=>$value)
			{
				$unitDescriptor=$value;
				$langName=$language[$unitDescriptor['languageEntry']];
				?>
					<th><img style="width:25px; height:25px" src="<?php echo $value['image'];?>" alt="<?php echo $language[$value['languageEntry']]; ?>" title="<?php echo $language[$value['languageEntry']]; ?>"></th>
				<?php
			}
		?>
	</tr>
	<tr>
		<?php
			foreach($config['units'] as $key=>$value)
			{
				?>
					<td><?php echo $this->params['unit_'.$key]; ?></td>
				<?php
			}
		?>
	</tr>
</table>

<p>
	<?php
		echo xprintf($language['receivervillage'],array(
			'<a href="javascript:void(parent.initMap('.$this->params['receiverVillageX'].','.$this->params['receiverVillageY'].'))">'.xprintf($language['villagetext'],array($this->params['receiverVillageName'],$this->params['receiverVillageX'],$this->params['receiverVillageY'])).'</a>',
			'<a href="viewplayer.php?id='.$this->params['receiverId'].'">'.$this->params['receiverName'].'</a>')); 
	?>
</p>
