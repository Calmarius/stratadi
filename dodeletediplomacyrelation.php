<?php

require_once('userworkerphps.php');

bounceSessionOver();
$myId=$_SESSION['userId'];
$r=runEscapedQuery("SELECT * FROM wtfb2_guildpermissions WHERE (userId={0}) AND (permission='diplomacy')",$myId);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$myId);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$myself=$r[0][0];

$r=runEscapedQuery("SELECT * FROM wtfb2_diplomacy WHERE (id={0})",$_GET['id']);
if (!isEmptyResult($r))
{
	$diplomacyEntry=$r[0][0];
	runEscapedQuery("DELETE FROM wtfb2_diplomacy WHERE (id={0}) AND (guildId={1})",$_GET['id'],$myself['guildId']);
	runEscapedQuery("INSERT INTO wtfb2_worldevents (x,y,eventTime,type,guildId,recipientGuildId) VALUES (0,0,NOW(),'diplomacychanged',{0},{1})",$diplomacyEntry['toGuildId'],$myself['guildId']);

	$r=runEscapedQuery("SELECT * FROM wtfb2_guilds WHERE (id={0})",$diplomacyEntry['toGuildId']);
	if (!isEmptyResult($r)) // only continue if the recipientGuild exists
	{
		$guild=$r[0][0];
		$r=runEscapedQuery("SELECT * FROM wtfb2_guilds WHERE (id={0})",$myself['guildId']);
		if (isEmptyResult($r)) jumpErrorPage($language['guildnotexist']);
		$myGuild=$r[0][0];

		// notify users with diplomacy right about the change
		$r=runEscapedQuery(
			"
				SELECT u.*
				FROM wtfb2_users u
				INNER JOIN wtfb2_guildpermissions p ON (p.userid=u.id)
				INNER JOIN wtfb2_guilds g ON (u.guildId=g.id)
				WHERE (g.id={0}) AND (p.permission='diplomacy')
			",$guild['id']
		);
		$diplomatsInGuild=array();
		foreach ($r[0] as $row)
		{
			$diplomatsInGuild[]=$row;
		}
		// different or no stance set so set a diplomacy invitation
		$reportTitle=xprintf($language['diplomacybrokenreporttitle'],array($myGuild['guildName']));
		$reportText=xprintf($language['diplomacybrokenreporttext'],array('<a href="viewplayer.php?id='.$myself['id'].'">'.$myself['userName'].'</a>','<a href="viewguild.php?id='.$myGuild['id'].'">'.$myGuild['guildName'].'</a>'));
		// send out reports
		$reportValues=array();
		if (count($diplomatsInGuild)>0)
		{
			foreach($diplomatsInGuild as $key=>$value)
			{
				$reportValues[]=sqlvprintf("({0},{1},{2},NOW(),MD5(RAND()))",array($value['id'],$reportTitle,$reportText));
			}
			runEscapedQuery("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ".implode(',',$reportValues)); // make 100% sure that array has escaped values!
		}
	}
}

jumpTo('guildops.php?cmd=diplomacy');

?>
