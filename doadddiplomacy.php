<?php
require_once("userworkerphps.php");

bounceSessionOver();
$myId=$_SESSION['userId'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId='{1}') AND (permission='diplomacy')",array($myId)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($myId)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$myself=mysql_fetch_assoc($r);
$guildId=$_POST['guildid'];
$guildName=$_POST['guildname'];

//jumpErrorPage($language['accessdenied']);


$guild;
if (is_numeric($guildId))
{
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guilds WHERE (id='{1}')",array($guildId)),'jumpErrorPage');
	if (mysql_num_rows($r)==0) jumpErrorPage($language['guildnotexist']);
}
else
{
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guilds WHERE (guildName='{1}')",array($guildName)),'jumpErrorPage');
	if (mysql_num_rows($r)==0) jumpErrorPage($language['guildnotexist']);
}
$guild=mysql_fetch_assoc($r);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guilds WHERE (id='{1}')",array($myself['guildId'])),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['guildnotexist']);
$myGuild=mysql_fetch_assoc($r);

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_diplomacy WHERE (guildId='{1}') AND (toGuildId='{2}')",array($myself['guildId'],$guild['id'])),'jumpErrorPage');
if (mysql_fetch_assoc($r)>0) jumpErrorPage($language['diplomaticstancealreadyexist']);
doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_diplomacy (attitude,guildId,toGuildId) VALUES ('{1}','{2}','{3}')",array($_POST['stance'],$myself['guildId'],$guild['id'])),'jumpErrorPage');
doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (x,y,eventTime,type,guildId,recipientGuildId) VALUES (0,0,NOW(),'diplomacychanged','{1}','{2}')",array($guild['id']),$myself['guildId']),'jumpErrorPage'); // send event ourselves.
// notify users with diplomacy right about the change
$r=doMySqlQuery(
	sqlPrintf(
		"
			SELECT u.* 
			FROM wtfb2_users u
			INNER JOIN wtfb2_guildpermissions p ON (p.userid=u.id)
			INNER JOIN wtfb2_guilds g ON (u.guildId=g.id)
			WHERE (g.id='{1}') AND (p.permission='diplomacy')
		",
		array($guild['id'])
	),
	'jumpErrorPage'
);
$diplomatsInGuild=array();
while($row=mysql_fetch_assoc($r))
{
	$diplomatsInGuild[]=$row;
}
// find out what diplomatic stance set by the recipient guild
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_diplomacy WHERE ((attitude='{1}') AND (guildId='{2}') AND (toGuildId='{3}'))",array($_POST['stance'],$guild['id'],$myself['guildId'])));
$reportTitle='';
$reportText='';
if (mysql_num_rows($r)==0)
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
if (count($diplomatsInGuild)>0)
{
	foreach($diplomatsInGuild as $key=>$value)
	{
		$reportValues[]=sqlPrintf("('{1}','{2}','{3}',NOW(),MD5(RAND()))",array($value['id'],$reportTitle,$reportText));
	}
	doMySqlQuery("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ".implode(',',$reportValues));
}

jumpTo('guildops.php?cmd=diplomacy');



?>
