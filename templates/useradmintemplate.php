<?php

global $config;
global $language;

?>

<h1><?php echo $language['hereyoucanswitchplayer'];?></h1>
<form action="switchuser.php" method="GET">
	<p class="center">
		Order by: <input type="text" name="orderby"> <input type="checkbox" name="desc" id="desc"> <label for="desc">descending</label> <input type="submit" value="GO">
	</p>
</form>
<table>
	<?php
		foreach($this->users as $key=>$user)
		{
			?>
				<tr><td colspan="2"><a href="javascript:void(parent.location.href='doswitchuser.php?id=<?php echo $user['id']; ?>')"><?php echo $user['userName']; ?></a></td></tr>
				<?php
					foreach($user as $key2=>$userParam)
					{
						?>
							<tr><td><?php echo $key2;?></td><td><?php echo $userParam?></td></tr>
						<?php
					}
				?>
			<?php
		}
	?>
</table>
