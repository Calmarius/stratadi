<?php

require_once("userworkerphps.php");

bounceSessionOver();

$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$_SESSION['userId']);
$me=$r[0][0];

$r=runEscapedQuery("SELECT * FROM wtfb2_guildpermissions WHERE (userId={0}) AND (permission='invite')",$_SESSION['userId']);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);

$r=runEscapedQuery("SELECT i.*,g.guildName FROM wtfb2_guildinvitations i JOIN wtfb2_guilds g ON (i.guildId=g.id) WHERE (i.id={0}) AND (i.guildId={1})",$_GET['id'],$me['guildId']);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$invitation=$r[0][0];

// delete the invitation
runEscapedQuery("DELETE FROM wtfb2_guildinvitations WHERE (id={0})",$_GET['id']);
// send report to the recipient
runEscapedQuery
(
	"INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,reportType) VALUES ({0},{1},{2},NOW(),'unknown')",
	$invitation['recipientId'],
	$language['revokeinvitationtitle'],
	xprintf($language['revokeinvitationtext'],array('<a href="viewguild.php?id='.$invitation['guildId'].'">'.$invitation['guildName'].'</a>'))
);

jumpTo('guildops.php?cmd=invite');

?>
