<?php

global $language;
global $config;

?>

<h1><?php echo $this->thread['subject']; ?></h1>
<p class="center">
	<?php
		for($i=0;$i<$this->pages;$i++)
		{
			?>
				<a href="viewthread.php?id=<?php echo $this->id; ?>&link=<?php echo $this->link; ?>&p=<?php echo $i; ?>"><?php echo ($i+1);?></a>
			<?php
		}
	?>
</p>
<?php
	if ($this->guildLetter)
	{
		echo '<p class="center">'.$language['thisisaguildletter'].'</p>';
	}
?>
<p>
	<?php echo $language['participants'];
		$first=true;
		foreach($this->participants as $key=>$value)
		{
			if (!$first) echo ', ';
			$first=false;
			echo "<a href=\"viewplayer.php?id=${value['userId']}\">${value['userName']}</a>";
		}
	?>

	
</p>
<p><a href="compose.php?thread=<?php echo $this->thread['id']; ?>&subject=<?php echo urlencode($this->thread['subject']); ?>"><?php echo $language['sendreply']?></a></p>
<?php
	foreach($this->entries as $key=>$value)
	{
		?>
		<table class="center">
			<tr>
				<td><a href="viewplayer.php?id=<?php echo htmlspecialchars($value['posterId']); ?>"><?php echo $value['userName']; ?></a></td>
				<td><?php echo $value['when'];?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo parseBBCode($value['text']); ?></td>
			</tr>
		</table>
		<?php
	}

?>

