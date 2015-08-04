<?php

require_once('userworkerphps.php');

$r=runEscapedQuery("SELECT * FROM wtfb2_guilds WHERE (id={0})",$_GET['id']);
if (isEmptyResult($r)) jumpErrorPage($language['guildnotexist']);
$guildInfo=$r[0][0];

$guildInfo['members']=array();
$q=
sqlvprintf(
	"
		SELECT u.*,SUM(p.permission='diplomacy') AS diplomacyRight, SUM(p.permission='invite') AS inviteRight
		FROM wtfb2_users u
		LEFT JOIN wtfb2_guildpermissions p ON (u.id=p.userId)
		WHERE (guildId={0})
		GROUP BY u.id
	",array($_GET['id']));
$r=runEscapedQuery($q);
foreach ($r[0] as $row)
{
	$guildInfo['members'][]=$row;
}

showInBox('templates/guildpage.php',array('guild'=>$guildInfo,'showOperations'=>false));

?>
