<?php

require_once('userworkerphps.php');

$guildInfo=array();

$q="SELECT wtfb2_guilds.* FROM wtfb2_users INNER JOIN wtfb2_guilds ON (wtfb2_guilds.id=wtfb2_users.guildId) WHERE (wtfb2_users.id='${_SESSION['userId']}')";
$r=runEscapedQuery($q);
$inGuild=false;
$invitations=array();

if (!isEmptyResult($r))
{
	$guildInfo=$r[0][0];
	$inGuild=true;
	$q=sqlvprintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId={0})",array($_SESSION['userId']));
	$r=runEscapedQuery($q);
	foreach ($r[0] as $row)
	{
		$guildInfo['permissions'][$row['permission']]=true;
	}
	$q=
		sqlvprintf(
		"
			SELECT u.*,SUM(p.permission='diplomacy') AS diplomacyRight, SUM(p.permission='invite') AS inviteRight
			FROM wtfb2_users u
			LEFT JOIN wtfb2_guildpermissions p ON (u.id=p.userId)
			WHERE (guildId={0})
			GROUP BY u.id
		",array($guildInfo['id']));
	$r=runEscapedQuery($q);
	$guildInfo['members'] = array();
	foreach ($r[0] as $row)
	{
		$guildInfo['members'][]=$row;
	}
	$r=runEscapedQuery
	(
		"SELECT wtfb2_diplomacy.*,wtfb2_guilds.guildName,wtfb2_guilds.id AS guildsId
		FROM wtfb2_diplomacy INNER JOIN wtfb2_guilds ON (wtfb2_diplomacy.toGuildId=wtfb2_guilds.id)
		WHERE (guildId={0})",$guildInfo['id']
	);
	$guildInfo['diplomacy'] = array();
	foreach ($r[0] as $row)
	{
		$guildInfo['diplomacy'][]=$row;
	}	
	$r=runEscapedQuery("SELECT * FROM wtfb2_threads WHERE (guildId={0})",$guildInfo['id']);
	$guildInfo['threads'] = array();
	foreach ($r[0] as $row)
	{
		$guildInfo['threads'][]=$row;
	}	
}
else
{
	$q=sqlvprintf(
	"
		SELECT wtfb2_guilds.guildName,wtfb2_guilds.id,wtfb2_guildinvitations.id AS  invitationId
		FROM wtfb2_guildinvitations INNER JOIN wtfb2_guilds ON (wtfb2_guildinvitations.guildId=wtfb2_guilds.id)
		WHERE (recipientId={0})
	",array($_SESSION['userId']));
	$r=runEscapedQuery($q);
	foreach ($r[0] as $row)
	{
		$invitations[]=$row;
	}
}

$content='';
if ($inGuild)
{
	$content=new Template('templates/guildpage.php',array('guild'=>$guildInfo,'showOperations'=>true));	
}
else
{
	$content=new Template('templates/noguild.php',array('invitations'=>$invitations));	
}
$box=new Template('templates/standardboxtemplate.php',array('content'=>$content->getContents()));
$page=new Template('templates/basiclayout.php',array('title'=>'WTFBattles II','content'=>$box->getContents()));
$page->render();


?>
