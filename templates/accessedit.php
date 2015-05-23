<?php

global $language;
global $config;

// hónaptömb
$months=array();
$months[]=$language['january'];
$months[]=$language['february'];
$months[]=$language['march'];
$months[]=$language['april'];
$months[]=$language['may'];
$months[]=$language['june'];
$months[]=$language['july'];
$months[]=$language['august'];
$months[]=$language['september'];
$months[]=$language['october'];
$months[]=$language['november'];
$months[]=$language['december'];

$genders=array();
$genders[]=$language['male'];
$genders[]=$language['female'];

?>

<h1><?php echo $language['editingprofile']; ?></h1>
<form method="post" action="doeditaccess.php" enctype="multipart/form-data">
	<table class="center borderlesscells">
		<tr><td class="right"><?php echo $language['city'];?></td><td class="left"><input type="text" name="town" value="<?php echo $this->city; ?>"></td></tr>
		<tr><td class="right"><?php echo $language['birthdate'];?></td><td class="left"><?php generateDatepicker('year','month','day',$months,$this->year,$this->month,$this->day); ?></td></tr>
		<tr><td class="right"><?php echo $language['gender'];?></td><td class="left"><?php generateGenderPicker($genders,'gender',$this->gender); ?></td></tr>
		<tr><td class="right"><?php echo $language['spokenlanguages'];?></td><td class="left"><?php generateLanguagePicker('spokenlanguages','wtfb2_languages',$this->languages); ?></td></tr>
		<tr><td class="center" colspan="2"><?php echo $language['ifyoudontwanttochangepassword']; ?></td></tr>
		<tr><td class="right"><?php echo $language['oldpassword'];?></td><td class="left"><input type="password" name="oldpassword" value=""></td></tr>
		<tr><td class="right"><?php echo $language['password'];?></td><td class="left"><input type="password" name="password" value=""></td></tr>
		<tr><td class="right"><?php echo $language['passwordagain'];?></td><td class="left"><input type="password" name="password2" value=""></td></tr>
		
	</table>
	<p><input type="submit" value="<?php echo $language['modifybutton'] ?>"></p>
</form>

