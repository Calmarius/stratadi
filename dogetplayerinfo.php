<?php

require_once('userworkerphps.php');

require_once("eventprocessor.php");
require_once("nightbonus.php");

header('Content-type: application/xml; charset=utf-8');

// select me
$myId=(int)@$_SESSION['userId'];
$r;
if ($myId!=0)
{
	$q=sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($_SESSION['userId']));
	$r=doMySqlQuery($q);
}
$sessionOver=$myId==0;
if (!$sessionOver) $sessionOver=mysql_num_rows($r)==0;
$me=mysql_fetch_assoc($r);
if (!$sessionOver)
{
	if (isset($_SESSION['asdeputy']) && $_SESSION['asdeputy'])
	{
		$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_deputies WHERE (sponsorId='{1}') AND (deputyId='{2}')",array($_SESSION['userId'],$_SESSION['returnUserId'])));
		$sessionOver=mysql_num_rows($r)==0;
	}
}


if ($sessionOver)
{
	echo '<?xml version="1.0" encoding="UTF-8" ?>';
	echo '<playerinfo>';
	echo '<sessionover>0</sessionover>';
	echo '</playerinfo>';

	die();
}

// log ip address
if (!isset($_SESSION['returnUserId']))
{
	$ip=$_SERVER['REMOTE_ADDR'];
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_iplog SET useCount=useCount+1, lastUsed=NOW() WHERE (ip='{1}') AND (userId='{2}')",array($ip,$me['id'])));
	if (mysql_affected_rows()==0)
	{
		doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_iplog (userId,lastUsed,ip) VALUES ('{1}',NOW(),'{2}')",array($me['id'],$ip)));
	}
}



// get diplomacy
$allies=array();
$peace=array();
$enemies=array();
if ($me['guildId']!='')
{
	$q=sqlPrintf("SELECT * FROM wtfb2_diplomacy WHERE (guildId='{1}')",array($me['guildId']));
	$r=doMySqlQuery($q);
	if (mysql_num_rows($r)>0)
	while($row=mysql_fetch_assoc($r))
	{
		if ($row['attitude']=='ally') $allies[]=$row;
		if ($row['attitude']=='peace') $peace[]=$row;
		if ($row['attitude']=='war') $enemies[]=$row;
	}
}

$newMessages=0;
$r=doMySqlQuery(sqlPrintf("SELECT COUNT(*) AS newMessages FROM wtfb2_threadlinks WHERE (userId='{1}') AND (`read`=0)",array($_SESSION['userId'])));
$newM=mysql_fetch_assoc($r);
$newMessages=$newM['newMessages'];
$r=doMySqlQuery(sqlPrintf("SELECT COUNT(*) AS newReports FROM wtfb2_reports WHERE (recipientId='{1}') AND (isRead=0)",array($_SESSION['userId'])));
$newR=mysql_fetch_assoc($r);
$newReports=$newR['newReports'];
$r=doMySqlQuery(
	sqlPrintf
	(
		"
			EXPLAIN SELECT e.*,UNIX_TIMESTAMP(eventTime) AS timestamp,u.userName,g.guildName,v.userName AS recipientPlayer,h.guildName AS recipientGuild
			FROM wtfb2_worldevents e
			LEFT JOIN wtfb2_guilds g ON (e.guildId=g.id)
			LEFT JOIN wtfb2_users u ON (e.playerId=u.id)
			LEFT JOIN wtfb2_users v ON (e.recipientId=v.id)
			LEFT JOIN wtfb2_guilds h ON (e.recipientGuildId=h.id)
			WHERE (eventTime>='{1}') AND (((recipientId IS NULL) AND (recipientGuildId IS NULL)) OR (recipientId='{2}') OR (recipientGuildId='{3}'))
		",array($me['lastLoaded'],$_SESSION['userId'],$me['guildId'])
	)
);

