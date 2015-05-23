<?php

require_once('userworkerphps.php');

bounceSessionOver();
$myId=$_SESSION['userId'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId='{1}') AND (permission='kick')",array($myId)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($myId)),'jumpErrorPage');
$me=mysql_fetch_assoc($r);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guilds WHERE (id='{1}')",array($me['guildId'])),'jumpErrorPage');
$guild=mysql_fetch_assoc($r);

$kickeeId=(int)$_GET['id'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($kickeeId)),'jumpErrorPage');
$kickee=mysql_fetch_assoc($r);
doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (eventTime,playerId,type) VALUES (NOW(),'{1}','guildchange')",array($kickeeId)));	 // guild change
doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (eventTime,guildId,type,recipientId,needFullRefresh) VALUES (NOW(),'{1}','diplomacychange','{2}',1)"),array($me['guildId'],$kickeeId)); // notify the player too.
// kick from the guild
doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET guildId=NULL WHERE (id='{1}')",array($kickeeId)));
// revoke his rights
doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_guildpermissions WHERE (userId='{1}')",array($kickeeId)));
// send him a farewell report
$repTitle=$language['youhavebeenkicked'];
$repContent=xprintf($language['someonekickedyoufromthesomeguild'],array($myId,$me['userName'],$guild['id'],$guild['guildName']));
doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ('{1}','{2}','{3}',NOW(),MD5(RAND()))",array($kickeeId,$repTitle,$repContent)));
// well done
jumpSuccessPage(xprintf($language['playerhasbeenkicked'],array($kickeeId,$kickee['userName'])),'');

?>
