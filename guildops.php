<?php

require_once("userworkerphps.php");

bounceSessionOver();
$userId=$_SESSION['userId'];
$operation=$_GET['cmd'];
$r=runEscapedQuery("SELECT * FROM wtfb2_guildpermissions WHERE (userId={0}) AND (permission={1})",$userId,$operation);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$userId);
$me=$r[0][0];
$r=runEscapedQuery("SELECT * FROM wtfb2_guilds WHERE (id={0})",$me['guildId']);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$myGuild=$r[0][0];


$content='';
if ($_GET['cmd']=='editprofile')
{
	$content=new Template('templates/editclanprofile.php',$myGuild);
}
else if ($_GET['cmd']=='invite')
{
	$r=runEscapedQuery("SELECT i.*,u.userName FROM wtfb2_guildinvitations i JOIN wtfb2_users u ON (u.id=i.recipientId)WHERE (i.guildId={0})",$me['guildId']);
	$invitations=array();
	foreach ($r[0] as $row) $invitations[]=$row;
	$content=new Template('templates/inviteplayer.php',array('invitations'=>$invitations));
}
else if ($_GET['cmd']=='dismiss')
{
	$content=new Template('templates/dismissguild.php',array());
}
else if ($_GET['cmd']=='diplomacy')
{
	$r=runEscapedQuery(
		"
			SELECT wtfb2_diplomacy.*,wtfb2_guilds.guildName,wtfb2_guilds.id AS guildsId
			FROM wtfb2_diplomacy
			INNER JOIN wtfb2_guilds ON (wtfb2_diplomacy.toGuildId=wtfb2_guilds.id) WHERE (guildId={0})
		",$myGuild['id']
	);
	$diplomacy=array();
	foreach ($r[0] as $row)
	{
		$diplomacy[]=$row;
	}
	$dipl=new Template('templates/diplomacy.php',array('diplomacy'=>$diplomacy));
	$content=new Template('templates/standardboxtemplate.php',array('content'=>$dipl->getContents()));
}
else if ($_GET['cmd']=='kick')
{
	$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (guildId={0}) AND (id<>{1})",$myGuild['id'],$userId);
	$members=array();
	foreach ($r[0] as $row)
	{
		$members[]=$row;
	}
	$kickplayer=new Template('templates/kickplayer.php',array('members'=>$members));
	$content=new Template('templates/standardboxtemplate.php',array('content'=>$kickplayer->getContents()));
}
else if ($_GET['cmd']=='circular')
{
	jumpTo('compose.php?extra=circular');
}
else if ($_GET['cmd']=='grantrights')
{
	$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (guildId={0}) AND (id<>{1})",$myGuild['id'],$userId);
	$members=array();
	foreach ($r[0] as $row)
	{
		$members[]=$row;
	}
	ksort($members);
	$r=runEscapedQuery(
		"SELECT gp.*,u.userName FROM wtfb2_guildpermissions gp INNER JOIN wtfb2_users u ON (gp.userId=u.id) INNER JOIN wtfb2_guilds g ON (u.guildId=g.id) WHERE u.guildId={0}"
		,$myGuild['id']
	);
	$permissions=array();
	foreach ($r[0] as $row)
	{
		$key=$row['userName'];
		if (!isset($permissions[$key])) $permissions[$key]=array();
		$permissions[$key][$row['permission']]=true;
	}
	ksort($permissions);
	$grantRights=new Template('templates/grantrights.php',array('members'=>$members,'permissions'=>$permissions));
	$content=new Template('templates/standardboxtemplate.php',array('content'=>$grantRights->getContents()));
}
else if ($_GET['cmd']=='moderate')
{
	$r=runEscapedQuery
	(
	    "SELECT * FROM wtfb2_threads WHERE (guildId={0})",$myGuild['id']
	);
	$guildThreads=array();
	foreach ($r[0] as $row)
	{
		$guildThreads[]=$row;
	}
	$moderate=new Template('templates/moderate.php',array('guildthreads'=>$guildThreads));
	$content=new Template('templates/standardboxtemplate.php',array('content'=>$moderate->getContents()));
}
else
	jumpErrorPage('Configuration failure: nonexisting task. If you clicked a menu item and see this please contact the administrator.');
$page=new Template('templates/basiclayout.php',array('title'=>'WTFBattles II','content'=>$content->getContents()));
$page->render();


?>
