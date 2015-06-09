<?php

require_once('userworkerphps.php');

bounceSessionOver();
$myId=$_SESSION['userId'];
$invitationId=$_GET['id'];

$r=runEscapedQuery("SELECT * FROM wtfb2_guildinvitations WHERE (id={0}) AND (recipientId={1})",$invitationId,$myId);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$invitation=$r[0][0];

$command=$_GET['cmd'];
if ($command=='accept')
{
	runEscapedQuery("DELETE FROM wtfb2_guildpermissions WHERE (userId={0})",$myId);
	runEscapedQuery("UPDATE wtfb2_users SET guildId={0} WHERE (id={1})",$invitation['guildId'],$myId);
	$r=runEscapedQuery("SELECT * FROM wtfb2_guilds WHERE (id={0})",$invitation['guildId']);
	if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
	$guild=$r[0][0];
	$_SESSION['successtitle']=$language['successfulguildenter'];
	$_SESSION['successcontent']=xprintf($language['nowyoumemberoftheguild'],array($guild['guildName']));
	runEscapedQuery("INSERT INTO wtfb2_worldevents (eventTime,playerId,type) VALUES (NOW(),{0},'guildchange')",$myId); // a játékos klánváltoztatását világgákürtöljük
	runEscapedQuery("INSERT INTO wtfb2_worldevents (eventTime,guildId,type,recipientId,needFullRefresh) VALUES (NOW(),{0},'diplomacychange',{1},1)",$invitation['guildId'],$myId); // a játékosnak is küldünk egy eseményt.
	
}
else if ($command=='refuse')
{
	$_SESSION['successtitle']=$language['invitationrefused'];
	$_SESSION['successcontent']='';
}
else jumpErrorPage('Command doesn\'t exist. If you arrived here from a link in the game, please contact administrator.');

runEscapedQuery("DELETE FROM wtfb2_guildinvitations WHERE (id={0})", $invitationId);

jumpTo("success.php");

?>
