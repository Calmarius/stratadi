<?php

global $language;

?>

<h1><?php echo $language['kickplayer']; ?></h1>
<p><?php echo $language['selectplayertokick']; ?></p>
<ul>
	<?php
		foreach($this->members as $key=>$value)
		{
			?>
				<li><a href="dokick.php?id=<?php echo $value['id']; ?>"><?php echo $value['userName']?></a></li>
			<?php
		}
	?>
</ul>

