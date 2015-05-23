<?php

global $language;

?>
<h1><?php echo $language['notes']; ?></h1>
<div class="positive"><?php echo $this->msg; ?></div>
<form action="dosavenotes.php" method="post">
	<p><textarea rows="25" cols="80" name="notes"><?php echo $this->notes?></textarea></p>
	<p><input type="submit" value="<?php echo $language['saveedits']; ?>"></p>
</form>
