<?php

global $language;
global $config;

?>

<form method="POST" action="dounsubscribe.php">
	<h1><?php echo $language['messages'];?></h1>
	<p class="center">
		<?php
			for($i=0;$i<$this->pages;$i++)
			{
				?>
					<a href="messages.php?p=<?php echo $i; ?>"><?php echo ($i+1);?></a>
				<?php
			}
		?>
	</p>
	<p class="center"><a href="compose.php"><?php echo $language["composenewmessage"]; ?></a></p>
	<script>
		function checkAllWithName(elmName,checkState)
		{
			var elms=document.getElementsByTagName('input');
			for(var i=0;i<elms.length;i++)
			{
				var elm=elms[i];
				if ((elm.getAttribute('type')=='checkbox') && (elm.getAttribute('name')==elmName))
				{
					elm.checked=checkState;
				}
			}
		}
	</script>
	<table class="center">
		<?php
			echo "<tr><th></th><th>${language['lastposter']}</th><th>${language['topic']}</th><th>${language['lastmessageposted']}</th></tr>";
			$i=0;
			echo '<td><input type="checkbox" onclick="checkAllWithName(\'thread[]\',this.checked)"></td><td colspan="4"></td>';
			foreach($this->letterlinks as $key=>$value)
			{
				$subject=$value['subject'];
				$subject=$subject=='' ? $language['nosubject'] : $subject;
				echo "<tr>";
				echo '<td><input type="checkbox" name="thread[]" value="'.$value['linkId'].'"></td>';
				echo "<td><a href=\"viewplayer.php?id=${value['senderId']}\">${value['userName']}</a></td>";
				echo "<td><a href=\"viewthread.php?id=${value['messageId']}&link=${value['linkId']}\">$subject</a></td>";
				echo "<td>${value['updated']}</td>";
				echo "<td>".($value['read'] ? '':$language['new'])."</td>";
				echo "</tr>";
			}
		?>
	</table>
	<p class="center"><input type="submit" value="<?php echo $language['unsubscribe']; ?>"></p>
</form>

