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
	$q=sqlvprintf("SELECT * FROM wtfb2_users WHERE (id={0})",array($_SESSION['userId']));
	$userSet=runEscapedQuery($q);
}
$sessionOver=$myId==0;
if (!$sessionOver) $sessionOver=isEmptyResult($r);
if (!$sessionOver)
{
	if (isset($_SESSION['asdeputy']) && $_SESSION['asdeputy'])
	{
		$r=runEscapedQuery("SELECT * FROM wtfb2_deputies WHERE (sponsorId={0}) AND (deputyId={1})",$_SESSION['userId'],$_SESSION['returnUserId']);
		$sessionOver=isEmptyResult($r);
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
$me=$userSet[0][0];

// log ip address
if (!isset($_SESSION['returnUserId']))
{
	$ip=$_SERVER['REMOTE_ADDR'];
	runEscapedQuery("UPDATE wtfb2_iplog SET useCount=useCount+1, lastUsed=NOW() WHERE (ip={0}) AND (userId={1})",$ip,$me['id']);
	if (getAffectedRowCount()==0)
	{
		runEscapedQuery("INSERT INTO wtfb2_iplog (userId,lastUsed,ip) VALUES ({0},NOW(),{1})",$me['id'],$ip);
	}
}



// get diplomacy
$allies=array();
$peace=array();
$enemies=array();
if ($me['guildId']!='')
{
	$q=sqlvprintf("SELECT * FROM wtfb2_diplomacy WHERE (guildId={0})",array($me['guildId']));
	$r=runEscapedQuery($q);
	if (!isEmptyResult($r))
	foreach ($r[0] as $row)
	{
		if ($row['attitude']=='ally') $allies[]=$row;
		if ($row['attitude']=='peace') $peace[]=$row;
		if ($row['attitude']=='war') $enemies[]=$row;
	}
}

$newMessages=0;
$r=runEscapedQuery("SELECT COUNT(*) AS newMessages FROM wtfb2_threadlinks WHERE (userId={0}) AND (`read`=0)",$_SESSION['userId']);
$newM=$r[0][0];
$newMessages=$newM['newMessages'];
$r=runEscapedQuery("SELECT COUNT(*) AS newReports FROM wtfb2_reports WHERE (recipientId={0}) AND (isRead=0)",$_SESSION['userId']);
$newR=$r[0][0];
$newReports=$newR['newReports'];
$r=runEscapedQuery(
	"
		EXPLAIN SELECT e.*,UNIX_TIMESTAMP(eventTime) AS timestamp,u.userName,g.guildName,v.userName AS recipientPlayer,h.guildName AS recipientGuild
		FROM wtfb2_worldevents e
		LEFT JOIN wtfb2_guilds g ON (e.guildId=g.id)
		LEFT JOIN wtfb2_users u ON (e.playerId=u.id)
		LEFT JOIN wtfb2_users v ON (e.recipientId=v.id)
		LEFT JOIN wtfb2_guilds h ON (e.recipientGuildId=h.id)
		WHERE (eventTime>={0}) AND (((recipientId IS NULL) AND (recipientGuildId IS NULL)) OR (recipientId={1}) OR (recipientGuildId={2}))
	",$me['lastLoaded'],$_SESSION['userId'],$me['guildId']
);

$r=runEscapedQuery(
	"
		SELECT e.*,UNIX_TIMESTAMP(eventTime) AS timestamp,u.userName,g.guildName,v.userName AS recipientPlayer,h.guildName AS recipientGuild
		FROM wtfb2_worldevents e
		LEFT JOIN wtfb2_guilds g ON (e.guildId=g.id)
		LEFT JOIN wtfb2_users u ON (e.playerId=u.id)
		LEFT JOIN wtfb2_users v ON (e.recipientId=v.id)
		LEFT JOIN wtfb2_guilds h ON (e.recipientGuildId=h.id)
		WHERE (eventTime>={0}) AND (((recipientId IS NULL) AND (recipientGuildId IS NULL)) OR (recipientId={1}) OR (recipientGuildId={2}))
	",$me['lastLoaded'],$_SESSION['userId'],$me['guildId']
);
$worldEvents=array();
runEscapedQuery("DELETE FROM wtfb2_worldevents WHERE (recipientId={0})",$_SESSION['userId']);
foreach ($r[0] as $row)
{
	$worldEvents[]=$row;
}
$r=runEscapedQuery("SELECT * FROM wtfb2_heroes WHERE (ownerId={0})",$_SESSION['userId']);
if (!isEmptyResult($r))
	$myHero=$r[0][0];
else
	$myHero=array('id'=>-1,'inVillage'=>0);

updatePlayer($_SESSION['userId']);

runEscapedQuery(
	"
		UPDATE wtfb2_users
		SET
			lastLoaded=NOW()
		WHERE (id={0})
	",$_SESSION['userId']
);


$q=sqlvprintf("SELECT *,UNIX_TIMESTAMP(NOW()) AS nowstamp FROM wtfb2_users WHERE (id={0})",array($_SESSION['userId']));
$r=runEscapedQuery($q);
$me=$r[0][0];

$buildingLevelNames = array();
foreach ($config['units'] as $unitDesc)
{
    $buildingLevelNames[] = $config['buildings'][$unitDesc['trainedAt']]['buildingLevelDbName'];
}

$buildingLevelSet = implode(",", $buildingLevelNames);
$r = runEscapedQuery("SELECT $buildingLevelSet FROM wtfb2_villages WHERE (ownerId={0})", $_SESSION['userId']);
$maxTrainingPerHour = array();
foreach ($config['units'] as $key => $unitDesc)
{
    $buildingName = $unitDesc['trainedAt'];
    $buildingDesc = $config['buildings'][$buildingName];
    $maxTrainingPerHour[$key] = 0;
    foreach ($r[0] as $row)
    {
        $maxTrainingPerHour[$key] +=
            1/$buildingDesc['timeReductionFunction']($row[$buildingDesc['buildingLevelDbName']])*3600/$unitDesc['trainingTime']*$config['serverSpeed'];
    }
}

$r=runEscapedQuery(
	"
		SELECT
			IF((e.eventType IN ('attack','raid','recon')) AND (v2.ownerId=u.id),'incomingattack',e.eventType) AS eventType,
			IF((e.eventType IN ('attack','raid','recon')) AND (v2.ownerId=u.id),'incomingattack',e.eventType) AS type,
		SUM( ((v.ownerId=u.Id) AND (e.eventType<>'return')) OR (e.heroId={0})) AS outgoing,SUM(v2.ownerId=u.id) AS incoming
		FROM wtfb2_events e
		LEFT JOIN wtfb2_villages v ON (v.id=e.launcherVillage)
		LEFT JOIN wtfb2_villages v2 ON (v2.id=e.destinationVillage)
		LEFT JOIN wtfb2_heroes h ON (h.id=e.heroId)
		LEFT JOIN wtfb2_users u ON ((v.ownerId=u.id) OR (v2.ownerId=u.id) OR (h.ownerId=u.id))
		WHERE (u.id={1})
		GROUP BY type
		HAVING (outgoing>0) OR (incoming>0)
    ",$myHero['id'],$_SESSION['userId']
);
$eventSummary=array();
foreach ($r[0] as $row)
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
echo '<maxTrainingPerHour>';
foreach ($maxTrainingPerHour as $key => $value)
{
    echo "<$key>$value</$key>";
}
echo '</maxTrainingPerHour>';
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
echo '<nowstamp>'.htmlspecialchars($me['nowstamp'],ENT_COMPAT,"utf-8").'</nowstamp>';
echo '</playerinfo>';

?>
