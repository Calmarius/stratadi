<?php

require_once('userworkerphps.php');

require_once("villageupdater.php");
require_once("battlecalculation.php");
require_once("templateclass.php");
require_once("nightbonus.php");

// TODO: (UI) Do not show buildings to attack when "moving troops".
// TODO: (UI) Prompt for confirmation when sending hero in a "last man standing battle"
// TODO: (wishlist) Show other things like night bonus and age bonus is battle report to be able to reproduce the conditions in the battle simulator.
// TODO: (wishlist) Check the 3:1 rule when the troops arrive.
// TODO: (wishlist) Make simple login.
// TODO: (task) Remove iwiw sharing.

//logText("eventProcessor got called!");

$events=array();
error_reporting(E_ALL);

///// CREATE A DEFAULT EVENT
$defaultEvent=array();
$r=doMySqlQuery("DESCRIBE wtfb2_events");
while($row=mysql_fetch_assoc($r))
{
	if ($row['Default']=='') continue; // we not set those fields that don't have default value. It would cause errors.
	$defaultEvent[$row['Field']]=$row['Default'];
}

//// POST EVENT

function postEvent($event)
{
	global $events;
	global $defaultEvent;
	$r=doMySqlQuery(sqlPrintf("SELECT TIMESTAMPDIFF(SECOND,NOW(),'{1}') AS happensIn,UNIX_TIMESTAMP('{2}') AS eventTime",array($event['happensAt'],$event['happensAt'])));
	$info=mysql_fetch_assoc($r);
	if ((int)$info['happensIn']<0) // already happened so we must insert it into the elapsed events.
	{
		foreach($event as $key=>$value) // ez elég szar... Lehet, hogy lehet jobban is csinálni.
		{
			// evaluate existing keys
			if ($value=='') continue;
			$r=doMySqlQuery(sqlPrintf("SELECT {1} AS result",array($value)));
			$a=mysql_fetch_assoc($r);
			$event[$key]=$a['result'];
		}
		foreach($defaultEvent as $key=>$value)
		{
			if (!isset($event[$key])) // set default values where necessary
			{
				$event[$key]=$value;
			}
		}
		$events[$info['eventTime']][]=$event;
		ksort($events,SORT_NUMERIC);
	}
	else // not yet happened, so we insert it into the database.
	{
		doMySqlQuery("INSERT INTO wtfb2_events (".implode(",",array_keys($event)).") VALUES (".implode(",",$event).")"); // events are safe afaik, only ids are there
	}
}

/*SELECT e.* FROM wtfb2_events e
INNER JOIN wtfb2_villages v ON ((v.id=e.launcherVillage) OR (v.id=e.destinationVillage))
INNER JOIN wtfb2_users u ON (v.ownerId=u.id)
WHERE (u.id=4)

*/
//// HANDLING EVENTS

function evtSettleVillage($event)
{
	global $config;
	global $language;
	// somebody settled at the position we planned to settle?
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_villages WHERE (x='{1}') AND (y='{2}')",array($event['targetX'],$event['targetY'])));
	$settledOn=mysql_num_rows($r)>0;
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_villages WHERE (id='{1}')",array($event['launcherVillage'])));
	$isVillage=mysql_num_rows($r)!=0;
	$village;
	if ($isVillage) $village=mysql_fetch_assoc($r);
	if ($settledOn)
	{
		// send back the diplomat if we can.
		if ($isVillage)
		{
			// send back the diplomat
			$newEvent=array
			(
				'eventType'=>"'move'",
				'happensAt'=>sqlPrintf("TIMESTAMPADD(SECOND,TIMESTAMPDIFF(SECOND,'{1}','{2}'),'{3}')",array($event['launchedAt'],$event['happensAt'],$event['happensAt'])),
				'estimatedTime'=>sqlPrintf("TIMESTAMPADD(SECOND,TIMESTAMPDIFF(SECOND,'{1}','{2}'),'{3}')",array($event['launchedAt'],$event['happensAt'],$event['happensAt'])),
				'launchedAt'=>sqlPrintf("'{1}'",array($event['happensAt'])),
				'launcherVillage'=>"0",
				'destinationVillage'=>mysql_real_escape_string($village['id']),
				$config['units'][$config['settlerUnit']]['countDbName']=>'1'
			);
			postEvent($newEvent);
			// give back an expansion point.
			doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=expansionPoints+1 WHERE (id='{1}')",array($village['ownerId'])));
		}
	}
	else
	{
		// get the launcher village
		if (!$isVillage) return; // no village there is nothing to do.
		// settle the village
		doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_villages (ownerId,villageName,x,y,lastUpdate) VALUES ('{1}','{2}','{3}','{4}',NOW())",array($village['ownerId'],$language['newvillage'],$event['targetX'],$event['targetY']))); 
		// also set a world event about the spawn of the village
		doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ('{1}','{2}',NOW(),'settle')",array($event['targetX'],$event['targetY'])));
	}
}

