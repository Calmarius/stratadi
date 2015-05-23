<?php
//bejelentkezési form
global $language;
?>

<table class="canvascontainer">
	<tr>
		<td class="canvascontainer">
			<h1><?php echo $language['wtfbattles'];?></h1>
			<h1><?php echo $language['wtfbattleslong'];?></h1>
			<div style="float:left; width:30%; margin: 10px; height:auto; border: 1px solid blue; background-color: #8080FF">
<!--				<div class="standardbox">-->
					<p><?php echo $language['pleaselogin'];?></p>
					<p class="center"><?php echo $language['imnotregistered'];?><a href="registration.php<?php echo (int)($this->referer) ? '?referer='.$this->referer : ''; ?>"><?php echo $language['registration'];?></a></p>				
					<hr>
					<p><?php echo $language['imregistered'];?></p> 
					<form method="post" action="dologin.php">
					<p><?php echo $language['username']; ?><input type="text" name="username" value="<?php echo $this->username; ?>"><?php echo $this->usernameError; ?></p>
					<p><?php echo $language['password']; ?><input type="password" name="password" value="<?php echo $this->password; ?>"><?php echo $this->passwordError; ?></p>
					<p><input type="submit" value="<?php echo $language['login']?>"></p>
<!--				</div>-->
			</div>
			<?php echo $language["gamedescription"]; ?>
		</td>
	</tr>
</table>
