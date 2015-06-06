<?php
global $language;
global $config;

if ($this->nightbonus<1) $this->nightbonus=1;
if ($this->agebonus<1) $this->agebonus=1;
if ($this->nightbonus>3) $this->nightbonus=3;

?>

<h1><?php echo $language['battlesimulator']; ?></h1>
<form action="battlesim.php" method="POST">
    <input type="hidden" name="calculatebattle" value="yes">
	<p><?php echo $language['attacker']; ?></p>
	<p><?php echo $language['attacklevelofhero'];?><input type="text" name="attackhero" style="width:7em" value="<?php echo (double)$this->attackhero?>"></p>
	<table class="center">
		<tr>
			<th></th>
			<?php
				foreach($config['units'] as $key=>$value)
				{
					?>
						<th><img style="width:25px; height:25px" src="<?php echo $value['image'];?>" alt="<?php echo $language[$value['languageEntry']]; ?>" title="<?php echo $language[$value['languageEntry']]; ?>"></th>
					<?php
				}
			?>
		</tr>
		<tr>
			<td><?php echo $language['wentbattle']; ?></td>
			<?php
				foreach($config['units'] as $key=>$value)
				{
					?>
						<td><input style="width:7ex"  type="text" name="attacker_<?php echo $key?>" value="<?php $prop='attacker_'.$key; echo (double)$this->$prop; ?>"></td>
					<?php
				}
			?>
		</tr>
		<tr>
			<td><?php echo $language['died']; ?></td>
			<?php
				foreach($config['units'] as $key=>$value)
				{
					?>
						<td><?php echo $this->attacker['casualties'][$key]; ?></td>
					<?php
				}
			?>
		</tr>
	</table>
	<?php
		if ($this->wouldConquer)
		{
			?>
			<p><b><?php echo $language['conqueredthevillage']; ?></b></p>
			<?php
		}
	?>
	<p><?php echo $language['defender']; ?></p>
	<p><?php echo $language['defendlevelofhero'];?><input type="text" name="defenderhero" style="width:7em" value="<?php echo (double)$this->defenderhero?>"></p>
	<p><?php echo $language['defenderwalllevel'];?><input type="text" name="walllevel" style="width:7em" value="<?php echo (double)$this->walllevel?>"></p>
	<p><?php echo $language['defendertargetlevel'];?><input type="text" name="targetlevel" style="width:7em" value="<?php echo (double)$this->targetlevel?>"> <?php echo xprintf($language['demolishedto'],array((double)$this->targetdemolished)); ?></p>
	<p><input type="checkbox" name="targetwall" id="targetwall" <?php echo $this->targetwall ? 'checked="checked"' : ''; ?>><label for="targetwall"><?php echo $language['catapulttargetiswall'];?></label></p>
	<p><?php echo $language['nightbonus'];?><input type="text" name="nightbonus" style="width:7em" value="<?php echo (double)$this->nightbonus?>"></p>
	<p><?php echo $language['agebonus'];?><input type="text" name="agebonus" style="width:7em" value="<?php echo (double)$this->agebonus?>"></p>
	<table class="center">
		<tr>
			<th></th>
			<?php
				foreach($config['units'] as $key=>$value)
				{
					?>
						<th><img style="width:25px; height:25px" src="<?php echo $value['image'];?>" alt="<?php echo $language[$value['languageEntry']]; ?>" title="<?php echo $language[$value['languageEntry']]; ?>"></th>
					<?php
				}
			?>
		</tr>
		<tr>
			<td><?php echo $language['wentbattle']; ?></td>
			<?php
				foreach($config['units'] as $key=>$value)
				{
					?>
						<td><input style="width:7ex" type="text" name="defender_<?php echo $key?>" value="<?php $prop='defender_'.$key; echo (double)$this->$prop; ?>"></td>
					<?php
				}
			?>
		</tr>
		<tr>
			<td><?php echo $language['died']; ?></td>
			<?php
				foreach($config['units'] as $key=>$value)
				{
					?>
						<td><?php echo $this->defender['casualties'][$key]; ?></td>
					<?php
				}
			?>
		</tr>
	</table>
	<p>
		<input type="radio" name="mode" value="attack" id="attack" <?php echo $this->mode=='attack' || $this->mode=='' ? 'checked="checked"':''; ?> ><label for="attack"><?php echo $language['attack']; ?></label><br>
		<input type="radio" name="mode" value="raid" id="raid"  <?php echo $this->mode=='raid' ? 'checked="checked"':''; ?> ><label for="raid"><?php echo $language['raid']; ?></label><br>
		<input type="radio" name="mode" value="recon" id="recon"  <?php echo $this->mode=='recon' ? 'checked="checked"':''; ?> ><label for="recon"><?php echo $language['recon']; ?></label><br>
	</p>
	<p class="center"><input type="submit" value="<?php echo $language['calculate']; ?>"></p>
</form>