$r=doMySqlQuery(
	sqlPrintf
	(
		"
			SELECT e.*,UNIX_TIMESTAMP(eventTime) AS timestamp,u.userName,g.guildName,v.userName AS recipientPlayer,h.guildName AS recipientGuild
			FROM wtfb2_worldevents e
			LEFT JOIN wtfb2_guilds g ON (e.guildId=g.id)
			LEFT JOIN wtfb2_users u ON (e.playerId=u.id)
			LEFT JOIN wtfb2_users v ON (e.recipientId=v.id)
			LEFT JOIN wtfb2_guilds h ON (e.recipientGuildId=h.id)
			WHERE (eventTime>='{1}') AND (((recipientId IS NULL) AND (recipientGuildId IS NULL)) OR (recipientId='{2}') OR (recipientGuildId='{3}'))
		",array($me['lastLoaded'],$_SESSION['userId'],$me['guildId'])
	)
);
$worldEvents=array();
doMySqlQuery(
	sqlPrintf
	(
		"DELETE FROM wtfb2_worldevents WHERE (recipientId='{1}')",array($_SESSION['userId'])
	)
);
while($row=mysql_fetch_assoc($r))
{
	$worldEvents[]=$row;
}
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_heroes WHERE (ownerId='{1}')",array($_SESSION['userId'])));
if (mysql_num_rows($r)>0)
	$myHero=mysql_fetch_assoc($r);
else
	$myHero=array('id'=>-1,'inVillage'=>0);

updatePlayer($_SESSION['userId']);

doMySqlQuery(
	sqlPrintf(
	"
		UPDATE wtfb2_users
		SET
			lastLoaded=NOW()
		WHERE (id='{1}')
	",array($_SESSION['userId'])
	)
);


$q=sqlPrintf("SELECT *,UNIX_TIMESTAMP(NOW()) AS nowstamp FROM wtfb2_users WHERE (id='{1}')",array($_SESSION['userId']));
$r=doMySqlQuery($q);
$me=mysql_fetch_assoc($r);


$r=doMySqlQuery(
	sqlPrintf(
		"
		SELECT
			IF((e.eventType IN ('attack','raid','recon')) AND (v2.ownerId=u.id),'incomingattack',e.eventType) AS eventType,
			IF((e.eventType IN ('attack','raid','recon')) AND (v2.ownerId=u.id),'incomingattack',e.eventType) AS type,
		SUM( ((v.ownerId=u.Id) AND (e.eventType<>'return')) OR (e.heroId='{1}')) AS outgoing,SUM(v2.ownerId=u.id) AS incoming
		FROM wtfb2_events e
		LEFT JOIN wtfb2_villages v ON (v.id=e.launcherVillage) 
		LEFT JOIN wtfb2_villages v2 ON (v2.id=e.destinationVillage) 
		LEFT JOIN wtfb2_heroes h ON (h.id=e.heroId)
		LEFT JOIN wtfb2_users u ON ((v.ownerId=u.id) OR (v2.ownerId=u.id) OR (h.ownerId=u.id))
		WHERE (u.id='{2}')
		GROUP BY type
		HAVING (outgoing>0) OR (incoming>0)
		",array($myHero['id'],$_SESSION['userId'])
	)
);
$eventSummary=array();
while($row=mysql_fetch_assoc($r))
{
	$eventSummary[]=$row;
}

