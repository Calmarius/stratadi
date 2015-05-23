<?php

require_once('userworkerphps.php');

bounceSessionOver();
$myId=$_SESSION['userId'];
$invitationId=$_GET['id'];

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guildinvitations WHERE (id='{1}') AND (recipientId='{2}')",array($invitationId,$myId)));
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$invitation=mysql_fetch_assoc($r);

$command=$_GET['cmd'];
if ($command=='accept')
{
	doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_guildpermissions WHERE (userId='{1}')",array($myId)),'jumpErrorPage');
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET guildId='{1}' WHERE (id='{2}')",array($invitation['guildId'],$myId)),'jumpErrorPage');
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guilds WHERE (id='{1}')",array($invitation['guildId'])));
	if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
	$guild=mysql_fetch_assoc($r);
	$_SESSION['successtitle']=$language['successfulguildenter'];
	$_SESSION['successcontent']=xprintf($language['nowyoumemberoftheguild'],array($guild['guildName']));
	doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (eventTime,playerId,type) VALUES (NOW(),'{1}','guildchange')",array($myId))); // a játékos klánváltoztatását világgákürtöljük
	doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (eventTime,guildId,type,recipientId,needFullRefresh) VALUES (NOW(),'{1}','diplomacychange','{2}',1)",array($invitation['guildId'],$myId))); // a játékosnak is küldünk egy eseményt.
	
}
else if ($command=='refuse')
{
	$_SESSION['successtitle']=$language['invitationrefused'];
	$_SESSION['successcontent']='';
}
else jumpErrorPage('Command doesn\'t exist. If you arrived here from a link in the game, please contact administrator.');

doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_guildinvitations WHERE (id='{1}')",array($invitationId)),'jumpErrorPage');

jumpTo("success.php");

?>
