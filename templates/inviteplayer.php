<?php

global $config;
global $language;

?>

<table class="canvascontainer">
	<tr>
		<td class="canvascontainer">
			<div class="standardbox">
				<h1><?php echo $language['inviteplayer']; ?></h1>
				<p><?php echo $language['inviteplayerinfo']; ?></p>
				<form action="doinviteplayer.php" method="POST">
					<p><?php echo $language['enterplayernametoinvite']; ?><input type="text" name="playertoinvite"></p>
					<p><input type="submit" value="<?php echo $language['invitebutton']; ?>"></p>
				</form>
				<p><?php echo $language['guildinvitations'];?></p>
				<table style="margin: 0 auto 0 auto">
					<tr><th><?php echo $language['player'];?></th><th>&nbsp;</th></tr>
					<?php
						foreach($this->invitations as $key=>$value)
						{
							?>
								<tr>
									<td><a href="viewplayer.php?id=<?php echo $value['recipientId']; ?>"><?php echo $value['userName']; ?></a></td>
									<td><a href="dorevokeguildinvitation.php?id=<?php echo $value['id']?>&rnd=<?php echo rand(); ?>"><?php echo $language['revoke']; ?></a></td>
								</tr>
							<?php
						}
					?>
				</table>
			</div>
		</td>
	</tr>
</table>