echo '<?xml version="1.0" encoding="UTF-8" ?>';
echo '<playerinfo>';
echo '<gold>'.htmlspecialchars($me['gold'],ENT_COMPAT,"utf-8").'</gold>';
echo '<goldProduction>'.htmlspecialchars($me['goldProduction'],ENT_COMPAT,"utf-8").'</goldProduction>';
echo '<id>'.htmlspecialchars($me['id'],ENT_COMPAT,"utf-8").'</id>';
echo '<permission>'.htmlspecialchars(@$me['permission'],ENT_COMPAT,"utf-8").'</permission>';
echo '<expansionPoints>'.htmlspecialchars($me['expansionPoints'],ENT_COMPAT,"utf-8").'</expansionPoints>';
echo '<guildId>'.$me['guildId'].'</guildId>';
echo '<diplomacy>';
echo '<allies>';
foreach($allies as $key => $value)
{
	echo '<guildId>'.$value['toGuildId'].'</guildId>';
}
echo '</allies>';
echo '<peace>';
foreach($peace as $key => $value)
{
	echo '<guildId>'.$value['toGuildId'].'</guildId>';
}
echo '</peace>';
echo '<enemies>';
foreach($enemies as $key => $value)
{
	echo '<guildId>'.$value['toGuildId'].'</guildId>';
}
echo '</enemies>';
echo'</diplomacy>';
echo "<newMessages>$newMessages</newMessages>";
echo "<newReports>$newReports</newReports>";
echo '<hero>';
foreach($myHero as $key=>$value)
{
	echo "<$key>$value</$key>";
}
echo '</hero>';
echo '<worldEvents>';
foreach($worldEvents as $key=>$worldEvent)
{
	echo '<worldEvent>';
	echo "<x>${worldEvent['x']}</x>";
	echo "<y>${worldEvent['y']}</y>";
	echo "<playerId>${worldEvent['playerId']}</playerId>";
	echo "<guildId>${worldEvent['guildId']}</guildId>";
	echo "<eventTime>${worldEvent['eventTime']}</eventTime>";
	echo "<timestamp>${worldEvent['timestamp']}</timestamp>";
	echo "<playerName>${worldEvent['userName']}</playerName>";
	echo "<guildName>${worldEvent['guildName']}</guildName>";
	echo "<needFullRefresh>${worldEvent['needFullRefresh']}</needFullRefresh>";
	echo "<recipientGuildId>${worldEvent['recipientGuildId']}</recipientGuildId>";
	echo "<recipientId>${worldEvent['recipientId']}</recipientId>";
	echo "<recipientPlayer>${worldEvent['recipientPlayer']}</recipientPlayer>";
	echo "<recipientGuild>${worldEvent['recipientGuild']}</recipientGuild>";
	echo "<type>${worldEvent['type']}</type>";
	echo '</worldEvent>';
}
echo '</worldEvents>';
echo '<eventSummary>';
foreach($eventSummary as $key=>$event)
{
	echo '<event>';
	echo "<eventName>".htmlspecialchars($language[$config['operations'][$event['eventType']]['langName']],ENT_COMPAT,"utf-8")."</eventName>";
	echo "<eventType>".htmlspecialchars($event['eventType'],ENT_COMPAT,"utf-8")."</eventType>";
	echo "<incoming>".htmlspecialchars($event['incoming'],ENT_COMPAT,"utf-8")."</incoming>";
	echo "<outgoing>".htmlspecialchars($event['outgoing'],ENT_COMPAT,"utf-8")."</outgoing>";
	echo '</event>';
}
echo '</eventSummary>';
$nbInfo=getNightBonusInfo(time());
echo '<nightBonus>'.$nbInfo['bonus'].'</nightBonus>';
echo '<nightBonusIndex>'.$nbInfo['index'].'</nightBonusIndex>';
//echo '<nightBonusIndex>-14</nightBonusIndex>';
/*echo '<events>';
$operations=$config['operations'];
foreach($events as $key=>$event)
{
	$units=array();
	foreach($config['units'] as $key=>$value)
	{
		$countName=$value['countDbName'];
		$units[]=xprintf($language['amountform'],array($event[$countName],$language[$value['languageEntry']]));
	}
	echo '<event>';
	$eventDescriptor=$operations[$event['eventType']];
	echo htmlspecialchars(xprintf($language[$eventDescriptor['langDesc']],
		array(
			$language[$eventDescriptor['langName']],
			$event['sender'],
			$event['destination'],
			implode(',',$units),
			'('.$event['targetX'].';'.$event['targetY'].')',
			$event['gold'],
			$event['happensAt']
		)
	));
	echo '</event>';
}
echo '</events>';*/
echo '<nowstamp>'.htmlspecialchars($me['nowstamp'],ENT_COMPAT,"utf-8").'</nowstamp>';
echo '</playerinfo>';

?>
