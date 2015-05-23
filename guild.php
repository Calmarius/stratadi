<?php

require_once('userworkerphps.php');

$guildInfo=array();

$q="SELECT wtfb2_guilds.* FROM wtfb2_users INNER JOIN wtfb2_guilds ON (wtfb2_guilds.id=wtfb2_users.guildId) WHERE (wtfb2_users.id='${_SESSION['userId']}')";
$r=doMySqlQuery($q,'jumpErrorPage');
$inGuild=false;
$invitations=array();

if (mysql_num_rows($r)>0)
{
	$guildInfo=mysql_fetch_assoc($r);
	$inGuild=true;
	$q=sqlPrintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId='{1}')",array($_SESSION['userId']));
	$r=doMySqlQuery($q,'jumpErrorPage');
	while($row=mysql_fetch_assoc($r))
	{
		$guildInfo['permissions'][$row['permission']]=true;
	}
	$q=
		sqlPrintf(
		"
			SELECT u.*,SUM(p.permission='diplomacy') AS diplomacyRight, SUM(p.permission='invite') AS inviteRight
			FROM wtfb2_users u
			LEFT JOIN wtfb2_guildpermissions p ON (u.id=p.userId)
			WHERE (guildId='{1}')
			GROUP BY u.id
		",$guildInfo['id']);
	$r=doMySqlQuery($q,'jumpErrorPage');
	while($row=mysql_fetch_assoc($r))
	{
		$guildInfo['members'][]=$row;
	}
	$r=doMySqlQuery
	(
		sqlPrintf
		(
			"SELECT wtfb2_diplomacy.*,wtfb2_guilds.guildName,wtfb2_guilds.id AS guildsId
			FROM wtfb2_diplomacy INNER JOIN wtfb2_guilds ON (wtfb2_diplomacy.toGuildId=wtfb2_guilds.id)
			WHERE (guildId={1})",array($guildInfo['id'])
		)
	);
	while($row=mysql_fetch_assoc($r))
	{
		$guildInfo['diplomacy'][]=$row;
	}	
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_threads WHERE (guildId='{1}')",array($guildInfo['id'])));
	while($row=mysql_fetch_assoc($r))
	{
		$guildInfo['threads'][]=$row;
	}	
}
else
{
	$q=sqlPrintf(
	"
		SELECT wtfb2_guilds.guildName,wtfb2_guilds.id,wtfb2_guildinvitations.id AS  invitationId
		FROM wtfb2_guildinvitations INNER JOIN wtfb2_guilds ON (wtfb2_guildinvitations.guildId=wtfb2_guilds.id)
		WHERE (recipientId='{1}')
	",array($_SESSION['userId']));
	$r=doMySqlQuery($q,'jumpErrorPage');
	while($row=mysql_fetch_assoc($r))
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
