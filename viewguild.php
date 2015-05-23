<?php

require_once('userworkerphps.php');

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guilds WHERE (id='{1}')",array($_GET['id']))); 
if (mysql_num_rows($r)==0) jumpErrorPage($language['guildnotexist']);
$guildInfo=mysql_fetch_assoc($r);

$guildInfo['members']=array();
$q=
sqlPrintf(
	"
		SELECT u.*,SUM(p.permission='diplomacy') AS diplomacyRight, SUM(p.permission='invite') AS inviteRight
		FROM wtfb2_users u
		LEFT JOIN wtfb2_guildpermissions p ON (u.id=p.userId)
		WHERE (guildId='{1}')
		GROUP BY u.id
	",array($_GET['id']));
$r=doMySqlQuery($q);
while($row=mysql_fetch_assoc($r))
{
	$guildInfo['members'][]=$row;
}

showInBox('templates/guildpage.php',array('guild'=>$guildInfo,'showOperations'=>false));

?>
