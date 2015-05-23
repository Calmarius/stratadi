<?php

global $config;
global $language;

?>

<h1><?php echo $language['grantrightstoplayers'];?></h1>
<p><?php echo $language['grantrightsinfo'];?></p>
<form action="dosetrights.php" method="POST">
<p><?php echo $language['currentrights'];?></p>
<p>
	<table>
		<tr>
			<th></th>
			<?php
				foreach($config['guildPermissions'] as $key=>$value)
				{
					?>
						<th><?php echo $language[$value['langName']]; ?></th>
					<?php
				}
			?>
		</tr>
		<?php
			foreach($this->permissions as $key=>$value)
			{
				?>
					<tr>
						<td><?php echo $key; ?></td>
						<?php
							foreach($config['guildPermissions'] as $key2=>$value2)
							{
								?>
									<td><?php echo isset($value[$key2]) ? 'X':'' ?></td>
								<?php
							}
						?>
					</tr>
				<?php
			}
		?>
	</table>
</p>
<p><?php echo $language['playerlist']; ?></p>
<p>
	<select name="players[]" multiple="multiple">
		<?php
			foreach($this->members as $key=>$value)
			{
				?>
					<option value="<?php echo $value['id']; ?>"><?php echo $value['userName']; ?></option>
				<?php
			}
		?>
	</select>
</p>
<p><?php echo $language['rightlist']; ?></p>
<p>
	<select name="rights[]" multiple="multiple">
		<?php
			foreach($config['guildPermissions'] as $key=>$value)
			{
				?>
					<option value="<?php echo $key; ?>"><?php echo $language[$value['langName']]; ?></option>
				<?php
			}
		?>
	</select>
</p>
<p class="center"><input type="submit" value="<?php echo $language['grantrightstoplayers'];?>"></p>
</form>
