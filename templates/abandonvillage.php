<?php

global $language;

?>

<h1><?php echo $language['abandonvillage'];?></h1>
<p><?php echo $language['youchoosetoabandonthisvillage']; ?></p>
<h2><?php echo xprintf($language['villagetext'],array($this->village['villageName'],$this->village['x'],$this->village['y']))?></h2>
<form method="post" action="doabandonvillage.php">
	<input type="hidden" value="<?php echo $this->village['id']?>" name="id">
	<p class="center"><?php echo $language['enterpasswordtoconfirm'];?><input type="password" name="password"></p>
	<p class="center"><input type="submit" value="<?php echo $language['confirm'];?>"></p>
</form>
