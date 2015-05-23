<?php

global $language;
global $config;

require_once('utils/gameutils.php');

?>

<h1><?php echo $this->guild['guildName']; ?></h1>
<p class="center">#<?php echo $this->guild['id']; ?></p>
<?php
if ($this->showOperations)
{
	?>
		<hr>
		<p><?php echo $language['guildtopics'];?></p>
		<ul>
		<?php
			if (is_array($this->guild['threads']))
			{
				?>
					<table class="center">
				<?php
				foreach($this->guild['threads'] as $key=>$value)
				{
					?>
						<tr>
							<td><a href="viewthread.php?id=<?php echo  $value['id']; ?>"><?php echo $value['subject']; ?></a></td>
							<td><a href="dosubscribeguildtopic.php?id=<?php echo $value['id']; ?>"><?php echo $language['subscribetopic'];?></a></td>
						</tr>
					<?php
				}
				?>
					</table>
				<?php
			}
		?>
		</ul>
		<hr>
		<p><?php echo $language['diplomacyrelationswithotherguilds']; ?></p>
		<ul>
			<?php
				if (is_array($this->guild['diplomacy']))
				{
					foreach($this->guild['diplomacy'] as $key=>$value)
					{
						?>
							<li><?php echo xprintf($language['diplomacywithguild'],array($value['guildName'],$language[$value['attitude']],$value['guildsId'])); ?></li>
						<?php
					}
				}
			?>
		</ul>
		<hr>
	 	<p><a href="exitguild.php"><?php echo $language['leaveguild'];?></a></p>
		<hr>
		<p><?php echo $language['guildoperations']; ?></p> 
		<ul>
			<?php
				foreach($config['guildPermissions'] as $key => $value)
				{
					echo isset($this->guild['permissions'][$key]) ? '<li><a href="guildops.php?cmd='.$key.'">'.$language[$value['langName']].'</a></li>' :'';
				}
			?>
		</ul>
	<?php
}
?>
<hr>
	<?php echo parseBBCode($this->guild['profile']);?>
<hr>
<p><?php echo $language['guildmemberlist']; ?></p>
<table class="center">
	<?php
		foreach($this->guild['members'] as $key=>$value)
		{
			$perms=array();
			if ((int)$value['diplomacyRight']) $perms[]=$language['guilddiplomat'];
			if ((int)$value['inviteRight']) $perms[]=$language['recruiter'];
			$permString=count($perms)==0  ? '':xprintf($language['guildpermissionstring'],array(implode(',',$perms)));
			?>
				<tr><td><a href="viewplayer.php?id=<?php echo $value['id']; ?>"><?php echo $value['userName'];?></a></td><td><?php echo $permString;?></td></tr>
			<?php
		}
	?>
</table>