function evtMoveTroops($event)
{
	global $config;
	global $language;
	// load the village
	$r=doMySqlQuery(sqlPrintf("SELECT *,u.userName FROM wtfb2_villages v LEFT JOIN wtfb2_users u ON (v.ownerId=u.id) WHERE (v.id='{1}')",array($event['destinationVillage'])));
	$isVillage=mysql_num_rows($r)>0;
	$village;
	if ($isVillage) $village=mysql_fetch_assoc($r);
	if ($isVillage)
	{
		// if village exists then add the troops
		$load=array();
		foreach($config['units'] as $key=>$value)
		{
			$levelName=$value['countDbName'];
			$load[]=mysql_real_escape_string($levelName.'='.$levelName.'+'.(int)$event[$levelName]);
		}
		doMySqlQuery(sqlPrintf("UPDATE wtfb2_villages SET ".implode(",",$load)." WHERE (id='{1}')",array($event['destinationVillage'])));
		doMySqlQuery(sqlPrintf("UPDATE wtfb2_heroes SET inVillage='{1}' WHERE (id='{2}')",array($event['destinationVillage'],$event['heroId'])));
		doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET gold=gold+{1} WHERE (id='{2}')",array($event['gold'],$village['ownerId'])));
		doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (x,y,eventTime,type,recipientId) VALUES ({1},{2},NOW(),'eventhappened',{3})",array($village['x'],$village['y'],$village['ownerId']))); // update that village
		
		if ($event['eventType']=='move')
		{
			$r=doMySqlQuery(sqlPrintf("SELECT *,u.userName FROM wtfb2_villages v LEFT JOIN wtfb2_users u ON (v.ownerId=u.id) WHERE (v.id='{1}')",array($event['launcherVillage'])));
			$lVillage=mysql_fetch_assoc($r);
			if ($lVillage['ownerId']!=$village['ownerId'])
			{
				$reportData=array
				(
					'donatorVillageX' => $lVillage['x'],
					'donatorVillageY' => $lVillage['y'],
					'donatorVillageName' => $lVillage['villageName'],
					'donatorId'=>$lVillage['id'],
					'donatorName'=>$lVillage['userName'],
					'receiverVillageX' => $village['x'],
					'receiverVillageY' => $village['y'],
					'receiverVillageName' => $village['villageName'],
					'receiverId'=>$village['id'],
					'receiverName'=>$village['userName']
				);
				foreach($config['units'] as $key=>$value)
				{
					$levelName=$value['countDbName'];
					$reportData['unit_'.$key]=$event[$levelName];
				}
				$report=new Template('templates/movereport.php',array('params' => $reportData));
				$reportText=$report->getContents();
				$reportTitle=xprintf($language['movetitle'],array(
					xprintf($language['villagetext'],array($lVillage['villageName'],$lVillage['x'],$lVillage['y'])),
					$lVillage['userName'],
					xprintf($language['villagetext'],array($village['villageName'],$village['x'],$village['y'])),
					$village['userName']
				));  				
				// send reports
				doMySqlQuery(
					sqlPrintf
					(
						"INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,reportType,token) VALUES ('{1}','{2}','{3}','{4}','{5}',MD5(RAND()))",
						array($lVillage['ownerId'],$reportTitle,$reportText,$event['happensAt'],'outgoingmove')
					)
				);
				doMySqlQuery(
					sqlPrintf
					(
						"INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,reportType,token) VALUES ('{1}','{2}','{3}','{4}','{5}',MD5(RAND()))",
						array($village['ownerId'],$reportTitle,$reportText,$event['happensAt'],'incomingmove')
					)
				);
				
			}
		}
	}
	else
	{
		// otherwise return back the troops to the owner
		$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_villages WHERE (id='{1}')",array($event['launcherVillage'])));
		if ((mysql_num_rows($r)==0) || ($event['eventType']=='return')) // also return event can't be turned back
		{
			// if no village to return then troops are lost. Heroes deleted from the database.
			doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_heroes WHERE (id='{1}')",array($event['heroId'])));
			return;
		}
		$newEvent=array
		(
			'eventType'=>"'return'",
			'happensAt'=>sqlPrintf("TIMESTAMPADD(SECOND,TIMESTAMPDIFF(SECOND,'{1}','{2}'),'{3}')",array($event['launchedAt'],$event['happensAt'],$event['happensAt'])),
			'estimatedTime'=>sqlPrintf("TIMESTAMPADD(SECOND,TIMESTAMPDIFF(SECOND,'{1}','{2}'),'{3}')",array($event['launchedAt'],$event['happensAt'],$event['happensAt'])),
			'launchedAt'=>sqlPrintf("'{1}'",array($event['happensAt'])),
			'launcherVillage'=>"0",
			'heroId'=>sqlPrintf("'{1}'",array($event['heroId'])),
			'destinationVillage'=>$event['launcherVillage'],
		);
		foreach($config['units'] as $key=>$value)
		{
			$levelName=$value['countDbName'];
			$newEvent[$levelName]=(int)$event[$levelName];
		}
		postEvent($newEvent);	
	}
}

