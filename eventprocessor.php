<?php

require_once('userworkerphps.php');

require_once("villageupdater.php");
require_once("battlecalculation.php");
require_once("templateclass.php");
require_once("nightbonus.php");

$events=array();
error_reporting(E_ALL);

///// CREATE A DEFAULT EVENT
$defaultEvent=array();
$r=runEscapedQuery("DESCRIBE wtfb2_events");
foreach ($r[0] as $row)
{
	if ($row['Default']=='') continue; // we not set those fields that don't have default value. It would cause errors.
	$defaultEvent[$row['Field']]=$row['Default'];
}

//// POST EVENT

// This function assumes that the event is properly real escaped.
function postEvent($event)
{
	global $events;
	global $defaultEvent;
	$r=runEscapedQuery("SELECT TIMESTAMPDIFF(SECOND,NOW(),{0}) AS happensIn,UNIX_TIMESTAMP({1}) AS eventTime",$event['happensAt'],$event['happensAt']);
	$info=$r[0][0];
	if ((int)$info['happensIn']<0) // already happened so we must insert it into the elapsed events.
	{
		foreach($event as $key=>$value) // ez elég szar... Lehet, hogy lehet jobban is csinálni.
		{
			// evaluate existing keys
			if ($value=='') continue;
			$r=runEscapedQuery("SELECT $value AS result");
			$a=$r[0][0];
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
		runEscapedQuery("INSERT INTO wtfb2_events (".implode(",",array_keys($event)).") VALUES (".implode(",",$event).")"); // events are safe afaik, only ids are there
	}
}

//// HANDLING EVENTS

function evtSettleVillage($event)
{
	global $config;
	global $language;
	// somebody settled at the position we planned to settle?
	$r=runEscapedQuery("SELECT * FROM wtfb2_villages WHERE (x={0}) AND (y={1})",$event['targetX'],$event['targetY']);
	$settledOn=!isEmptyResult($r);
	$r=runEscapedQuery("SELECT * FROM wtfb2_villages WHERE (id={0})", $event['launcherVillage']);
	$isVillage=!isEmptyResult($r);
	$village;
	if ($isVillage) $village=$r[0][0];
	if ($settledOn)
	{
		// send back the diplomat if we can.
		if ($isVillage)
		{
			// send back the diplomat
			$newEvent=array
			(
				'eventType'=>"'move'",
				'happensAt'=>sqlvprintf("TIMESTAMPADD(SECOND,TIMESTAMPDIFF(SECOND,{0},{1}),{2})",array($event['launchedAt'],$event['happensAt'],$event['happensAt'])),
				'estimatedTime'=>sqlvprintf("TIMESTAMPADD(SECOND,TIMESTAMPDIFF(SECOND,{0},{1}),{2})",array($event['launchedAt'],$event['happensAt'],$event['happensAt'])),
				'launchedAt'=>sqlvprintf("{0}",array($event['happensAt'])),
				'launcherVillage'=>"0",
				'destinationVillage'=>$village['id'],
				$config['units'][$config['settlerUnit']]['countDbName']=>'1'
			);
			postEvent($newEvent);
			// give back an expansion point.
			runEscapedQuery("UPDATE wtfb2_users SET expansionPoints=expansionPoints+1 WHERE (id={0})",$village['ownerId']);
		}
	}
	else
	{
		// get the launcher village
		if (!$isVillage) return; // no village there is nothing to do.
		// settle the village
		runEscapedQuery(
		    "INSERT INTO wtfb2_villages (ownerId,villageName,x,y,lastUpdate) 
		    VALUES ({0},{1},{2},{3},NOW())",
		    $village['ownerId'],
		    $language['newvillage'],
		    $event['targetX'],
		    $event['targetY']
		); 
		// also set a world event about the spawn of the village
		runEscapedQuery("INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ({0},{1},NOW(),'settle')",$event['targetX'],$event['targetY']);
	}
}

function evtMoveTroops($event)
{
	global $config;
	global $language;
	// load the village
	$r=runEscapedQuery("SELECT *,u.userName FROM wtfb2_villages v LEFT JOIN wtfb2_users u ON (v.ownerId=u.id) WHERE (v.id={0})",$event['destinationVillage']);
	$isVillage=!isEmptyResult($r);;
	$village;
	if ($isVillage) $village=$r[0][0];
	if ($isVillage)
	{
		// if village exists then add the troops
		$load=array();
		foreach($config['units'] as $key=>$value)
		{
			$unitName=$value['countDbName'];
			$load[]=sqlvprintf("$unitName=$unitName+{0}", array((int)$event[$unitName]));
		}
		runEscapedQuery("UPDATE wtfb2_villages SET ".implode(",",$load)." WHERE (id={0})",$event['destinationVillage']);
		runEscapedQuery("UPDATE wtfb2_heroes SET inVillage={0} WHERE (id={1})",$event['destinationVillage'],$event['heroId']);
		runEscapedQuery("UPDATE wtfb2_users SET gold=gold+{0} WHERE (id={1})",(float)$event['gold'],$village['ownerId']);
		runEscapedQuery("INSERT INTO wtfb2_worldevents (x,y,eventTime,type,recipientId) VALUES ({0},{1},NOW(),'eventhappened',{2})",$village['x'],$village['y'],$village['ownerId']); // update that village
		
		if ($event['eventType']=='move')
		{
			$r=runEscapedQuery("SELECT *,u.userName FROM wtfb2_villages v LEFT JOIN wtfb2_users u ON (v.ownerId=u.id) WHERE (v.id={0})",$event['launcherVillage']);
			$lVillage=$r[0][0];
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
				if ($lVillage['ownerId'] !== null)
				{
				    runEscapedQuery(
					    "INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,reportType,token) VALUES ({0},{1},{2},{3},{4},MD5(RAND()))",
					    $lVillage['ownerId'],$reportTitle,$reportText,$event['happensAt'],'outgoingmove'
				    );
				}
				if ($village['ownerId'] !== null)
				{
				    runEscapedQuery(
					    "INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,reportType,token) VALUES ({0},{1},{2},{3},{4},MD5(RAND()))",
					    $village['ownerId'],$reportTitle,$reportText,$event['happensAt'],'incomingmove'
				    );
				}
				
			}
		}
	}
	else
	{
		// otherwise return back the troops to the owner
		$r=runEscapedQuery("SELECT * FROM wtfb2_villages WHERE (id={0})",$event['launcherVillage']);
		if ((isEmptyResult($r)) || ($event['eventType']=='return')) // also return event can't be turned back
		{
			// if no village to return then troops are lost. Heroes deleted from the database.
			runEscapedQuery("DELETE FROM wtfb2_heroes WHERE (id={0})",$event['heroId']);
			return;
		}
		$newEvent=array
		(
			'eventType'=>"'return'",
			'happensAt'=>sqlvprintf("TIMESTAMPADD(SECOND,TIMESTAMPDIFF(SECOND,{0},{1}),{2})",array($event['launchedAt'],$event['happensAt'],$event['happensAt'])),
			'estimatedTime'=>sqlvprintf("TIMESTAMPADD(SECOND,TIMESTAMPDIFF(SECOND,{0},{1}),{2})",array($event['launchedAt'],$event['happensAt'],$event['happensAt'])),
			'launchedAt'=>sqlvprintf("{0}",array($event['happensAt'])),
			'launcherVillage'=>"0",
			'heroId'=>sqlvprintf("{0}",array($event['heroId'])),
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
	$r=runEscapedQuery(
		"
			SELECT wtfb2_villages.*,wtfb2_users.userName,wtfb2_users.id AS userId,TIMESTAMPDIFF(SECOND,wtfb2_users.regDate,NOW()) AS ownerGameTime
			FROM wtfb2_villages LEFT JOIN wtfb2_users ON (wtfb2_users.id=wtfb2_villages.ownerId) WHERE (wtfb2_villages.id={0})
		",
		$event['launcherVillage']
	);	
	$attVillage=array();
	if (!isEmptyResult($r)) $attVillage=$r[0][0];
	// update the player if updated long ago
	
	$r=runEscapedQuery(
		"
			SELECT wtfb2_villages.*,wtfb2_users.userName,wtfb2_users.id AS userId
			FROM wtfb2_villages LEFT JOIN wtfb2_users ON (wtfb2_users.id=wtfb2_villages.ownerId) WHERE (wtfb2_villages.id={0})
		",$event['destinationVillage']
	);
	$villageExist=!isEmptyResult($r);
	$buildingsDamaged=false;
	$conquered=false;
	if ($villageExist)
	{
		// get defender village
		$dstVillage=$r[0][0];
		// get owners of the villages
		$attackerPlayer=$attVillage['ownerId'];
		$defenderPlayer=$dstVillage['ownerId'];
		// update the target and source players
		updatePlayer($dstVillage['ownerId'],$event['happensAt']);
		updatePlayer($attVillage['ownerId'],$event['happensAt']);
		updateVillage($dstVillage['id'],$event['happensAt']);
		// get the updated defender village
		$r=runEscapedQuery(
			"
				SELECT wtfb2_villages.*,wtfb2_users.userName,wtfb2_users.id AS userId,TIMESTAMPDIFF(SECOND,wtfb2_users.regDate,NOW()) AS ownerGameTime
				FROM wtfb2_villages LEFT JOIN wtfb2_users ON (wtfb2_users.id=wtfb2_villages.ownerId) WHERE (wtfb2_villages.id={0})
			",$event['destinationVillage']
		);
		$dstVillage=$r[0][0];
		
		// get defender heroes
		$r=runEscapedQuery(
			"
				SELECT *
				FROM wtfb2_heroes
				WHERE (inVillage ={0})
			",$dstVillage['id']
		);
		$defenderHeroes=array();
		$defenderHeroIds=array();
		$defenderHeroSkills=array('offense'=>0,'defense'=>0);
		foreach ($r[0] as $row)
		{
			$defenderHeroes[]=$row;
			$defenderHeroIds[]=$row['id'];
			$defenderHeroSkills['offense']+=$row['offense'];
			$defenderHeroSkills['defense']+=$row['defense'];
		}
		$attackerHeroSkill=0;
		// get attacker hero data
		$r=runEscapedQuery(
	        "SELECT * FROM wtfb2_heroes WHERE (id={0})",$event['heroId']
		);
		$attackerHeroes=array();
		if (!isEmptyResult($r)) 
		{
			$attackerHeroes[]=$r[0][0];
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
		runEscapedQuery("UPDATE wtfb2_heroes SET offense=offense+{0} WHERE (id={1})",$simulationResult['attackerHeroXP'],$event['heroId']);
			// defender heroes if any
		$cntDefenderHeroes=count($defenderHeroes);
		if ($cntDefenderHeroes>0)
		{
			$xp=$simulationResult['defenderHeroXP']/$cntDefenderHeroes;
			runEscapedQuery("UPDATE wtfb2_heroes SET defense=defense+$xp WHERE (id IN (".implode(',',$defenderHeroIds)."))");	// its not injectable
		}
		// add kills to the players
		runEscapedQuery("UPDATE wtfb2_users SET attackKills=attackKills+{0} WHERE (id={1})",$simulationResult['attackerHeroXP'],$attVillage['ownerId']);
		runEscapedQuery("UPDATE wtfb2_users SET defenseKills=defenseKills+{0} WHERE (id={1})",$simulationResult['defenderHeroXP'],$dstVillage['ownerId']);
		
		// set the attack back event
		$newEvent=array();
		$newEvent['gold']='0';
		$heroesToDismiss=array();
		if ($simulationResult['attackerFalls'])
		{
			// he eaten it.
			// dissmiss his hero but place it in the destination village
			runEscapedQuery("UPDATE wtfb2_heroes SET ownerId=0,inVillage={0} WHERE (id={1})",$dstVillage['id'],$event['heroId']); //safe
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
				if ($simulationResult['wouldConquer'])
				{
					// the player must have enough expansion points to finish the conquer
					$r=runEscapedQuery("SELECT expansionPoints FROM wtfb2_users WHERE (id={0})",$attVillage['ownerId']);
					$canConquer=true;
					if (isEmptyResult($r)) $canConquer=false;
					$player=$r[0][0];
					if ((int)$player['expansionPoints']<1) $canConquer=false;
					if ($canConquer)
					{
						runEscapedQuery
						(
					        "INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ({0},{1},{2},'conquer')",$dstVillage['x'],$dstVillage['y'],$event['happensAt']
						); // set up a world event
						// add the troops to the newly conquered village
						foreach($config['units'] as $key=>$value)
						{
							$unitDescriptor=$value;
							$countDbName=$unitDescriptor['countDbName'];
							$dstVillage[$countDbName]+=$newEvent[$countDbName];
						}
						$dstVillage[$conquerorUnitDbName]-=1; // we take the conqueror unit
						// take that expansion point from the player
						runEscapedQuery("UPDATE wtfb2_users SET expansionPoints=expansionPoints-1 WHERE (id={0})",$attVillage['ownerId']);
						// give expansion point to the victim
						runEscapedQuery("UPDATE wtfb2_users SET expansionPoints=expansionPoints+1 WHERE (id={0})",$dstVillage['ownerId']);
						// pending settlings can mess up things
							// so how many settlings are on the way?
							$r=runEscapedQuery("SELECT COUNT(*) AS pendingSettlingCount FROM wtfb2_events WHERE (launcherVillage={0}) AND (eventType='settle')",$dstVillage['id']);
							$a=$r[0][0];
							$pendingSettings=$a['pendingSettlingCount'];
							// take as many expansion points from the attacker
							runEscapedQuery("UPDATE wtfb2_users SET expansionPoints=expansionPoints-{0} WHERE (id={1})",$pendingSettings,$attVillage['ownerId']);
							// and give back as many expansion points to the victim
							runEscapedQuery("UPDATE wtfb2_users SET expansionPoints=expansionPoints+{0} WHERE (id={1})",$pendingSettings,$dstVillage['ownerId']);
						// the new owner can turn back the troops that's moving away. We must prevent this.
						runEscapedQuery("UPDATE wtfb2_events SET eventType='return' WHERE (eventType='move') AND (launcherVillage={0})",$dstVillage['id']);
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
					$r=runEscapedQuery("SELECT COUNT(*) AS villageCount FROM wtfb2_villages WHERE (ownerId={0})", $dstVillage['ownerId']);
					$a=$r[0][0];
					$villages=(int)$a['villageCount'];
					$r=runEscapedQuery("SELECT gold FROM wtfb2_users WHERE (id={0})",$dstVillage['ownerId']);
					if (isEmptyResult($r))
					{
					    $allGold = 0;
					}
					else
					{					
					    $a=$r[0][0];
					    $allGold=(double)$a['gold'];
					}
					$goldCanBeTaken=$allGold/$villages;
					$newEvent['gold']=round($goldCanBeTaken < $possibleGold ? $goldCanBeTaken : $possibleGold);
					// take the gold from the player
					runEscapedQuery("UPDATE wtfb2_users SET gold=gold-{0} WHERE (id={1})",$newEvent['gold'],$dstVillage['ownerId']);
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
				runEscapedQuery (
						"INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ({0},{1},{2},'scorechanged')",
						$dstVillage['x'],$dstVillage['y'],$event['happensAt']
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
			$r=runEscapedQuery
			(
				"SELECT id FROM wtfb2_heroes WHERE (inVillage={0})",
				$dstVillage['id']
			); 
			foreach ($r[0] as $row)
			{
				$heroesToDismiss[]=(int)$row['id'];
			}
			
			runEscapedQuery
			(
				"UPDATE wtfb2_heroes SET ownerId=0 WHERE (inVillage={0})",
				$dstVillage['id']
			);
		}
		// Now move the attacker hero to the conquered village (if we did that before we would dismiss the attacker hero too!)
		if ($conquered)
			runEscapedQuery("UPDATE wtfb2_heroes SET inVillage={0} WHERE (id={1})",$dstVillage['id'],$event['heroId']);
		// move out dismissed heroes
		if (count($heroesToDismiss)>0)
			moveFreeHeroes($event['happensAt'],$heroesToDismiss);
		$destroyed=false;
		if (($sumLevels==0) && ($simulationResult['defenderFalls']))
		{
			// give back expansion points to the victim for pending settlings if the village is not also conquered
			if (!$conquered)
			{
				$r=runEscapedQuery
				(
					"SELECT COUNT(*) AS pendingSettlingCount FROM wtfb2_events WHERE (launcherVillage={0}) AND (eventType='settle')"
					,$dstVillage['id']
				);
				$a=$r[0][0];
				$pendingSettings=$a['pendingSettlingCount'];
				// give back as many expansion points to the victim
				runEscapedQuery
				(
						"UPDATE wtfb2_users SET expansionPoints=expansionPoints+{0} WHERE (id={1})"
						,$pendingSettings,$dstVillage['ownerId']
				);
			}
			
			// delete the village
			runEscapedQuery("DELETE FROM wtfb2_villages WHERE (id={0})",$dstVillage['id']);
			// give expansion point to the victim
			runEscapedQuery("UPDATE wtfb2_users SET expansionPoints=expansionPoints+1 WHERE (id={0})",$dstVillage['ownerId']);

			runEscapedQuery
			(
			    "INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ({0},{1},{2},'destroy')",$dstVillage['x'],$dstVillage['y'],$event['happensAt']
			); // set up a world event
			$destroyed=true;
		}

		runEscapedQuery
		(
			"INSERT INTO wtfb2_worldevents (x,y,eventTime,type,recipientId)
			VALUES ({0},{1},{2},'eventhappened',{3})"
			,$dstVillage['x'],$dstVillage['y'],$event['happensAt'],$dstVillage['ownerId']
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
		if ($dstVillage['userId'] !== null)
		{
		    runEscapedQuery(
				    "INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,reportType,token) VALUES ({0},{1},{2},{3},{4},MD5(RAND()))",
				    $dstVillage['userId'],$reportTitle,$reportText,$event['happensAt'],$defenderReportType
		    );
		}
		
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
		runEscapedQuery
		(
			"INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,reportType,token) VALUES ({0},{1},{2},{3},{4},MD5(RAND()))"
			,$attVillage['userId'],$reportTitle,$reportText,$event['happensAt'],$attackerReportType
		);
		// so update the defender village.
		$updates=array();
		unset($dstVillage['userName']); // unsert user specific stuff
		unset($dstVillage['userId']);  // unsert user specific stuff
		unset($dstVillage['ownerGameTime']);  // unsert user specific stuff
		foreach($dstVillage as $key=>$value)
		{
			if ($key=='') continue; // WTF?
			$updates[]= sqlvprintf("$key={0}", array($value));
		}
		runEscapedQuery
		(
			"UPDATE wtfb2_villages SET ".implode(',',$updates)." WHERE (id={0})",
			$dstVillage['id']
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
	$r=runEscapedQuery("SELECT COUNT(*) AS villageCount FROM wtfb2_villages");
	$a=$r[0][0];
	$villageCount=$a['villageCount'];
	// select all abandoned or from the idsheroes that are in a village.
	$insetText="";
	if (count($heroIds)>0)
	{
		$insetText="AND (id IN (".implode(',',$heroIds)."))";
	}
	$freeHeroes=runEscapedQuery("SELECT * FROM wtfb2_heroes WHERE (ownerId=0) AND (inVillage<>0) $insetText"); // ids won't contain '-s so they are safe.
	foreach ($freeHeroes[0] as $freeHero)
	{
		// select the village the hero in
		$r=runEscapedQuery("SELECT * FROM wtfb2_villages WHERE (id={0})",$freeHero['inVillage']);
		if (isEmptyResult($r)) continue; // if village not found then the hero is lost.
		$village=$r[0][0];
		// select a 'random' destination village
		$r=runEscapedQuery("SELECT * FROM wtfb2_villages LIMIT ".mt_rand(0,$villageCount-1).",1"); // explain says ALL :(
		$dstVillage=$r[0][0];
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
			'launchedAt'=>sqlvprintf("{0}",array($currentDate)),
			'estimatedTime'=>sqlvprintf("TIMESTAMPADD(SECOND,{0},{1})",array((int)$travelTime,$currentDate)),
			'happensAt'=>sqlvprintf("TIMESTAMPADD(SECOND,{0},{1})",array((int)$realTime,$currentDate)),
			'launcherVillage'=>$village['id'],
			'destinationVillage'=>$dstVillage['id'],
			'heroId'=>$freeHero['id']
		);
		// leave the village
		runEscapedQuery("UPDATE wtfb2_heroes SET inVillage=0 WHERE (id={0})",$freeHero['id']);
		// then post that event
		postEvent($event);
	}
}

function moveFreeHeroesIfNeeded()
{
	global $config;
	$r=runEscapedQuery("SELECT TIMESTAMPDIFF(DAY,lastHeroMove,NOW()) AS needHeroMove FROM wtfb2_worldupdate");
	$a=$r[0][0];
	if ((int)$a['needHeroMove']>0)
	{
		moveFreeHeroes();
		$r=runEscapedQuery("UPDATE wtfb2_worldupdate SET lastHeroMove=CURDATE()"); // then update it.
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
        runEscapedQuery("DELETE FROM wtfb2_events WHERE (id IN (".implode(',',$toDelete)."))");	 //safe
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
	$r=runEscapedQuery("SELECT *,UNIX_TIMESTAMP(happensAt) AS eventTime FROM wtfb2_events WHERE (happensAt<=NOW())"); // hottest query in the game
	foreach ($r[0] as $row)
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
		runEscapedQuery("DELETE FROM wtfb2_events WHERE (id IN (".implode(',',$toDelete)."))");	//safe
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


?>
