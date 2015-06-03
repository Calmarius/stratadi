<?php

require_once('userworkerphps.php');

bounceSessionOver();

$userId=$_SESSION['userId'];

$r=runEscapedQuery("SELECT * FROM wtfb2_guildpermissions WHERE (userId={0}) AND (permission='editprofile')",$userId);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);


$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$userId);
$a=$r[0][0];
$guildprofile=htmlspecialchars($_POST['guildprofile']);
runEscapedQuery("UPDATE wtfb2_guilds SET profile={0} WHERE (id={1})",$guildprofile,$a['guildId']);


jumpTo("guild.php");


?>
