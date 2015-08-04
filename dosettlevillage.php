<?php

require_once('userworkerphps.php');
require_once('villageupdater.php');

if (!isset($_SESSION['userId'])) jumpTo('login.php');

if ($_SESSION['permission']=='inactive') jumpTo('activate.php');

if (villageCount($_SESSION['userId'])>0) jumpTo('game.php');

function settleFirstVillage($userId)
{
	global $config;
	global $language;
	$runSeconds=-secondsToStart();
	if ($runSeconds<0) $runSeconds=0;
	$minRadius=sqrt(($runSeconds/86400*$config[newVillageAreaIncreasePerDay])/3.1415926536);
	$maxRadius=$config['newVillageAreaRadius']+$minRadius;
//	logText("Settling happened: runSeconds: $runSeconds, minRadius: $minRadius, maxRadius: $maxRadius");
	for($i=0;$i<$config['maxSettleAttempts'];$i++)
	{
		$radius=rand($minRadius,$maxRadius);
		$angle=rand(0,359);
		$rad=deg2rad($angle);
		$x=(int)(cos($rad)*$radius);
		$y=(int)(sin($rad)*$radius);
		$q=sqlvprintf("SELECT * FROM wtfb2_villages WHERE (x={0}) AND (y={1})",array($x,$y));
		$r=runEscapedQuery($q);
		if (isEmptyResult($r))
		{
			$q=sqlvprintf("INSERT INTO wtfb2_villages (ownerId,villageName,x,y,lastUpdate) VALUES ({0},{1},{2},{3},NOW())",array($userId,$language['newvillage'],$x,$y));
			$r=runEscapedQuery($q);
			$insertId= getLastInsertId();
			runEscapedQuery("INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ({0},{1},NOW(),'settle')",$x,$y);
			runEscapedQuery("UPDATE wtfb2_heroes SET inVillage={0},offense=0,defense=0 WHERE (ownerId={1})",$insertId,$userId);
			runEscapedQuery("UPDATE wtfb2_users SET expansionPoints=0,gold={0},lastUpdate=NOW(),regDate=NOW() WHERE (id={1})",$config['startGold'],$userId); // reset player.
			runEscapedQuery("INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ({0},{1},NOW(),'settle')",$x,$y);
			updateAllVillages($userId);
			updatePlayer($userId);
			jumpTo("game.php?x=$x&y=$y");
		}
		
	}
	jumpErrorPage($language['toomanyvillagescantspawn']);
	
}

settleFirstVillage($_SESSION['userId']);

?>
