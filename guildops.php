<?php

require_once("userworkerphps.php");

bounceSessionOver();
$userId=$_SESSION['userId'];
$operation=$_GET['cmd'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId='{1}') AND (permission='{2}')",array($userId,$operation)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($userId)));
$me=mysql_fetch_assoc($r);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guilds WHERE (id='{1}')",array($me['guildId'])));
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$myGuild=mysql_fetch_assoc($r);


$content='';
if ($_GET['cmd']=='editprofile')
{
	$content=new Template('templates/editclanprofile.php',$myGuild);
}
else if ($_GET['cmd']=='invite')
{
	$r=doMySqlQuery(sqlPrintf("SELECT i.*,u.userName FROM wtfb2_guildinvitations i JOIN wtfb2_users u ON (u.id=i.recipientId)WHERE (i.guildId='{1}')",array($me['guildId'])));
	$invitations=array();
	while($row=mysql_fetch_assoc($r)) $invitations[]=$row;
	$content=new Template('templates/inviteplayer.php',array('invitations'=>$invitations));	
}
else if ($_GET['cmd']=='dismiss')
{
	$content=new Template('templates/dismissguild.php',array());		
}
else if ($_GET['cmd']=='diplomacy')
{
	$r=doMySqlQuery(
		sqlPrintf(
			"
				SELECT wtfb2_diplomacy.*,wtfb2_guilds.guildName,wtfb2_guilds.id AS guildsId FROM wtfb2_diplomacy INNER JOIN wtfb2_guilds ON (wtfb2_diplomacy.toGuildId=wtfb2_guilds.id) WHERE (guildId='{1}')
			",array($myGuild['id'])
		)
	);
	$diplomacy=array();
	while($row=mysql_fetch_assoc($r))
	{
		$diplomacy[]=$row;
	}
	$dipl=new Template('templates/diplomacy.php',array('diplomacy'=>$diplomacy));		
	$content=new Template('templates/standardboxtemplate.php',array('content'=>$dipl->getContents()));		
}
else if ($_GET['cmd']=='kick')
{
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (guildId='{1}') AND (id<>'{2}')",array($myGuild['id'],$userId)));
	$members=array();
	while($row=mysql_fetch_assoc($r))
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
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (guildId='{1}') AND (id<>'{2}')",array($myGuild['id'],$userId)));
	$members=array();
	while($row=mysql_fetch_assoc($r))
	{
		$members[]=$row;
	}
	ksort($members);
	$r=doMySqlQuery
	(
		sqlPrintf
		(
			"SELECT gp.*,u.userName FROM wtfb2_guildpermissions gp INNER JOIN wtfb2_users u ON (gp.userId=u.id) INNER JOIN wtfb2_guilds g ON (u.guildId=g.id) WHERE u.guildId='{1}'"
			,array($myGuild['id'])
		)
	);
	$permissions=array();
	while($row=mysql_fetch_assoc($r))
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
	$r=doMySqlQuery
	(
		sqlPrintf
		(
			"SELECT * FROM wtfb2_threads WHERE (guildId={1})",array($myGuild['id'])
		)
	);
	$guildThreads=array();
	while($row=mysql_fetch_assoc($r))
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
