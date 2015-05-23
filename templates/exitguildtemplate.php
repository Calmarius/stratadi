<?php

global $language;

?>

<h1><?php echo $language['leaveguild'];?></h1>
<form action="doleaveguild.php" method="POST">
	<p><?php echo $language['usepasswordtoleave']; ?></p>
	<p><?php echo $language['password']; ?> <input type="password" name="password"></p>
	<p><input type="submit" value="<?php echo $language['leaveguild']; ?>"></p>
</form>

