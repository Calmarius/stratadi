<?php

global $language;

?>

<h1><?php echo $language['managediplomacy']; ?></h1>
<p><?php echo $language['diplomacyrelationswithotherguilds']; ?></p>
<ul>
	<?php
		foreach($this->diplomacy as $key=>$value)
		{
			?>
				<li><?php echo xprintf($language['diplomacywithguild'],array($value['guildName'],$language[$value['attitude']],$value['guildsId'])); ?>
					<a href="dodeletediplomacyrelation.php?id=<?php echo $value['id']; ?>"><?php echo $language['deletediplomacyrelation'];?></a></li>
			<?php
		}
	?>
</ul>
<hr>
	<form action="doadddiplomacy.php" method="post">
		<p class="center"><?php echo $language['newdiplomaticrelationship']; ?></p>
		<p class="center"><?php echo $language['typeguildsid']; ?><input type="text" name="guildid"></p>
		<p class="center"><?php echo $language['or']; ?></p>
		<p class="center"><?php echo $language['typeguildname']; ?><input type="text" name="guildname"></p>
		<p>
			<?php echo $language['diplomaticstance']; ?><br>
			<input type="radio" name="stance" value="ally" checked="checked" id="ally"><label for="ally"><?php echo $language['ally']; ?></label><br>
			<input type="radio" name="stance" value="peace" id="peace"><label for="peace"><?php echo $language['peace']; ?></label><br>
			<input type="radio" name="stance" value="war" id="war"><label for="war"><?php echo $language['war']; ?></label>
		</p>
		<p><input type="submit" value="<?php echo $language['setdiplomaticstance'];?>"></p>
	</form>

