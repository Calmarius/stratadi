<?php

require_once('userworkerphps.php');
bounceSessionOver();

$myId=$_SESSION['userId'];
$myPass=$_POST['password'];

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_accesses WHERE (accountId='{1}') AND (passwordHash=MD5('{2}'))",array($myId,$myPass)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);

doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET guildId=NULL WHERE (id='{1}')",array($myId)),'jumpErrorPage');
doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (eventTime,playerId,type) VALUES (NOW(),'{1}','guildchange')",array($myId)));	

jumpSuccessPage($language['youlefttheguild']);


?>