function evtAttackWithTroops($event)
{
	global $config;
	global $language;
	
	// get attacker village
	$r=doMySqlQuery(
		sqlPrintf(
		"
			SELECT wtfb2_villages.*,wtfb2_users.userName,wtfb2_users.id AS userId,TIMESTAMPDIFF(SECOND,wtfb2_users.regDate,NOW()) AS ownerGameTime
			FROM wtfb2_villages LEFT JOIN wtfb2_users ON (wtfb2_users.id=wtfb2_villages.ownerId) WHERE (wtfb2_villages.id='{1}')
		",array($event['launcherVillage'])
		)
	);	
	$attVillage=array();
	if (mysql_num_rows($r)) $attVillage=mysql_fetch_assoc($r);
	// update the player if updated long ago
		
	
	$r=doMySqlQuery(
		sqlPrintf(
		"
			SELECT wtfb2_villages.*,wtfb2_users.userName,wtfb2_users.id AS userId
			FROM wtfb2_villages LEFT JOIN wtfb2_users ON (wtfb2_users.id=wtfb2_villages.ownerId) WHERE (wtfb2_villages.id='{1}')
		",array($event['destinationVillage'])
		)
	);
	$villageExist=mysql_num_rows($r)>0;
	$buildingsDamaged=false;
	$conquered=false;
	if ($villageExist)
	{
		// get defender village
		$dstVillage=mysql_fetch_assoc($r);
		// get owners of the villages
		$attackerPlayer=$attVillage['ownerId'];
		$defenderPlayer=$dstVillage['ownerId'];
		// update the target and source players
		updatePlayer($dstVillage['ownerId'],$event['happensAt']);
		updatePlayer($attVillage['ownerId'],$event['happensAt']);
		updateVillage($dstVillage['id'],$event['happensAt']);
		// get the updated defender village
		$r=doMySqlQuery(
			sqlPrintf(
			"
				SELECT wtfb2_villages.*,wtfb2_users.userName,wtfb2_users.id AS userId,TIMESTAMPDIFF(SECOND,wtfb2_users.regDate,NOW()) AS ownerGameTime
				FROM wtfb2_villages LEFT JOIN wtfb2_users ON (wtfb2_users.id=wtfb2_villages.ownerId) WHERE (wtfb2_villages.id='{1}')
			",array($event['destinationVillage'])
			)
		);
		$dstVillage=mysql_fetch_assoc($r);		
		
		// get defender heroes
		$r=doMySqlQuery(
			sqlPrintf(
			"
				SELECT *
				FROM wtfb2_heroes
				WHERE (inVillage ='{1}')
			",array($dstVillage['id'])
			)
		);
		$defenderHeroes=array();
		$defenderHeroIds=array();
		$defenderHeroSkills=array('offense'=>0,'defense'=>0);
		while($row=mysql_fetch_assoc($r))
		{
			$defenderHeroes[]=$row;
			$defenderHeroIds[]=$row['id'];
			$defenderHeroSkills['offense']+=$row['offense'];
			$defenderHeroSkills['defense']+=$row['defense'];
		}
		$attackerHeroSkill=0;
		// get attacker hero data
		$r=doMySqlQuery
		(
			sqlPrintf
			(
				"SELECT * FROM wtfb2_heroes WHERE (id='{1}')",array($event['heroId'])
			)
		);
		$attackerHeroes=array();
		if (mysql_num_rows($r)>0) 
		{
			$attackerHeroes[]=mysql_fetch_assoc($r);
			$attackerHeroSkill=$attackerHeroes[0]['offense'];
		}
		// prepare attack
/*
		.attacker_<unittype>: units on the attacker side +
		.attackerhero: attacker hero attack level +
		.defender_<unittype>: units on defender side +
		.defenderhero: defender hero defense level +
		.mode: attack mode (attack,raid or recon) +
		.targetlevel: level of the targeted building. +
		.targetwall: set when the taret is the wall +
		.walllevel: level of the wall	+
*/
		$simulationInput=array(); 
		foreach($config['units'] as $key=>$value)
		{
			$unitDescriptor=$value;
			$simulationInput['attacker_'.$key]=round($event[$unitDescriptor['countDbName']]);
			$simulationInput['defender_'.$key]=round($dstVillage[$unitDescriptor['countDbName']]);
		}
		$simulationInput['defenderhero']=$config['experienceFunction']($defenderHeroSkills['defense']);
		$simulationInput['attackhero']=$config['experienceFunction']($attackerHeroSkill);
		$simulationInput['mode']=$event['eventType'];
		$simulationInput['targetlevel']=0;
		$nbInfo=getNightBonusInfo($event['eventTime']);
		$simulationInput['nightbonus']=$nbInfo['bonus'];
		$ab=1;
		if (($attVillage['ownerGameTime']!==null) && ($dstVillage['ownerGameTime']!==null))
		{
			$ab=$attVillage['ownerGameTime']/$dstVillage['ownerGameTime'];
		}
		if ($ab<1) $ab=1;
		$simulationInput['agebonus']=$ab;
		if (isset($config['buildings'][$event['catapultTarget']]))
		{
			$buildingDescriptor=$config['buildings'][$event['catapultTarget']];
			$simulationInput['targetlevel']=$dstVillage[$buildingDescriptor['buildingLevelDbName']];
		}
		if ($event['catapultTarget']==$config['armorBuilding'])
		{
			$simulationInput['targetwall']=true;
		}
		if (!isset($config['buildings'][$config['armorBuilding']])) die('Configuration failure. Armor building not found.');
		$wallDescriptor=$config['buildings'][$config['armorBuilding']];
		$simulationInput['walllevel']=$dstVillage[$wallDescriptor['buildingLevelDbName']];
		// let it go!!!
		$simulationResult=calculateBattleCasualties($simulationInput);
		// grab the results.
		/*
			.attackerFalls: attacker fallen
			.attacker.casualties.<unittype>: attacker's loss
			.defender.casualties.<unittype>: defender's loss
			.defender.targetdemolished: the new level of the target.
			.defenderFalls: attacker fallen
		*/
		// give participant heroes experience
			// attacker hero
		doMySqlQuery(sqlPrintf("UPDATE wtfb2_heroes SET offense=offense+'{1}' WHERE (id='{2}')",array($simulationResult['attackerHeroXP'],$event['heroId'])));
			// defender heroes if any
		$cntDefenderHeroes=count($defenderHeroes);
		if ($cntDefenderHeroes>0)
		{
			$xp=$simulationResult['defenderHeroXP']/$cntDefenderHeroes;
			doMySqlQuery("UPDATE wtfb2_heroes SET defense=defense+$xp WHERE (id IN (".implode(',',$defenderHeroIds)."))");	// its not injectable
		}
		// add kills to the players
		doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET attackKills=attackKills+'{1}' WHERE (id='{2}')",array($simulationResult['attackerHeroXP'],$attVillage['ownerId'])));
		doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET defenseKills=defenseKills+'{1}' WHERE (id='{2}')",array($simulationResult['defenderHeroXP'],$dstVillage['ownerId'])));
		
		// set the attack back event
		$newEvent=array();
		$newEvent['gold']='0';
		$heroesToDismiss=array();
		if ($simulationResult['attackerFalls'])
		{
			// he eaten it.
			// dissmiss his hero but place it in the destination village
			doMySqlQuery(sqlPrintf("UPDATE wtfb2_heroes SET ownerId=0,inVillage='{1}' WHERE (id='{2}')",array($dstVillage['id'],$event['heroId']))); //safe
			$heroesToDismiss[]=$event['heroId'];
		}
		else
		{
			// if we have diplomat and normal attacked, we will conquer the village and won't return!
			$possibleGold=0;
			foreach($config['units'] as $key=>$value)
			{
				$unitDescriptor=$value;
				$countDbName=$unitDescriptor['countDbName'];
				// attackers' things
				$survivors=(int)$event[$countDbName]-(int)$simulationResult['attacker']['casualties'][$key];
				$event[$countDbName]=$survivors;
				$newEvent[$countDbName]=$event[$countDbName];
				$possibleGold+=$survivors*$unitDescriptor['strength'];
			}
			if ($event['eventType']=='attack')
			{
				$conquerorUnitDbName=$config['units'][$config['conquerorUnit']]['countDbName'];
//				if ((int)$event[$conquerorUnitDbName]>(int)$simulationResult['attacker']['casualties'][$config['conquerorUnit']])
				if ($simulationResult['wouldConquer'])
				{
					// the player must have enough expansion points to finish the conquer
					$r=doMySqlQuery(sqlPrintf("SELECT expansionPoints FROM wtfb2_users WHERE (id={1})",array($attVillage['ownerId'])));
					$canConquer=true;
					if (mysql_num_rows($r)<1) $canConquer=false;
					$player=mysql_fetch_assoc($r);
					if ((int)$player['expansionPoints']<1) $canConquer=false;
					if ($canConquer)
					{
						doMySqlQuery
						(
							sqlPrintf
							(
								"INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ('{1}','{2}','{3}','conquer')",array($dstVillage['x'],$dstVillage['y'],$event['happensAt'])
							)
						); // set up a world event
						// add the troops to the newly conquered village
						foreach($config['units'] as $key=>$value)
						{
							$unitDescriptor=$value;
							$countDbName=$unitDescriptor['countDbName'];
							$dstVillage[$countDbName]+=$newEvent[$countDbName];
						}
						$dstVillage[$conquerorUnitDbName]-=1; // we take the conqueror unit
/*						// if the hero came with us we will place him in this village
						doMySqlQuery(sqlPrintf("UPDATE wtfb2_heroes SET inVillage='{1}' WHERE (id='{2}')",array($dstVillage['id'],$event['heroId'])));*/
						// take that expansion point from the player
						doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=expansionPoints-1 WHERE (id='{1}')",array($attVillage['ownerId'])));
						// give expansion point to the victim
						doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=expansionPoints+1 WHERE (id='{1}')",array($dstVillage['ownerId'])));
						// pending settlings can mess up things
							// so how many settlings are on the way?
							$r=doMySqlQuery(sqlPrintf("SELECT COUNT(*) AS pendingSettlingCount FROM wtfb2_events WHERE (launcherVillage='{1}') AND (eventType='settle')",array($dstVillage['id'])));
							$a=mysql_fetch_assoc($r);
							$pendingSettings=$a['pendingSettlingCount'];
							// take as many expansion points from the attacker
							doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=expansionPoints-{1} WHERE (id='{2}')",array($pendingSettings,$attVillage['ownerId'])));
							// and give back as many expansion points to the victim
							doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=expansionPoints+{1} WHERE (id='{2}')",array($pendingSettings,$dstVillage['ownerId'])));
						// the new owner can turn back the troops that's moving away. We must prevent this.
						doMySqlQuery(sqlPrintf("UPDATE wtfb2_events SET eventType='return' WHERE (eventType='move') AND (launcherVillage='{1}')",array($dstVillage['id'])));
						// finally change owner
						$dstVillage['ownerId']=(int)$attVillage['ownerId'];
						$dstVillage['buildPoints']=0;
						$conquered=true;
					}
				}
			}
			if (!$conquered)
			{
				$newEvent['eventType']="'return'";
				// flip the source and destination villages
				$newEvent['launcherVillage']=$event['destinationVillage'];
				$newEvent['destinationVillage']=$event['launcherVillage'];
				// calculate the backtime
				$newEvent['launchedAt']="'${event['happensAt']}'";
				$newEvent['happensAt']=$newEvent['estimatedTime']="TIMESTAMPADD(SECOND,TIMESTAMPDIFF(SECOND,'${event['launchedAt']}','${event['happensAt']}'),'${event['happensAt']}')";
				// with hero or without hero we set its id.
				$newEvent['heroId']=$event['heroId'];
				// finally calculate the gold we can take if we didn't reconned
				$newEvent['gold']=0;
				if ($event['eventType']!='recon')
				{
					$r=doMySqlQuery(sqlPrintf("SELECT COUNT(*) AS villageCount FROM wtfb2_villages WHERE (ownerId='{1}')",array($dstVillage['ownerId'])));
					$a=mysql_fetch_assoc($r);
					$villages=(int)$a['villageCount'];
					$r=doMySqlQuery(sqlPrintf("SELECT gold FROM wtfb2_users WHERE (id='{1}')",array($dstVillage['ownerId'])));
					$a=mysql_fetch_assoc($r);
					$allGold=(double)$a['gold'];
					$goldCanBeTaken=$allGold/$villages;
					$newEvent['gold']=round($goldCanBeTaken < $possibleGold ? $goldCanBeTaken : $possibleGold);
	/*				echo 'villages='.$villages."\r\n";
					echo 'allGold='.$allGold."\r\n";
					echo 'goldCanBeTaken='.$goldCanBeTaken."\r\n";
					echo 'possibleGold='.$possibleGold."\r\n";
					print_r($newEvent);
					die($newEvent['gold']);*/
					// take the gold from the player
					doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET gold=gold-{1} WHERE (id={2})",array($newEvent['gold'],$dstVillage['ownerId'])));
				}
				postEvent($newEvent);
			}
		}
		// update the defender's village.
		foreach($config['units'] as $key=>$value)
		{
			// defenders' things
			$unitDescriptor=$value;
			$countDbName=$unitDescriptor['countDbName'];
			$dstVillage[$countDbName]=(int)$dstVillage[$countDbName]-(int)$simulationResult['defender']['casualties'][$key];
			if ($dstVillage[$countDbName]<0) $dstVillage[$countDbName]=0;
		}
		// do catapulting
		if (isset($config['buildings'][$event['catapultTarget']]))
		{
			$buildingDescriptor=$config['buildings'][$event['catapultTarget']];
			if ($dstVillage[$buildingDescriptor['buildingLevelDbName']]!=$simulationResult['defender']['targetdemolished'])
			{
				doMySqlQuery
				(
					sqlPrintf
					(
						"INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ('{1}','{2}','{3}','scorechanged')"
						,array($dstVillage['x'],$dstVillage['y'],$event['happensAt'])
					)
				); // score will change as a result of demolition
				$buildingsDamaged=true;
			}
			$dstVillage[$buildingDescriptor['buildingLevelDbName']]=$simulationResult['defender']['targetdemolished'];
		}
		// delete the village if destroyed.
		$sumLevels=0;
		foreach($config['buildings'] as $key=>$value)
		{
			$sumLevels+=(int)$dstVillage[$value['buildingLevelDbName']];
		}
		// dismiss all defending heroes if defenders fallen or the village destroyed.
		if ($simulationResult['defenderFalls'])
		{
			// select all heroes except the attacking hero (we would dismiss him immediately on conquer otherwise!)
			$r=doMySqlQuery
			(
				sqlPrintf
				(
					"SELECT id FROM wtfb2_heroes WHERE (inVillage={1})",
					array($dstVillage['id'])
				)
			); 
			while($row=mysql_fetch_assoc($r))
			{
				$heroesToDismiss[]=(int)$row['id'];
			}
			
			doMySqlQuery
			(
				sqlPrintf
				(
					"UPDATE wtfb2_heroes SET ownerId=0 WHERE (inVillage={1})",
					array($dstVillage['id'])
				)
			);
		}
		// Now move the attacker hero to the conquered village (if we did that before we would dismiss the attacker hero too!)
		if ($conquered)
			doMySqlQuery(sqlPrintf("UPDATE wtfb2_heroes SET inVillage='{1}' WHERE (id='{2}')",array($dstVillage['id'],$event['heroId'])));
		// move out dismissed heroes
		if (count($heroesToDismiss)>0)
			moveFreeHeroes($event['happensAt'],$heroesToDismiss);
		$destroyed=false;
		if (($sumLevels==0) && ($simulationResult['defenderFalls']))
		{
			// give back expansion points to the victim for pending settlings if the village is not also conquered
			if (!$conquered)
			{
				$r=doMySqlQuery
				(
					sqlPrintf
					(
						"SELECT COUNT(*) AS pendingSettlingCount FROM wtfb2_events WHERE (launcherVillage='{1}') AND (eventType='settle')"
						,array($dstVillage['id'])
					)
				);
				$a=mysql_fetch_assoc($r);
				$pendingSettings=$a['pendingSettlingCount'];
				// give back as many expansion points to the victim
				doMySqlQuery
				(
					sqlPrintf
					(
						"UPDATE wtfb2_users SET expansionPoints=expansionPoints+'{1}' WHERE (id='{2}')"
						,array($pendingSettings,$dstVillage['ownerId'])
					)
				);
			}
			
			// delete the village
			doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_villages WHERE (id='{1}')",array($dstVillage['id'])));
			// give expansion point to the victim
			doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=expansionPoints+1 WHERE (id='{1}')",array($dstVillage['ownerId'])));

			doMySqlQuery
			(
				sqlPrintf
				(
					"INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ('{1}','{2}','{3}','destroy')",array($dstVillage['x'],$dstVillage['y'],$event['happensAt'])
				)
			); // set up a world event
			$destroyed=true;
		}


		doMySqlQuery
		(
			sqlPrintf
			(
				"INSERT INTO wtfb2_worldevents (x,y,eventTime,type,recipientId)
				VALUES ('{1}','{2}','{3}','eventhappened','{4}')"
				,array($dstVillage['x'],$dstVillage['y'],$event['happensAt']),$dstVillage['ownerId']
			)
		); // set up a world event for the owner of the village

		$attackerReportType='attackwithloss';
		$defenderReportType='defensewithloss';
		if ($simulationResult['defenderFalls']) $defenderReportType='defensefail';
		if ($simulationResult['defenderNoCasualties']) $defenderReportType='defensenoloss';
		if ($simulationResult['attackerFalls']) $attackerReportType='attackfail';
		if ($simulationResult['attackerNoCasualties']) $attackerReportType='attacknoloss';
		if ($conquered)
		{
			$attackerReportType='gotvillagebyconquer';
			$defenderReportType='lostvillagebyconquer';
		}
		if ($destroyed)
		{
			$attackerReportType='destroyedvillage';
			$defenderReportType='lostvillagebydestruct';
		}
		// send out the reports
		
		// first send report to the defender.
		$reportData=array_merge($simulationInput,$simulationResult);
		$reportData['defenderVillageX']=$dstVillage['x'];
		$reportData['defenderVillageY']=$dstVillage['y'];
		$reportData['defenderVillageName']=$dstVillage['villageName'];
		$reportData['defenderName']=$dstVillage['userName'];
		$reportData['defenderId']=$dstVillage['userId'];
		$reportData['attackerVillageX']=$attVillage['x'];
		$reportData['attackerVillageY']=$attVillage['y'];
		$reportData['attackerVillageName']=$attVillage['villageName'];
		$reportData['attackerName']=$attVillage['userName'];
		$reportData['attackerId']=$attVillage['userId'];
		$reportData['loot']=$newEvent['gold'];
		$reportData['catapultTarget']=$event['catapultTarget'];
		$reportData['conquered']=$conquered;
		$reportData['destroyed']=$destroyed;
		$reportData['attacker']['heroes']=$attackerHeroes;			
		$reportData['defender']['heroes']=$defenderHeroes;
		$reportData['attackerConquered']=$conquered;
		$report=new Template("templates/battlereport.php",array('params'=>$reportData));
		$reportText=$report->getContents();
		$reportTitle=xprintf($language['attacktitle'],array(
			xprintf($language['villagetext'],array($attVillage['villageName'],$attVillage['x'],$attVillage['y'])),
			$attVillage['userName'],
			xprintf($language['villagetext'],array($dstVillage['villageName'],$dstVillage['x'],$dstVillage['y'])),
			$dstVillage['userName']
		)); // hát ennek nem itt kéne lennie...
			// sending the report now
		doMySqlQuery(
			sqlPrintf
			(
				"INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,reportType,token) VALUES ('{1}','{2}','{3}','{4}','{5}',MD5(RAND()))",
				array($dstVillage['userId'],$reportTitle,$reportText,$event['happensAt'],$defenderReportType)
			)
		);
		
		// the send report to the attacker.
		if ($simulationResult['attackerFalls'])
		{
			// if the attacker sucked it, then strip all defender info out.
			foreach($config['units'] as $key=>$value)
			{
				$reportData['defender_'.$key]='?';
				$reportData['defender']['casualties'][$key]='?';
			}
			$reportData['defender']['targetdemolished']='?';
			$reportData['targetlevel']='?';
		}
		$report=new Template("templates/battlereport.php",array('params'=>$reportData));
		$reportText=$report->getContents();
		doMySqlQuery
		(
			sqlPrintf
			(
				"INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,reportType,token) VALUES ('{1}','{2}','{3}','{4}','{5}',MD5(RAND()))"
				,array($attVillage['userId'],$reportTitle,$reportText,$event['happensAt'],$attackerReportType)
			)
		);
		// so update the defender village.
		$updates=array();
		unset($dstVillage['userName']); // unsert user specific stuff
		unset($dstVillage['userId']);  // unsert user specific stuff
		unset($dstVillage['ownerGameTime']);  // unsert user specific stuff
		foreach($dstVillage as $key=>$value)
		{
			if ($key=='') continue; // WTF?
			$updates[]="$key='".mysql_real_escape_string($value)."'";
		}
		doMySqlQuery
		(
			sqlPrintf
			(
				"UPDATE wtfb2_villages SET ".implode(',',$updates)." WHERE (id='{1}')",
				array($dstVillage['id'])
			)
		);
	}
	else
	{
		// if village does not exist, just turn back.
		$newEvent=array();
		$newEvent['eventType']="'return'";
		// set event times.
		$newEvent['launchedAt']="'${event['happensAt']}'";
		$newEvent['happensAt']=$newEvent['estimatedTime']="TIMESTAMPADD(SECOND,TIMESTAMPDIFF(SECOND,'${event['launchedAt']}','${event['happensAt']}'),'${event['happensAt']}')";
		// also exchange the sender and destination villages
		$newEvent['launcherVillage']=$event['destinationVillage'];
		$newEvent['destinationVillage']=$event['launcherVillage'];
		foreach($config['units'] as $key=>$value)
		{
			// defenders' things
			$unitDescriptor=$value;
			$countDbName=$unitDescriptor['countDbName'];
			$newEvent[$countDbName]=$event[$countDbName];
		}
		$newEvent['heroId']=$event['heroId'];
		// everything remains the same so post the event
		postEvent($newEvent);
	}
	// Finally update the gold production of the player.
	if ($buildingsDamaged || $conquered)
	{
		recalculatePlayerInfo($defenderPlayer);
	}
	if ($conquered)
	{
		recalculatePlayerInfo($attackerPlayer);
	}
		
}

