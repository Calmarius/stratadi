<?php

global $language;
global $config;

//print_r($this->args);
?>

<h1><?php echo $this->userName;?></h1>
<p class="center">#<?php echo $this->id;?></p>
<p class="center"><?php echo xprintf($language['villagecountscoretext'],array($this->villageCount,$this->totalScore)); ?></p>
<p class="center"><?php echo xprintf($language['agebonustext'],array(round($this->ageBonus,2))); ?></p>
<p class="center"><?php echo xprintf($language['revagebonustext'],array(round($this->revAgeBonus,2))); ?></p>
<p><img src="<?php echo $this->avatarLink; ?>" onerror="this.parentNode.removeChild(this);"></p>
<?php
	if ($this->heroId=='')
	{
		?>
			<p><?php echo $language['thisplayerdonthavehero']; ?></p>
		<?php
	}
	else
	{
		?>
		<p><a href="viewhero.php?id=<?php echo $this->heroId; ?>"><?php echo $language['viewhero']; ?></a></p>
		<?php
	}
	if ($this->own)
	{
		?>
			<p><a href="profile.php"><?php echo $language['editingprofile']; ?></a></p>			
		<?php
	}
	else
	{
		?>
			<p><a href="compose.php?name=<?php echo urlencode($this->userName); ?>"><?php echo $language['sendhimmessage']; ?></a></p>
		<?php
	}
?>
<table class="center">
	<tr><td><?php echo $language['guildname']; ?></td><td><a href="viewguild.php?id=<?php echo $this->guildId; ?>"><?php echo $this->guildName;?></a></td></tr>
	
	<tr>
		<td>
			<?php 
				echo $language['kings'];
			 ?>
		</td>
		<td>
			<?php
				$kinglinks=array();
				foreach($this->kings as $key=>$value)
				{
					$kinglinks[]='<a href="viewaccess.php?id='.$value['id'].'">'.$value['userName'].'</a>'.($value['isMaster'] ? "(${language['accountmaster']})":'');
				}
				echo implode(', ',$kinglinks);
			?>
		</td>
	</tr>
	<tr><td><?php echo $language['profile']; ?></td><td><?php echo parseBBCode($this->profile);?></td></tr>
</table>
<p><?php echo $language['villages']; ?></p>
<table class="center">
<?php
    foreach ($this->villages as $village)
    {
        $x = $village['x'];
        $y = $village['y'];
        $villageString = "${village['villageName']} ($x, $y)";
        echo '<tr><td><a href="javascript:void(parent.initMap('.$x.','.$y.'))">'.$villageString.'</a></td></tr>';
    }
?>
</table>

