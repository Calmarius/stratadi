<?php

global $language;
global $config;

?>

<p>
	<?php
		echo xprintf($language['attackervillage'],array(
			'<a href="javascript:void(parent.initMap('.$this->params['attackerVillageX'].','.$this->params['attackerVillageY'].'))">'.xprintf($language['villagetext'],array($this->params['attackerVillageName'],$this->params['attackerVillageX'],$this->params['attackerVillageY'])).'</a>',
			'<a href="viewplayer.php?id='.$this->params['attackerId'].'">'.$this->params['attackerName'].'</a>')); 
	?>
</p>
<table class="center">
	<tr>
		<th>&nbsp;</th>
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
		<td><?php echo $language['wentbattle'];?></td>
		<?php
			foreach($config['units'] as $key=>$value)
			{
				?>
					<td><?php echo $this->params['attacker_'.$key]; ?></td>
				<?php
			}
		?>
	</tr>
	<tr>
		<td><?php echo $language['died'];?></td>
		<?php
			$attackerLosses=0;
			foreach($config['units'] as $key=>$value)
			{
				$attackerLosses+=$this->params['attacker']['casualties'][$key]*$value['cost'];
				?>
					<td><?php echo $this->params['attacker']['casualties'][$key]; ?></td>
				<?php
			}
		?>
	</tr>
	<tr>
		<td colspan="<?php echo count($config['units'])+1; ?>"><?php echo xprintf($language['loottext'],array($this->params['loot'])); ?></td>
	</tr>
	<tr>
		<?php
		if (isset($config['buildings'][$this->params['catapultTarget']]))
		{		
			$bdesc=$config['buildings'][$this->params['catapultTarget']];
			$buildingLangEntry=$language[$bdesc['languageEntry']];
			?>
				<td colspan="<?php echo count($config['units'])+1; ?>"><?php echo xprintf($language['demolitiontext'],array('<img style="vertical-align: middle" src="'.$bdesc['image'].'" alt="'.$buildingLangEntry.'" title="'.$buildingLangEntry.'">',$this->params['targetlevel'],$this->params['defender']['targetdemolished'])); ?></td>
			<?php
		}
		?>
	</tr>
	<tr>
		<td colspan="<?php echo count($config['units'])+1;?>"><?php echo xprintf($language['attackerlosses'],array($attackerLosses));?></td>
	</tr>
	<?php
		if ($this->params['attackerFalls'])
		{
			?>
				<tr>
					<td colspan="<?php echo count($config['units'])+1; ?>"><?php echo $language['attackerlostbattle']; ?></td>
				</tr>
			<?php
			if (count($this->params['attacker']['heroes'])>0)
			{
				?>
					<tr>
						<td colspan="<?php echo count($config['units'])+1; ?>"><?php echo $language['attackerlostbattlehero']; ?></td>
					</tr>
				<?php
			}
		}
		if (count($this->params['attacker']['heroes'])>0)
		{
			$heroes=array();
			foreach($this->params['attacker']['heroes'] as $key=>$value)
			{
				$heroes[]='<a href="viewhero.php?id='.$value['id'].'">'.$value['name'].'</a>';
			}
			?>
				<tr>
					<td colspan="<?php echo count($config['units'])+1; ?>"><?php echo xprintf($language['attackerheroes'],array(implode(',',$heroes))) ?></td>
				</tr>
			<?php			
		}
		if ($this->params['destroyed'])
		{
			?>
				<tr>
					<td colspan="<?php echo count($config['units'])+1; ?>"><?php echo $language['villagedestroyed']; ?></td>
				</tr>
			<?php			
		}
		if ($this->params['conquered'])
		{
			?>
				<tr>
					<td colspan="<?php echo count($config['units'])+1; ?>"><?php echo $language['conqueredthevillage']; ?></td>
				</tr>
			<?php			
		}
	?>
</table>

<p>
	<?php
		echo xprintf($language['defendervillage'],array(
			'<a href="javascript:void(parent.initMap('.$this->params['defenderVillageX'].','.$this->params['defenderVillageY'].'))">'.xprintf($language['villagetext'],array($this->params['defenderVillageName'],$this->params['defenderVillageX'],$this->params['defenderVillageY'])).'</a>',
			'<a href="viewplayer.php?id='.$this->params['defenderId'].'">'.$this->params['defenderName'].'</a>')); 
	?>
</p>
<table class="center">
	<tr>
		<th>&nbsp;</th>
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
		<td><?php echo $language['wentbattle'];?></td>
		<?php
			foreach($config['units'] as $key=>$value)
			{
				?>
					<td><?php echo $this->params['defender_'.$key]; ?></td>
				<?php
			}
		?>
	</tr>
	<tr>
		<td><?php echo $language['died'];?></td>
		<?php
			$defenderLosses=0;
			foreach($config['units'] as $key=>$value)
			{
				$defenderLosses+=$this->params['defender']['casualties'][$key]*$value['cost'];
				?>
					<td><?php echo $this->params['defender']['casualties'][$key]; ?></td>
				<?php
			}
		?>
	</tr>
	<tr>
		<td colspan="<?php echo count($config['units'])+1;?>"><?php echo xprintf($language['defenderlosses'],array($defenderLosses));?></td>
	</tr>
	<?php
		if ($this->params['defenderFalls'])
		{
			?>
				<tr>
					<td colspan="<?php echo count($config['units'])+1; ?>"><?php echo $language['defenderlostbattle']; ?></td>
				</tr>
			<?php
			if (count($this->params['defender']['heroes'])>0)
			{
				?>
					<tr>
						<td colspan="<?php echo count($config['units'])+1; ?>"><?php echo $language['defenderlostbattlehero']; ?></td>
					</tr>
				<?php
			}
		}
		if (count($this->params['defender']['heroes'])>0)
		{
			$heroes=array();
			foreach($this->params['defender']['heroes'] as $key=>$value)
			{
				$heroes[]='<a href="viewhero.php?id='.$value['id'].'">'.$value['name'].'</a>';
			}
			?>
				<tr>
					<td colspan="<?php echo count($config['units'])+1; ?>"><?php echo xprintf($language['defenderheroes'],array(implode(',',$heroes))) ?></td>
				</tr>
			<?php			
		}
	?>
</table>