function moveFreeHeroes($happensAt=null,$heroIds=array())
{
	global $config;
	// select the count of the villages
	$r=doMySqlQuery("SELECT COUNT(*) AS villageCount FROM wtfb2_villages");
	$a=mysql_fetch_assoc($r);
	$villageCount=$a['villageCount'];
	// select all abandoned or from the idsheroes that are in a village.
	$insetText="";
	if (count($heroIds)>0)
	{
		$insetText="AND (id IN (".implode(',',$heroIds)."))";
	}
	$freeHeroes=doMySqlQuery("SELECT * FROM wtfb2_heroes WHERE (ownerId=0) AND (inVillage<>0) $insetText"); // ids won't contain '-s so they are safe.
	while($freeHero=mysql_fetch_assoc($freeHeroes))
	{
		// select the village the hero in
		$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_villages WHERE (id='{1}')",array($freeHero['inVillage'])));
		if (mysql_num_rows($r)<1) continue; // if village not found then the hero is lost.
		$village=mysql_fetch_assoc($r);
		// select a 'random' destination village
		$r=doMySqlQuery("SELECT * FROM wtfb2_villages LIMIT ".mt_rand(0,$villageCount-1).",1"); // explain says ALL :(
		$dstVillage=mysql_fetch_assoc($r);
		// move the hero to that village
		$dx=(int)$village['x']-(int)$dstVillage['x'];
		$dy=(int)$village['y']-(int)$dstVillage['y'];
		$distance=sqrt($dx*$dx+$dy*$dy);
		$travelTime=$distance*3600/$config['heroSpeed'];
		if ($travelTime>86400) $travelTime=86400; // no more than a day
		// set up the event
		if ($happensAt===null)
		{
			$currentDate=date('Y-m-d').' 0:00:00'; // time set for debug reasons
		}
		else
		{
			$currentDate=$happensAt; // time set for debug reasons
		}
		$realTime=randomizeTime($travelTime);

		$event=array
		(
			'eventType'=>"'heromove'",
			'launchedAt'=>sqlPrintf("'{1}'",array($currentDate)),
			'estimatedTime'=>sqlPrintf("TIMESTAMPADD(SECOND,{1},'{2}')",array($travelTime,$currentDate)),
			'happensAt'=>sqlPrintf("TIMESTAMPADD(SECOND,{1},'{2}')",array($realTime,$currentDate)),
			'launcherVillage'=>$village['id'],
			'destinationVillage'=>$dstVillage['id'],
			'heroId'=>$freeHero['id']
		);
		// leave the village
		doMysqlQuery(sqlPrintf("UPDATE wtfb2_heroes SET inVillage=0 WHERE (id='{1}')",array($freeHero['id'])));
		// then post that event
		postEvent($event);
	}
}

