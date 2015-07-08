<?php

require_once('userworkerphps.php');

bounceSessionOver();
$myId=$_SESSION['userId'];
$r=runEscapedQuery("SELECT * FROM wtfb2_guildpermissions WHERE (userId={0}) AND (permission='grantrights')",$myId);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
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
if (count($players) == 0)
{
    jumpTo('guildops.php?cmd=grantrights');
    die();
}
// first revoke all rights of the selected players
$userSet='('.implode(',',$players).')';
runEscapedQuery("DELETE FROM wtfb2_guildpermissions WHERE (userId IN $userSet)");
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
	runEscapedQuery($q); // escaped btw
}

jumpTo('guildops.php?cmd=grantrights');

?>
