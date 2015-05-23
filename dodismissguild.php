<?php

require_once('userworkerphps.php');
bounceSessionOver();

$myId=$_SESSION['userId'];
$myPass=$_POST['password'];

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId='{1}') AND (permission='dismiss')",array($myId)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_accesses WHERE (accountId='{1}') AND (passwordHash=MD5('{2}'))",array($myId,$myPass)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$access=mysql_fetch_assoc($r);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($access['accountId'])));
$myself=mysql_fetch_assoc($r);

$r=doMySqlQuery(sqlPrintf("SELECT id FROM wtfb2_users WHERE (guildId='{1}')",array($myself['guildId'])));
$values=array();
while($row=mysql_fetch_assoc($r))
{
	$values[]=sqlPrintf("(NOW(),'{1}','guildchange')",array($row['id']));	
}
doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET guildId=NULL WHERE (guildId='{1}')",array($myself['guildId'])),'jumpErrorPage');
doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_guilds WHERE (id='{1}')",array($myself['guildId'])),'jumpErrorPage');
doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_guildpermissions WHERE (userId='{1}')",array($myId)),'jumpErrorPage');
if (count($values)>0)
	doMySqlQuery("INSERT INTO wtfb2_worldevents (eventTime,playerId,type) VALUES ".implode(',',$values)); // is values escaped?	

$_SESSION['successtitle']=$language['youdismissedtheguild'];

jumpTo("success.php");

?>