function moveFreeHeroesIfNeeded()
{
	global $config;
	$r=doMysqlQuery("SELECT TIMESTAMPDIFF(DAY,lastHeroMove,NOW()) AS needHeroMove FROM wtfb2_worldupdate");
	$a=mysql_fetch_assoc($r);
	if ((int)$a['needHeroMove']>0)
	{
		moveFreeHeroes();
		$r=doMysqlQuery("UPDATE wtfb2_worldupdate SET lastHeroMove=CURDATE()"); // then update it.
	}
}

$toDelete=array();
$elapsedEvents=array();

define('WORKING_DIR', getcwd());

function onShutdown()
{
    global $toDelete;
    global $elapsedEvents;

    chdir(WORKING_DIR);
    if (count($toDelete)>0)
    {
        doMysqlQuery("DELETE FROM wtfb2_events WHERE (id IN (".implode(',',$toDelete)."))");	 //safe
        $f=fopen('elapsedevents_uccsetalalodkimianevedhkashdjkhasjdhjkashdka.txt','a+t');
        foreach($elapsedEvents as $key=>$value)
        {
            fwrite($f,json_encode($value)."\n");
        }
        fclose($f);
    }
    if (!unlink('lockfile'))
    {
        $e = error_get_last();
        die('Failed to delete lockfile.');
    }
}

