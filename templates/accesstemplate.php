<?php

global $language;

?>

<?php

global $language;
global $config;

//print_r($this->args);
?>

<h1><?php echo $this->userName;?></h1>
<p class="center">#<?php echo $this->id;?></p>
<?php
	if ($this->own)
	{
		?>
			<p><a href="editaccess.php"><?php echo $language['editingprofile']; ?></a></p>			
		<?php
	}
?>
<table class="center">
	<tr><td><?php echo $language['city']; ?></td><td><?php echo $this->city;?></td></tr>
	<tr><td><?php echo $language['age']; ?></td><td><?php echo $this->age;?></td></tr>
	<tr><td><?php echo $language['gender']; ?></td><td><?php if (isset($this->gender)) echo $language[$this->gender];?></td></tr>
	<tr><td><?php echo $language['spokenlanguages']; ?></td><td><?php echo $this->languages;?></td></tr>
</table>

