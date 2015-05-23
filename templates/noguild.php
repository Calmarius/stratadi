<?php

global $language;


?>

<table class="canvascontainer borderlesscells">
	<tr>
		<td class="canvascontainer">
			<div class="standardbox">
				<h1><?php echo $language['notclanmember']; ?></h1>
				<p><?php echo $language['notclanmemberindetail']; ?></p>
				<hr>
				<p><?php echo $language['guildinvitations']; ?></p>
				<p>
					<?php
						if (count($this->invitations)==0)
							echo $language['noinvitations'];
						else 
						{
							echo '<ul>';
							foreach($this->invitations as $key=>$value)
							{
								?>
								<li>
									<a href="viewguild.php?id=<?php echo $value['id']; ?>"><?php echo $value['guildName']; ?></a> | 
									<a href="doinvitation.php?cmd=accept&id=<?php echo $value['invitationId']; ?>"><?php echo $language['acceptinvitation']; ?></a> |
									<a href="doinvitation.php?cmd=refuse&id=<?php echo $value['invitationId']; ?>"><?php echo $language['refuseinvitation']; ?></a>
								</li>
								<?php
//								echo '<tr><a href="viewguild.php?id='.$value['id'].'">'.$value['guildName'].'</a> <a href="doinvitation.php?cmd=accept&id='.$value['invitationId'].'">'.$language['acceptinvitation'].'</a> <a href="doinvitation.php?cmd=refuse&id='.$value['invitationId'].'">'.$language['refuseinvitation'].'</a></tr>';
							}
							echo '</ul>';
						}
					?>
				</p>
				<hr>
				<p><?php echo $language['foundaguild']; ?></p>
				<form action="dofoundguild.php" method="POST">
					<table class="borderlesscells center">
						<tr><td><?php echo $language['newguildname']; ?></td><td><input type="text" name="guildname"></td></tr>
					</table>
					<p><input type="submit" value="<?php echo $language['foundguildbutton']; ?>"></p>
				</form>
			</div>
		</td>
	</tr>
</table>
