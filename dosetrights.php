<?php

require_once('userworkerphps.php');

bounceSessionOver();
$myId=$_SESSION['userId'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId='{1}') AND (permission='grantrights')",array($myId)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$players=$rights=array();
if (isset($_POST['players']))
{
	foreach($_POST['players'] as $key=>$value)
	{
		$players[]=sqlPrintf("'{1}'",array($value));
	}
}
if (isset($_POST['rights']))
{
	foreach($_POST['rights'] as $key=>$value)
	{
		$rights[]=sqlPrintf("'{1}'",array($value));
	}
}
// first revoke all rights of the selected players
$userSet='('.implode(',',$players).')';
doMySqlQuery("DELETE FROM wtfb2_guildpermissions WHERE (userId IN $userSet)",'jumpErrorPage');
// then add new rights
$newRecords=array();
$newParts=array();
$c=0;
$maxRecords=(int)$config['maxInsertRecordsPerQuery'];
foreach($players as $key=>$player)
{
	foreach($rights as $key=>$right)
	{
		$newRecords[]="($player,$right)"; // it's escaped now
		$c++;
		if ($c>$maxRecords)
		{
			$c=0;
			$newParts[]=$newRecords;
			$newRecords=array();
		}
	}
}
$newParts[]=$newRecords;

foreach($newParts as $key=>$value)
{
	if (count($value)==0) continue;
	$q='INSERT INTO wtfb2_guildpermissions (userId,permission) VALUES '.implode(',',$value);
	doMySqlQuery($q); // escaped btw
}

jumpTo('guildops.php?cmd=grantrights');

?>
