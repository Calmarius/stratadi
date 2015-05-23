<?php

require_once('userworkerphps.php');

bounceSessionOver();
$myId=$_SESSION['userId'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId='{1}') AND (permission='diplomacy')",array($myId)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($myId)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$myself=mysql_fetch_assoc($r);

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_diplomacy WHERE (id='{1}')",array($_GET['id'])));
if (mysql_num_rows($r)>0) 
{
	$diplomacyEntry=mysql_fetch_assoc($r);
	doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_diplomacy WHERE (id='{1}') AND (guildId='{2}')",array($_GET['id'],$myself['guildId'])));
	doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (x,y,eventTime,type,guildId,recipientGuildId) VALUES (0,0,NOW(),'diplomacychanged','{1}','{2}')",array($diplomacyEntry['toGuildId'],$myself['guildId'])));

	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guilds WHERE (id='{1}')",array($diplomacyEntry['toGuildId'])),'jumpErrorPage');
	if (mysql_num_rows($r)>0) // only continue if the recipientGuild exists
	{
		$guild=mysql_fetch_assoc($r);
		$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guilds WHERE (id='{1}')",array($myself['guildId'])),'jumpErrorPage');
		if (mysql_num_rows($r)==0) jumpErrorPage($language['guildnotexist']);
		$myGuild=mysql_fetch_assoc($r);

		// notify users with diplomacy right about the change
		$r=doMySqlQuery(
			sqlPrintf(
			"
				SELECT u.* 
				FROM wtfb2_users u
				INNER JOIN wtfb2_guildpermissions p ON (p.userid=u.id)
				INNER JOIN wtfb2_guilds g ON (u.guildId=g.id)
				WHERE (g.id={1}) AND (p.permission='diplomacy')
			",array($guild['id'])
			)
		);
		$diplomatsInGuild=array();
		while($row=mysql_fetch_assoc($r))
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
				$reportValues[]=sqlPrintf("('{1}','{2}','{3}',NOW(),MD5(RAND()))",array($value['id'],$reportTitle,$reportText));
			}
			doMySqlQuery("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ".implode(',',$reportValues)); // make 100% sure that array has escaped values!
		}
	}
}

jumpTo('guildops.php?cmd=diplomacy');

?>
