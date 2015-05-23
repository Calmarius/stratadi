<?php

global $language;

$url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$urldir=strrpos($url,'/');
$path=substr($url,0,$urldir+1);
/*echo '<pre>';
var_dump($_SERVER);
echo '</pre>';*/

?>

<h1><?php echo $language['inviteplayerandgetep']; ?></h1>
<p><?php echo xprintf($language['publishthefollowinglink'],array($path.'login.php?referer='.$this->me['id'])); ?></p>
<?php echo $language['inviteplayerdescription']; ?>
<p><?php echo $language['yourreferreds']; ?></p>
<table class="center">
<tr><th><?php echo $language['invitedplayername'];?></th><th><?php echo $language['action']; ?></th></tr>
<?php
	foreach($this->invited as $key=>$value)
	{
		?>
			<tr>
				<td><a href="viewplayer.php?id=<?php echo $value['id']; ?>"><?php echo $value['userName']; ?></a></td>
				<td><a href="docheckreferred.php?id=<?php echo $value['id']; ?>&rnd=<?php echo mt_rand(); ?>"><?php echo $language['checkreferred']; ?></a></td>
			</tr>
		<?php
	}
?>
</table>
