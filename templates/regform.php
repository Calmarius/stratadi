<?php
//bejelentkezési form
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

			<h1><?php echo $language['wtfbattles'];?></h1>
			<div class="standardbox">
				<h2><?php echo $language['registrationtitle'];?></h2>
				<?php readfile($language['rulefile']);?>
				<form method="post" action="doregistration.php" onsubmit="return validateRegForm(this)" enctype="multipart/form-data">
					<table class="center borderlesscells" >
						<tr><td colspan="2"><h3><?php echo $language['kingsdata'];?></h3></td></tr>
						<tr><td colspan="2"><h4><?php echo $language['mandatorydata'];?></h4></td></tr>
						<tr><td class="right"><?php echo $language['username'];?></td><td class="left"><input type="text" name="username" value="<?php echo $this->username; ?>"><?php echo $this->kingNameError; ?></td></tr>
						<tr><td class="right"><?php echo $language['email'];?></td><td class="left"><input type="text" name="mail" value="<?php echo $this->mail; ?>"><?php echo $this->mailError; ?></td></tr>
						<tr><td class="right"><?php echo $language['password'];?></td><td class="left"><input type="password" name="password" value="<?php echo $this->password; ?>"><?php echo $this->passwordError; ?></td></tr>
						<tr><td class="right"><?php echo $language['passwordagain'];?></td><td class="left"><input type="password" name="password2" value="<?php echo $this->password2; ?>"><?php echo $this->password2Error; ?></td></tr>
						<tr><td colspan="2"><h4><?php echo $language['optionaldata'];?></h4></td></tr>
						<tr><td class="right"><?php echo $language['city'];?></td><td class="left"><input type="text" name="town" value="<?php echo $this->town; ?>"><?php echo $this->townError; ?></td></tr>
						<tr><td class="right"><?php echo $language['birthdate'];?></td><td class="left"><?php generateDatepicker('year','month','day',$months,$this->year,$this->month,$this->day); ?><?php echo $this->dateError; ?></td></tr>
						<tr><td class="right"><?php echo $language['gender'];?></td><td class="left"><?php generateGenderPicker($genders,'gender',$this->gender); ?><?php echo $this->genderError; ?></td></tr>
						<tr><td class="right"><?php echo $language['spokenlanguages'];?></td><td class="left"><?php generateLanguagePicker('languages','wtfb2_languages'); ?></td></tr>
					</table>
					<fieldset>
						<legend>
							<input type="checkbox" checked="checked" name="registerkingdom" id="regkingdomcheckbox" onclick="var tags=this.parentNode.parentNode.getElementsByTagName('input'); for(var i in tags){var e=tags[i]; if (e==this) continue; e.disabled=!this.checked;}">
							<label for="regkingdomcheckbox"><?php echo $language['registerkingdom'];?></label>
						</legend>
						<table class="center borderlesscells" >
							<tr><td colspan="2"><h3><?php echo $language['kingdomsdata'];?></h3></td></tr>
							<tr><td colspan="2"><h4><?php echo $language['mandatorydata'];?></h4></td></tr>
							<tr><td class="right"><?php echo $language['kingdomname'];?></td><td class="left"><input type="text" name="kingdomname" value="<?php echo $this->kingdomname; ?>"><?php echo $this->userNameError; ?></td></tr>
							<tr><td class="right"><?php echo $language['heroname'];?></td><td class="left"><input type="text" name="heroname" value="<?php echo $this->heroname; ?>"><?php echo $this->heroNameError; ?></td></tr>
							<tr><td colspan="2"><h4><?php echo $language['optionaldata'];?></h4></td></tr>
							<tr><td class="right"><?php echo $language['heroavatar'];?></td><td class="left"><span><input type="button" onclick="this.parentNode.innerHTML=this.parentNode.innerHTML;" value="X"><input type="file" name="heroavatar"><span><?php echo $this->heroAvatarError; ?></td></tr>
							<tr><td class="right"><?php echo $language['kingdomsavatar'];?></td><td class="left"><span><input type="button" onclick="this.parentNode.innerHTML=this.parentNode.innerHTML;" value="X"><input type="file" name="youravatar"><span><?php echo $this->yourAvatarError; ?></td></tr>
						</table>
					</fieldset>
					<p><?php echo $language['reginfo'];?></p>
					<p><input type="hidden" name="referer" value="<? echo (int)@$_GET['referer']; ?>"><input type="submit" value="<?php echo $language['registrationbutton'] ?>"></p>
				</form>
			</div>
<script type="text/javascript">
	function validateRegForm(form)
	{
		/*var errorElement=document.createElement('span');
		errorElement.className="negative";
		var text=document.createTextNode('asdsadasd');
		errorElement.appendChild(text);
		//checking username
		var elm=form.username;
		if (elm.nextSibling)
			elm.nextSibling.parentNode.removeChild(elm.nextSibling);
		var s=form.username.value;
		if (s.length<<?php echo $config['minUserNameLength']; ?>)
		{
			text.data='<?php echo $language['usernameshort']; ?>';
			elm.parentNode.appendChild(errorElement);
			return false;
		}
		//checking e-mail
		elm=form.mail;
		if (elm.nextSibling) elm.nextSibling.parentNode.removeChild(elm.nextSibling);
		var s=form.mail.value;
		if (!s.match(/^[^@]+@([^\.]+\.)+[^\.]+$/))
		{
			text.data='<?php echo $language['invalidemail']; ?>';
			elm.parentNode.appendChild(errorElement);
			return false;
		}
		// checking passwords
		elm=form.password;
		if (elm.nextSibling) elm.nextSibling.parentNode.removeChild(elm.nextSibling);
		if (form.password.value.length< <?php echo $config['minUserPasswordLength'];?>)
		{
			text.data='<?php echo $language['passwordtooshort']; ?>';
			elm.parentNode.appendChild(errorElement);
			return false;			
		}
		if (form.password.value!=form.password2.value)
		{
			text.data='<?php echo $language['passwordnotmatch']; ?>';
			elm.parentNode.appendChild(errorElement);
			return false;
		}
		// checking hero name
		elm=form.heroname;
		if (elm.nextSibling) elm.nextSibling.parentNode.removeChild(elm.nextSibling);
		if (elm.value.length< <?php echo $config['minHeroNameLength']?> )
		{
			text.data='<?php echo $language['heronametooshort']; ?>';
			elm.parentNode.appendChild(errorElement);
			return false;
		}
		//checking day
		elm=form.day;
		if (elm.nextSibling) elm.nextSibling.parentNode.removeChild(elm.nextSibling);
		if ((elm.value!="") && ((parseInt(elm.value)<1) || (parseInt(elm.value)>31)))
		{
			text.data='<?php echo $language['invalidday']; ?>';
			elm.parentNode.appendChild(errorElement);
			return false;
		}
		// checking year
		elm=form.year;
		if (elm.nextSibling) elm.nextSibling.parentNode.removeChild(elm.nextSibling);
		var cDate=new Date();
		if ((elm.value!="") && (cDate.getFullYear()<parseInt(elm.value)))
		{
			text.data='<?php echo $language['invalidyear']; ?>';
			elm.parentNode.appendChild(errorElement);
			return false;
		}
		
		// all is ok*/
		return true;					
	}
</script>









