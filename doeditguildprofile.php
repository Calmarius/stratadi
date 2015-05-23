<?php

require_once('userworkerphps.php');

bounceSessionOver();

$userId=$_SESSION['userId'];

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId='{1}') AND (permission='editprofile')",array($userId)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);


$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($userId)));
$a=mysql_fetch_assoc($r);
$guildprofile=htmlspecialchars($_POST['guildprofile']);
doMySqlQuery(sqlPrintf("UPDATE wtfb2_guilds SET profile='{1}' WHERE (id='{2}')",array($guildprofile,$a['guildId'])));


jumpTo("guild.php");


?>
