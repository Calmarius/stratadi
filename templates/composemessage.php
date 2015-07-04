<?php

global $config;
global $language;

$subjectText = @$this->subject;

if ($subjectText != '' && (substr($subjectText, 0, strlen('RE:')) != 'RE:')) $subjectText = 'RE:' . $subjectText;

// TODO: (security, xss) Check all echo's if they need to use htmlspecialchars. Consider using a new function that prints escaped string.

?>

<h1><?php echo $language['composemessage'];?></h1>
<p><span class="positive"><?php echo @$this->notification; ?></span></p>
<form method="POST" action="dosendmessage.php">
	<table class="center">
		<tr><td><?php echo $language['addparticipant']; ?></td><td><input type="text" name="recipient" value="<?php echo htmlspecialchars($this->recipient); ?>" <?php echo $this->extra!='' ? 'disabled="disabled"':'';  ?>><input type="hidden" name="thread" value="<?php echo $this->thread; ?>"></td></tr>
		<tr><td><?php echo $language['subject']; ?></td><td><input type="text" name="subject" value="<?php echo htmlspecialchars($subjectText); ?>"></td></tr>
		<?php
			if ($this->extra=='circular')
			{
				?>
				<tr><td colspan="2"><?php echo $language['thismessagewillbeacircular']; ?></td></tr>
				<?php
			}
			else if ($this->extra=='guildthread')
			{
				?>
				<tr><td colspan="2"><?php echo $language['thismessagewillbeaguildthread']; ?></td></tr>
				<?php
			}	
		?>
	</table>
	<p>
		<textarea cols="50" rows="10" name="content" id="content"><?php echo $this->content; ?></textarea>
		<input type="hidden" name="extra" value="<?php echo $this->extra?>">
		<input type="hidden" name="nowstamp" value="<?php echo $this->nowstamp; ?>">
	</p>
	<p class="center"><input type="submit" value="<?php echo $language['sendletterbutton']; ?>"><input type="button" value="<?php echo $language['preview']; ?>" onclick="window.open('bbcodepreview.php?text='+encodeURIComponent(document.getElementById('content').value),'guildwindow','width=320,height=200,scrollbars=1,toolbar=1')"></p>
</form>
<?php
	foreach($this->entries as $key=>$value)
	{
		?>
		<table class="center">
			<tr>
				<td><a href="viewplayer.php?id=<?php echo htmlspecialchars($value['posterId']); ?>"><?php echo $value['userName']; ?></a></td>
				<td><?php echo $value['when'];?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo parseBBCode($value['text']); ?></td>
			</tr>
		</table>
		<?php
	}

?>

