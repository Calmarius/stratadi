<?php

global $language;

?>

<h1><?php echo $language['managekings'];?></h1>
<table class="center">
	<tr><th></th><th><?php echo $language['name']; ?></th><th><?php echo $language['action']; ?></th></tr>
	<?php
		foreach($this->accesses as $key=>$value)
		{
			?>
				<tr>
					<td><?php echo $value['isMaster'] ? $language['accountmaster']:''; ?></td>
					<td><?php echo $value['userName']; ?></td>
					<td>
						<?php
							if ($this->canmanage)
							{
								?>
									<a href="dokingops.php?cmd=kick&id=<?php echo $value['id'];?>&rnd=<?php echo md5(rand());?>"><?php echo $language['kick']?></a> | 
									<a href="dokingops.php?cmd=setmaster&id=<?php echo $value['id'];?>&rnd=<?php echo md5(rand());?>"><?php echo $language['setmaster']?></a>
								<?php
							}
							else if ($value['isMe'])
							{
								?>
									<a href="dokingops.php?cmd=leave&rnd=<?php echo md5(rand());?>"><?php echo $language['leavekingdom']; ?></a>
								<?php
							}
						?>
					</td>
				</tr>
			<?php
		}
	?>
</table>
<?php
	if ($this->canmanage)
	{
		?>
			<form action="dokingops.php?cmd=newking" method="post">
				<p><?php echo $language['addnewking']?> <input type="text" name="newking"></p>
				<p><?php echo $language['thekingmustnotcontrolakingdom']?></p>
				<p><input type="submit" value="<?php echo $language['hire']; ?>"></p>
			</form>
		<?php
	}
?>

