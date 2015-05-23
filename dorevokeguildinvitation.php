<?php

require_once("userworkerphps.php");

bounceSessionOver();

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($_SESSION['userId'])));
$me=mysql_fetch_assoc($r);

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId='{1}') AND (permission='invite')",array($userId)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);

$r=doMySqlQuery(sqlPrintf("SELECT i.*,g.guildName FROM wtfb2_guildinvitations i JOIN wtfb2_guilds g ON (i.guildId=g.id) WHERE (i.id='{1}') AND (i.guildId='{2}')",array($_GET['id'],$me['guildId'])),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$invitation=mysql_fetch_assoc($r);

// delete the invitation
doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_guildinvitations WHERE (id='{1}')",array($_GET['id'])));
// send report to the recipient
doMySqlQuery
(
	sqlPrintf
	(
		"INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,reportType) VALUES ('{1}','{2}','{3}',NOW(),'unknown')",
		array
		(
			$invitation['recipientId'],
			$language['revokeinvitationtitle'],
			xprintf($language['revokeinvitationtext'],array('<a href="viewguild.php?id='.$invitation['guildId'].'">'.$invitation['guildName'].'</a>'))
		)
	)
);

jumpTo('guildops.php?cmd=invite');

?>
