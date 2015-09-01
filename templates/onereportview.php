<?php

global $language;
global $config;

/*print_r($this->report);
die();*/

?>

<h1><?php echo $this->report['title']; ?></h1>
<p>
	<?php echo $this->reportLink;  ?>
	<?php
		if ($this->report['isPublic'])
		{
			?>
				<p class="center">
					<?php echo $language['shareit']; ?>
					<a title="Facebook" onclick="window.open('http://www.facebook.com/share.php?u=<?php echo urlencode($this->reportLink); ?>'); return false;" href="http://www.facebook.com/share.php?u=<?php echo urlencode($this->reportLink); ?>"><img src="img/facebook.png"></a>
					<a title="Twitter" onclick="window.open('http://twitter.com/home?status=<?php echo urlencode($this->reportLink); ?>'); return false;" href="http://twitter.com/home?status=<?php echo urlencode($this->reportLink); ?>"><img src="img/twitter.png"></a>
					<a title="Tumblr" onclick="window.open('http://www.tumblr.com/share?v=3&u=<?php echo urlencode($this->reportLink); ?>'); return false;" href="http://www.tumblr.com/share?v=3&u=<?php echo urlencode($this->reportLink); ?>"><img src="img/tumblr.png"></a>
				</p>					
			<?php
		}
	?>
</p>
<p class="center"><?php echo $this->report['reportTime'];?></p>
<?php
	if ($this->showoptions)
	{
		?>
		<table style="width:100%; table-layout:fixed">
			<tr>
				<?php
					if ($this->report['prevId']!='') 
					{
						?>
							<td style="text-align:center"><a href="viewreport.php?id=<?php echo $this->report['prevId'];?>&amp;token=<?php echo $this->report['prevToken']; ?>"><?php echo $language['previousreport']; ?></a></td>
						<?php
					}
				?>
				<td style="text-align:center"><a href="viewreport.php?id=<?php echo $this->report['id']; ?>&amp;token=<?php echo $this->report['token']; ?>&amp;op=delete"><?php echo $language['delete']; ?></a></td>
				<?php
					if ($this->report['nextId']!='') 
					{
						?>
							<td style="text-align:center"><a href="viewreport.php?id=<?php echo $this->report['nextId'];?>&amp;token=<?php echo $this->report['nextToken']; ?>"><?php echo $language['nextreport']; ?></a></td>
						<?php
					}
				?>
			</tr>
		</table>
		<?php
	}
?>
<div>
	<?php echo $this->report['text']; ?>
</div>
<?php
	if ($this->showoptions)
	{
		?>
		<hr>
		<table style="width:100%; table-layout:fixed">
			<tr>
				<?php
					if ($this->report['prevId']!='') 
					{
						?>
							<td style="text-align:center"><a href="viewreport.php?id=<?php echo $this->report['prevId'];?>&amp;token=<?php echo $this->report['prevToken']; ?>"><?php echo $language['previousreport']; ?></a></td>
						<?php
					}
				?>
				<td style="text-align:center"><a href="viewreport.php?id=<?php echo $this->report['id']; ?>&amp;token=<?php echo $this->report['token']; ?>&amp;op=delete"><?php echo $language['delete']; ?></a></td>
				<?php
					if ($this->report['nextId']!='') 
					{
						?>
							<td style="text-align:center"><a href="viewreport.php?id=<?php echo $this->report['nextId'];?>&amp;token=<?php echo $this->report['nextToken']; ?>"><?php echo $language['nextreport']; ?></a></td>
						<?php
					}
				?>
			</tr>
		</table>
		<form action="dosetreport.php" method="POST" >
			<p><input type="checkbox" name="hidden" value="hidden" <?php echo $this->report['isHidden'] ? 'checked="checked"':'';?>><?php echo $language["makehidden"]; ?></p>
			<p><input type="checkbox" name="public" value="public"  <?php echo $this->report['isPublic'] ? 'checked="checked"':'';?>><?php echo $language["makepublic"]; ?></p>
			<p><input type="submit" value="<?php echo $language['set']; ?>"><input type="hidden" name="id" value="<?php echo $this->report['id']; ?>"></p>
		</form>
		<?php
	}
	else
	{
		?>
			<hr>
			<p><a href="login.php"><?php echo $language['playnow']; ?></a></p>
		<?php
	}
?>

