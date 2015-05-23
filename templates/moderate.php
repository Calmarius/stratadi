<?php
global $language;
?>

<h1><?php echo $language['moderateforum'];?></h1>
<p><?php echo $language['thesearetheguildthreads'];?></p>
<ul>
	<?php
		foreach($this->guildthreads as $key=>$value)
		{
			?>
				<li><a href="viewthread.php?id=<?php echo $value['id']; ?>"><?php echo $value['subject']; ?></a></li>
			<?php
		}
	?>
</ul>
<p class="center"><a href="compose.php?extra=guildthread"><?php echo $language['startnewtopic'];?></a></p>