if (!file_exists('lockfile'))
{
	$f=fopen('lockfile','w+');
	fclose($f);
	register_shutdown_function('onShutdown');
///// MOVE FREE HEROES IF NEEDED
	moveFreeHeroesIfNeeded();
///// MAGIC
	$r=doMysqlQuery("SELECT *,UNIX_TIMESTAMP(happensAt) AS eventTime FROM wtfb2_events WHERE (happensAt<=NOW())"); // hottest query in the game
	while($row=mysql_fetch_assoc($r))
	{
		$events[$row['eventTime']][]=$row;
	}
	for(;;)
	{
		ksort($events,SORT_NUMERIC);
		$event;
		$k1;
		$k2;
		$noEvent=true;
		foreach($events as $key=>$value)
		{
			foreach($value as $key2=>$value2)
			{
				$k1=$key;
				$k2=$key2;
				$noEvent=false;
				break 2;
			}
		}
		if ($noEvent) break;
		$event=$events[$k1][$k2];
		// stuff goes here
	/*	echo "I HANDLED THIS FUCKED EVENT: \n";
		print_r($event);*/
		if ($event['eventType']=='settle') evtSettleVillage($event);
		else if (($event['eventType']=='move') || ($event['eventType']=='return') || ($event['eventType']=='heromove')) evtMoveTroops($event);
		else if (($event['eventType']=='attack') || ($event['eventType']=='recon') || ($event['eventType']=='raid')) evtAttackWithTroops($event);
		else die('OOPS! unhandled event type this sucks! This should not happen! The eventType was: '.$event['eventType']);
	
	
		// end of the stuff
		unset($events[$k1][$k2]);
		if (isset($event['id']))
		{
			$toDelete[]=$event['id'];
			$elapsedEvents[]=$event;
			
		}
	}
	if (count($toDelete)>0)
	{
		doMysqlQuery("DELETE FROM wtfb2_events WHERE (id IN (".implode(',',$toDelete)."))");	//safe
		$f=fopen('elapsedevents_uccsetalalodkimianevedhkashdjkhasjdhjkashdka.txt','a+t');
		foreach($elapsedEvents as $key=>$value)
		{
			fwrite($f,json_encode($value)."\n");
		}
		fclose($f);
	}
	$toDelete=array();
	$elapsedEvents=array();
}
//die('YEAH');



/*print_r($events);
die();*/

/*$testArray=array('9'=>'foo','10'=>'bar','11'=>'zig');
ksort($testArray,SORT_NUMERIC);
print_r($testArray);
die();*/


?>
