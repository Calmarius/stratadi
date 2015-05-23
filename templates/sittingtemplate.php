<?php
	global $language;
	global $config;
	
?>

<h1><?php echo $language['deputies'];?></h1>
<p><?php echo $language['youdeputize']; ?></p>
<ul>
<?php
	foreach($this->deputies as $key=>$value)
	{
		?>
			<li><a href="javascript:void(parent.location.href='dologinasdeputy.php?id=<?php echo $value['sponsorId']; ?>')"><?php echo $value['userName'];?></a></li>
		<?php	
	}
?>
</ul>
<hr>
<p><?php echo $language['youcandelegatedeputy'];?></p>
<p><?php echo $language['deputywillreceiveareport'];?></p>
<form action="dodelegatedeputy.php" method="POST">
	<p><?echo $language['pleaseenterthechoosendeputyname']; ?><input type="text" name="deputyname"></p>
	<p><input type="submit" value="<?php echo $language['delegate']; ?>"> </p>
</form>

