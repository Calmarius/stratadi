<?php

global $config;
global $language;

?>

<h1><?php echo $language['events']; ?></h1>
<p class="center">
	<?php
		for($i=0;$i<$this->pages;$i++)
		{
			?>
				<a href="showevents.php?type=<?echo $this->type; ?>&category=<?echo $this->category; ?>&p=<?php echo $i; ?>"><?php echo ($i+1);?></a>
			<?php
		}
	?>
</p>
<?php
	foreach($this->events as $key=>$event)
	{
		$eventDescriptor=$config['operations'][$event['eventType']];
		$units=array();
		$unitTable='<table class="center">';
		$unitTable.='<tr>';
		foreach($config['units'] as $key=>$value)
		{
			$countName=$value['countDbName'];
			$unitTable.='<th><img src="'.$value['image'].'" alt="'.$language[$value['languageEntry']].'" style="width:20px; height:20px"></th>';
		}
		$unitTable.='</tr>';
		$unitTable.='<tr>';
		foreach($config['units'] as $key=>$value)
		{
			$countName=$value['countDbName'];
			$unitTable.='<td>'.$event[$countName].'</td>';
		}
		$unitTable.='</tr>';
		$unitTable.='</table>';
//			$units[]=xprintf($language['amountform'],array($event[$countName],$language[$value['languageEntry']]));
		?>
			<p>
				<?php 
				    if ($event['catapultTarget'] == 'none') $event['catapultTarget'] = '';
				    if ($event['catapultTarget'])
				    {
				        // TODO: Check if 'none' is a valid catapult target in the database.
					    $buildingName=$language[$config['buildings'][$event['catapultTarget']]['languageEntry']];
					    $buildingImage=$config['buildings'][$event['catapultTarget']]['image'];
					}
					echo xprintf($language[$eventDescriptor['langDesc']],
						array
						(
							$language[$eventDescriptor['langName']],
							"<a href=\"javascript:void(parent.initMap(${event['srcX']},${event['srcY']}))\">".xprintf($language['villagetext'],array($event['source'],$event['srcX'],$event['srcY']))."</a>",
							"<a href=\"javascript:void(parent.initMap(${event['dstX']},${event['dstY']}))\">".xprintf($language['villagetext'],array($event['destination'],$event['dstX'],$event['dstY']))."</a>",
							$unitTable,
							"<a href=\"javascript:void(parent.initMap(${event['targetX']}, ${event['targetY']}))\">".xprintf($language['coordinate'],array($event['targetX'],$event['targetY']))."</a>",
							$event['gold'],
							'<span class="countdown">'.$event['happensIn'].'</span> ~'.$event['estimatedTime'],
							$event['heroName']!='' ? '<a href="viewhero.php?id='.$event['heroId'].'">'.$event['heroName'].'</a>' : $language['na'],
							$event['catapultTarget']=='' ? '?':'<img src="'.$buildingImage.'" alt="'.$buildingName.'" title="'.$buildingName.'">',
							$event['cancellable'] ? '<a href="docancelevent.php?id='.$event['id'].'">'.$language['cancelevent'].'</a>':'',
							$eventDescriptor['color']
						)
					);
				 ?>
			</p>
		<?php
	}
?>
