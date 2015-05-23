<?php

//header('content-type: text/plain; charset=UTF-8');

require_once('userworkerphps.php');

function fetchAllVillagesWithScore($playerId)
{
	global $config;
	$bLevelNames=array();
	foreach($config['buildings'] as $key=>$value)
	{
		$buildingDescriptor=$value;
		$bLevelnames[]=$buildingDescriptor['buildingLevelDbName'];
	}
	$scoreQuery=$config['villageScoreFunction']($bLevelnames);
	$q=sqlPrintf("SELECT *,$scoreQuery AS score  FROM wtfb2_villages WHERE (ownerId='{1}')",array($playerId));
	$r=doMySqlQuery($q);
	$villages=array();
	while($row=mysql_fetch_assoc($r))
	{
		$villages[]=$row;
	}
	return $villages;
}

function recalculatePlayerInfo($playerId)
{
	global $config;
	$villages=fetchAllVillagesWithScore($playerId);
	updateAggregatedPlayerInfo($playerId,$villages);
}

function updateAggregatedPlayerInfo($playerId,$villages)
{
	global $config;
	$count=count($villages);
	$goldProduction=0;
	$score=0;
	$goldProducer=$config['buildings'][$config['goldProducer']];
	$goldProducerLevelName=$goldProducer['buildingLevelDbName'];
	foreach($villages as $key=>$value)
	{
		$goldProduction+=$goldProducer['goldProductionSpeedFunction']($value[$goldProducerLevelName])*$config['serverSpeed'];
		$score+=$value['score'];
	}
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET goldProduction='{1}',villageCount='{2}',totalScore='{3}' WHERE (id='{4}')",array($goldProduction,$count,$score,$playerId)));
}

function updateAllVillages($playerId,$toWhen=null)
{
	global $config;
	$when='NOW()';
	if ($toWhen!=null) $when="'$toWhen'";
	$r=doMySqlQuery(sqlPrintf("SELECT TIMESTAMPDIFF(SECOND,lastMassVillageUpdate,$when) AS lastMassUpdate FROM wtfb2_users WHERE (id='{1}')",array($playerId)));
	if (mysql_num_rows($r)<1) return;
	$a=mysql_fetch_assoc($r);
//	if ((int)$a['lastMassUpdate']<(int)$config['forceUpdatePeriod']) return;
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET lastMassVillageUpdate=$when WHERE (id='{1}')",array($playerId)));
	$villages=fetchAllVillagesWithScore($playerId);
	foreach($villages as $key=>$value)
	{
		updateVillage($value['id'],$toWhen);
	}	
	updateAggregatedPlayerInfo($playerId,$villages);
}

function updatePlayer($playerId,$toWhen=null)
{
	$when='NOW()';
	if ($toWhen!=null) $when="'$toWhen'";
	global $config;
	doMySqlQuery
	(
		sqlPrintf
		(
			"UPDATE wtfb2_users
			SET expansionPoints=expansionPoints+TIMESTAMPDIFF(SECOND,lastUpdate,$when)/86400*{2},gold=gold+goldProduction*TIMESTAMPDIFF(SECOND,lastUpdate,$when)/3600, lastUpdate=$when WHERE (id='{1}'
			)",array($playerId,$config['serverSpeed'])
		)
	);
}

function updateVillage($villageId,$toWhen=null)
{
	global $config;
	global $language;
	$when='NOW()';
	if ($toWhen!=null) $when="'$toWhen'";
	$q=sqlPrintf("SELECT TIMESTAMPDIFF(SECOND,lastUpdate,$when) AS secsElapsed,wtfb2_villages.* FROM wtfb2_villages WHERE (id='{1}')",array($villageId));
	$r=doMySqlQuery($q);
	if (mysql_num_rows($r)==0) return;
	$a=mysql_fetch_assoc($r);

	$secsElapsed=$a['secsElapsed']*$config['serverSpeed']; // more secs elapsed with speed factor
	$trainedCounts=array();
	$trainingTimePerUnit;
	foreach($config['units'] as $key=>$value)
	{
		$trainedCounts[$key]=0;
		if (!isset($a[$value['trainingDbName']])) die('Configuration failure: unit.trainingDbName is not exist. Unit name was: '.$key);
		if (!isset($a[$value['countDbName']])) die('Configuration failure: unit.countDbName is not exist. Unit name was: '.$key);
		if (!isset($config['buildings'][$value['trainedAt']])) die('Configuration failure: buildings[unit.trainedAt] is not exist. Unit name was: '.$key.', the index was: '.$value['trainedAt']);	
		$training=$a[$value['trainingDbName']];
		$count=$a[$value['countDbName']];
		$building=$config['buildings'][$value['trainedAt']];
		$buildingLevelName=$building['buildingLevelDbName'];
		if (!isset($a[$buildingLevelName])) die('Configuration failure: building.buildingLevelDbName is not exist. Unit name was: '.$key.', the index was:'.$buildingLevelName);
		$trainingTimePerUnit=$value['trainingTime']*$building['timeReductionFunction']($a[$buildingLevelName]);
		$wouldBeNewUnitCount=$secsElapsed/$trainingTimePerUnit;
		$unitsToAdd=0;
		$trainingComplete=false;
		if ($wouldBeNewUnitCount < $training)
		{
			$unitsToAdd=$wouldBeNewUnitCount;
			$trainingComplete=false;
		}
		else
		{
			$unitsToAdd=$training;
			$trainingComplete=true;
		}
		$a[$value['countDbName']]+=$unitsToAdd;
		$a[$value['trainingDbName']]-=$unitsToAdd;
		if ($trainingComplete)
		{
			$a[$value['countDbName']]=round($a[$value['countDbName']]);
			$a[$value['trainingDbName']]=round($a[$value['trainingDbName']]);
		}
	}
	$bpProducer=$config['buildPointProducer'];
	if (!isset($config['buildings'][$bpProducer])) die('Configuration failure build point producer building is not found. buildings[buildPointProducer]. ');
	$bpBuilding=$config['buildings'][$bpProducer];
	if (!isset($a[$bpBuilding['buildingLevelDbName']])) die('Configuration failure Db[buildings[buildPointProducer].buildingLevelDbName] not found.');
	$bpbLevel=$a[$bpBuilding['buildingLevelDbName']];
	$a['buildPoints']+=(double)$secsElapsed/86400*$bpBuilding['bpProductionSpeedFunction']($bpbLevel);

	$goldProducer=$config['goldProducer'];
	if (!isset($config['buildings'][$goldProducer])) die('Configuration failure gold producer building is not found. buildings[goldProducer]. ');
	$goldBuilding=$config['buildings'][$goldProducer];
	if (!isset($a[$goldBuilding['buildingLevelDbName']])) die('Configuration failure Db[buildings[goldProducer].buildingLevelDbName] not found.');
	$goldLevel=$a[$goldBuilding['buildingLevelDbName']];
	$goldProduced=$secsElapsed/3600*$goldBuilding['goldProductionSpeedFunction']($goldLevel);

	unset($a['secsElapsed']);
	unset($a['villageName']);
	foreach($a as $key => $value)
	{
		$a[$key]="'".mysql_real_escape_string($value)."'";
	}
	$a['lastUpdate']="$when";
	//assembling request
	$assignments=array();
	foreach($a as $key => $value)
	{
		$assignments[]="$key=$value";
//		$assignments[]=mysql_real_escape_string("$key=$value");
	}
	
	$q="UPDATE wtfb2_villages SET ".implode(',',$assignments)." WHERE (id=${a['id']})"; // escaped
	$r=doMySqlQuery($q);


}
//echo $q;

?>
