<?php

require_once('eventprocessor.php');
require_once('userworkerphps.php');

$eventId=(int)$_GET['id'];
$myId=$_SESSION['userId'];
$r=doMySqlQuery(
	sqlPrintf(
	"
	SELECT e.*
	FROM wtfb2_events e
	INNER JOIN wtfb2_villages v ON (v.id=e.launcherVillage)
	LEFT JOIN wtfb2_heroes h ON (h.id=e.heroId)
	WHERE (((v.ownerId='{1}') AND (h.id IS NULL)) OR (h.ownerId='{1}')) AND (e.id='{2}') AND (e.eventType!='return')
	",array($myId,$eventId)
	)
,'jumpErrorPage');

if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$event=mysql_fetch_assoc($r);

$newEvent=array();
// add quotes
foreach($event as $key=>$value)
{
	$newEvent[$key]="'$value'";
}
// set event type
unset($newEvent['id']); // don't set id.
$newEvent['eventType']="'return'";
// set event times
$newEvent['launchedAt']="NOW()";
$newEvent['happensAt']=$newEvent['estimatedTime']="TIMESTAMPADD(SECOND,TIMESTAMPDIFF(SECOND,'${event['launchedAt']}',NOW()),NOW())";
// exchange launcher and destination villages
$newEvent['launcherVillage']=$event['destinationVillage'];
$newEvent['destinationVillage']=$event['launcherVillage'];
// every other thing remains unchanged

// on settle event give us back our expansion point
if ($event['eventType']=='settle')
{
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=expansionPoints+1 WHERE (id='{1}')",array($myId)),'jumpErrorPage');
}

// finally we post that event.
doMySqlQuery("INSERT INTO wtfb2_events (".implode(',',array_keys($newEvent)).") VALUES (".implode(',',$newEvent).")",'jumpErrorPage'); // insecure, be careful.
// and delete the old
doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_events WHERE (id='{1}')",array($eventId)),'jumpErrorPage');

$_SESSION['successtitle']=$language['eventsuccessfullycancelled'];
$_SESSION['successcontent']='';

jumpTo(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'success.php')

?>
