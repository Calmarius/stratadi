<?php
require_once("userworkerphps.php");

bounceSessionOver();
$myId=$_SESSION['userId'];
$guildPermission=runEscapedQuery("SELECT * FROM wtfb2_guildpermissions WHERE (userId={0}) AND (permission='diplomacy')",$myId);
if (isEmptyResult($guildPermission)) jumpErrorPage($language['accessdenied']);
$myself=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$myId);
if (isEmptyResult($myself)) jumpErrorPage($language['accessdenied']);
$myself = $myself[0][0];
$guildId=$_POST['guildid'];
$guildName=$_POST['guildname'];

if (is_numeric($guildId))
{
	$theirGuild=runEscapedQuery("SELECT * FROM wtfb2_guilds WHERE (id={0})",$guildId);
	if (isEmptyResult($theirGuild)) jumpErrorPage($language['guildnotexist']);    
}
else
{
	$theirGuild=runEscapedQuery("SELECT * FROM wtfb2_guilds WHERE (guildName={0})",$guildName);
	if (isEmptyResult($theirGuild)) jumpErrorPage($language['guildnotexist']);
}
$theirGuild=$theirGuild[0][0];
$myGuild = runEscapedQuery("SELECT * FROM wtfb2_guilds WHERE (id={0})",$myself['guildId']);
if (isEmptyResult($myGuild)) jumpErrorPage($language['guildnotexist']);
$myGuild=$myGuild[0][0];

$diplomaticStance=runEscapedQuery("SELECT * FROM wtfb2_diplomacy WHERE (guildId={0}) AND (toGuildId={1})",$myself['guildId'],$theirGuild['id']);
if (!isEmptySet(diplomaticStance)) jumpErrorPage($language['diplomaticstancealreadyexist']);
runEscapedQuery("INSERT INTO wtfb2_diplomacy (attitude,guildId,toGuildId) VALUES ({0},{1},{2})",$_POST['stance'],$myself['guildId'],$theirGuild['id']);
runEscapedQuery("INSERT INTO wtfb2_worldevents (x,y,eventTime,type,guildId,recipientGuildId) VALUES (0,0,NOW(),'diplomacychanged',{0},{1})",$theirGuild['id'],$myself['guildId']); // send event ourselves.
// notify users with diplomacy right about the change
$diplomatsInTheirGuild=runEscapedQuery(
		"
			SELECT u.* 
			FROM wtfb2_users u
			INNER JOIN wtfb2_guildpermissions p ON (p.userid=u.id)
			INNER JOIN wtfb2_guilds g ON (u.guildId=g.id)
			WHERE (g.id={0}) AND (p.permission='diplomacy')
		",
		$theirGuild['id']
);
$diplomatsInTheirGuild = $diplomatsInTheirGuild[0];
// find out what diplomatic stance set by the recipient guild
$r=runEscapedQuery("SELECT * FROM wtfb2_diplomacy WHERE ((attitude={0}) AND (guildId={1}) AND (toGuildId={2}))",$_POST['stance'],$theirGuild['id'],$myself['guildId']);
$reportTitle='';
$reportText='';
if (isEmptySet($r))
{
	// different or no stance set so set a diplomacy invitation
	$reportTitle=xprintf($language['diplomacyreporttitle'],array($myGuild['guildName']));
	$reportText=xprintf($language['diplomacyreporttext'],array('<a href="viewplayer.php?id='.$myself['id'].'">'.$myself['userName'].'</a>','<a href="viewguild.php?id='.$myGuild['id'].'">'.$myGuild['guildName'].'</a>',$language[$_POST['stance']]));
}
else
{
	// the same stance set so set and accept report
	$reportTitle=xprintf($language['diplomacyrequitedtitle'],array($myGuild['guildName']));
	$reportText=xprintf($language['diplomacyrequitedtext'],array('<a href="viewplayer.php?id='.$myself['id'].'">'.$myself['userName'].'</a>','<a href="viewguild.php?id='.$myGuild['id'].'">'.$myGuild['guildName'].'</a>'));
}
$reportValues=array();
if (count($diplomatsInTheirGuild)>0)
{
	foreach($diplomatsInTheirGuild as $key=>$value)
	{
		$reportValues[]=sqlvprintf("({0},{1},{2},NOW(),MD5(RAND()))",array($value['id'],$reportTitle,$reportText));
	}
	runEscapedQuery("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ".implode(',',$reportValues));
}

jumpTo('guildops.php?cmd=diplomacy');



?>
