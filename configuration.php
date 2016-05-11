<?php

// TODO: (wishlist) Do not let players abandon their last village, or at least prevent the demo account do so.

$config=array
(
	'activationCodeLength' => 16,		// length of the activation code in characters
	'adminMail' => 'calmarius@calmarius.net',	// admin's mail
	'adminName' => 'Calmarius',		// admin's name
	'ageInteractionLimit'=>3,			// The age limit of the unit transfer. (If set 3, that means you can't send troops against a 3 or more times younger or older account.
	'armorBuilding'=>'wall', 			// the armor building in the game (the wall)
	'attackVectorDescription'=> array			// description of the attack vector (language entries)
	(
		0=>'infantryattack',
		1=>'archerattack',
		2=>'cavalryattack',
	),
	'avatarSize' => 200,				// avatars' dimensions in pixels
	'buildPointProducer'=>'townhall',		// build point producer building
	'buildings' => array				// building descriptors
	(
		'barracks' => array
		(
			'costFunction' => create_function('$level','return 10*pow(1.3,$level);'),		// cost of a particular level
			'jsCostFunction' => 'function(level){return 10*Math.pow(1.3,level);}',		// the same cost function in javascript for client side.
			'timeReductionFunction' => create_function('$level','return pow(0.9,$level);'),	// time reduction factor of the training on a particular level
			'buildingLevelDbName'=>'barracksLevel',							// the database column name of the building level
			'image'=>'img/barracks.png',									// icon that appear in the village view
			'languageEntry'=>'barracks' 									// name of the entry in the language file
		),
		'archeryrange' => array
		(
			'costFunction' => create_function('$level','return 15*pow(1.3,$level);'),
			'jsCostFunction' => 'function(level){return 15*Math.pow(1.3,level);}',
			'timeReductionFunction' => create_function('$level','return pow(0.9,$level);'),
			'buildingLevelDbName'=>'archeryRangeLevel',
			'image'=>'img/archeryrange.png',
			'languageEntry'=>'archeryrange'
		),
		'stables' => array
		(
			'costFunction' => create_function('$level','return 40*pow(1.3,$level);'),
			'jsCostFunction' => 'function(level){return 40*Math.pow(1.3,level);}',
			'timeReductionFunction' => create_function('$level','return pow(0.9,$level);'),
			'buildingLevelDbName'=>'stablesLevel',
			'image'=>'img/stables.png',
			'languageEntry'=>'stables'
		),
		'workshop' => array
		(
			'costFunction' => create_function('$level','return 100*pow(1.3,$level);'),
			'jsCostFunction' => 'function(level){return 100*Math.pow(1.3,level);}',
			'timeReductionFunction' => create_function('$level','return pow(0.9,$level);'),
			'buildingLevelDbName'=>'workshopLevel',
			'image'=>'img/workshop.png',
			'languageEntry'=>'workshop'
		),
		'townhall' => array
		(
			'costFunction' => create_function('$level','return 300*pow(1.3,$level);'),
			'jsCostFunction' => 'function(level){return 300*Math.pow(1.3,level);}',
			'timeReductionFunction' => create_function('$level','return pow(0.9,$level);'),
			'buildingLevelDbName'=>'townHallLevel',
			'bpProductionSpeedFunction'=>create_function('$level','return pow(1.1,$level);'),
			'image'=>'img/towncenter.png',
			'languageEntry'=>'townhall'
		),
		'goldmine' => array
		(
			'costFunction' => create_function('$level','return 0;'),
			'jsCostFunction' => 'function(level){return 0;}',
			'buildingLevelDbName'=>'goldmineLevel',
			'goldProductionSpeedFunction'=>create_function('$level','return $level*10;'),
			'image'=>'img/goldmine.png',
			'languageEntry'=>'goldmine'
		),
		'wall' => array
		(
			'costFunction' => create_function('$level','return 10*pow(1.2,$level);'),
			'jsCostFunction' => 'function(level){return 10*Math.pow(1.2,level);}',
			'buildingLevelDbName'=>'wallLevel',
			'image'=>'img/wall.png',
			'languageEntry'=>'wall'
		)
	),
	'buildingStrengthFunction'=>create_function('$level','return pow($level,1.7);'),						// calculate the building strength from level
	'buildingStrengthFunctionInverse'=>create_function('$bsp','if ($bsp<0) return 0; else return pow($bsp,1/1.7);'),   // calculate the level from the building strength (must inverse of the buildingStrengthFunction)
	'closed'=>false, // if the server is closed, then no further action can be started.
	'conquerorUnit'=>'_4diplomat',			// unit you conquer with
	'defenseVectorDescription'=> array			// description of the defense vector (language entries)
	(
		0=>'infantrydefense',
		1=>'archerdefense',
		2=>'cavalrydefense',
	),
	'demoAccountName' => 'demo',
	'experienceFunction'=>create_function('$xp','return floor(pow(max($xp, 0)*0.01,1.0/2.3));'), // experience to level function
	'experienceFunctionInverse'=>create_function('$level','return pow($level,2.3)*100;'), // level to experience function (must be inverse of the xp to level function)
	'experienceFunctionMySql'=>'FLOOR(POW(GREATEST(0, {1})*0.01,1.0/2.3))', // mysql format of the experience function. {1} is the column name substituted
	'facebookDefaultUrl' => 'http://calmarius.net/stratadi/xhu1/login.php',
	'facebookGroupLink' => htmlspecialchars('http://www.facebook.com/home.php?sk=group_192249450790540&ap=1'), // Link to the Facebook group (you may put it into the main page)
	'facebookImageLink' => 'http://calmarius.net/stratadi/xhu1/img/town7.png', // Image to be shown on Facebook
	'forceUpdatePeriod'=>'3600',				// For performance reasons. When a player gets massive amount of attacks we don't update the player every time. The interval here describes the minimal amount of time (in seconds) that must be elapsed before the next update.
	'forumLink' => 'http://calmarius.net/forum', // Link to a forum, you may put it into the main page.
	'gameStarted'=>'2015-05-24 0:00:00',
	'goldProducer'=>'goldmine',
	'guildPermissions'=>array  // permission types in a guild
	(
		'circular' => array
		(
			'langName'=>'sendcircular'
		),
		'diplomacy' => array
		(
			'langName'=>'managediplomacy'
		),
		'dismiss' => array
		(
			'langName'=>'dismissguild'
		),
		'editprofile' => array
		(
			'langName'=>'editguildprofile'
		),
		'grantrights' => array
		(
			'langName'=>'grantrightstoplayers'
		),
		'invite' => array
		(
			'langName'=>'inviteplayer'
		),
		'kick' => array
		(
			'langName'=>'kickplayer'
		),
		'moderate' => array
		(
			'langName'=>'moderateforum'
		)
	),
	'heroAttackFormula'=>create_function('$level','return 1+$level*0.1;'), // attack factor formula for hero
	'heroDefendFormula'=>create_function('$level','return 1+$level*0.1;'), // defend factor formula for hero.
	'heroSpeed'=>20,					// speed of the hero
	'imageRoot' => 'imageroot',            // Root URL of ceratin images to save space on the host.
	'latitude'=>47.498333333,				// latitude of the game. North latitudes are positive.
	'longitude'=>19.040833333,			// longitude of the game. Eastern longitudes are positive.
	'maxFreeHeroTravelDistance'=>100, 		// maximum distance, the free heroes can travel.
	'maxInsertRecordsPerQuery'=> 400, 		// maximum number of records inserted once.
	'maxSettleAttempts' => 3,				// maximum attempt to settle a village
	'maxUserNameLength' => 20,				// maximal user name length
	'minimalArmyValueRate'=>0.24,			// this number is multiplied by the players gold production when sendng an army.
	'minUserNameLength' => 4,				// minimal user name length
	'minHeroNameLength' => 4,				// minimal hero name length
	'minUserPasswordLength' => 6,			// minimum password length
	'newVillageAreaRadius' => 20,				// the difference of the inner and outer radius of the ring where new players appear.
	'newVillageAreaIncreasePerDay' => 1000,	// The newbie area increases after every second specified here. Don't modify this on a running server.
	'nightBonusMax'=>3, 					// night bonus multiplier
	'openAccounts'=>array('demo'), 				// accounts that does not require password
	'operations'=> array
	(
		'attack'=>array
		(
			'color'=>'#FFDDDD', // must use pastell colors
			'langDesc'=>'attackevent', // language entry to the description of the event
			'langName'=>'attack' 	// name entry in the language file
		),
		'heromove'=>array
		(
			'color'=>'transparent',
			'langDesc'=>'moveevent',
			'langName'=>'heromove',
		),
		'incomingattack'=>array
		(
			'color'=>'#FFAAAA',
			'langDesc'=>'attackevent',
			'langName'=>'incomingattack'
		),
		'move'=>array
		(
			'color'=>'transparent',
			'langDesc'=>'moveevent',
			'langName'=>'movetroops'
		),
		'raid'=>array
		(
			'color'=>'#FFFFDD',
			'langDesc'=>'attackevent',
			'langName'=>'raid'
		),
		'recon'=>array
		(
			'color'=>'#DDDDFF',
			'langDesc'=>'attackevent',
			'langName'=>'recon'
		),
		'return'=>array
		(
			'color'=>'#DDFFDD',
			'langDesc'=>'returnevent',
			'langName'=>'return'
		),
		'settle'=>array
		(
			'color'=>'#DDFFFF',
			'langDesc'=>'settleevent',
			'langName'=>'settlevillage'
		),
	),
	'pageSize'=>20,
	'payment'=>array
	(
		'stratadverts'=>false,
		'subscriptionFee'=>0,
		'buyArmy'=>false,
		'buyGold'=>false,
		'buyExpasionPoints'=>false,
		'mercenaryForRealMoney'=>false
	),
	'referredRewardVillageCount'=>8,			// villages needed for a referred player to reward the referer.
	'reportTypes'=> array
	(
		'unknown'=>array
		(
			'image'=>'img/unknownreport.png',
			'langName'=>'unknownreport'
		),
		'defensenoloss'=>array
		(
			'image'=>'img/defensenoloss.png',
			'langName'=>'defensenoloss'
		),
		'defensewithloss'=>array
		(
			'image'=>'img/defensewithloss.png',
			'langName'=>'defensewithloss'
		),
		'defensefail'=>array
		(
			'image'=>'img/defensefail.png',
			'langName'=>'defensefail'
		),
		'attacknoloss'=>array
		(
			'image'=>'img/attacknoloss.png',
			'langName'=>'attacknoloss'
		),
		'attackwithloss'=>array
		(
			'image'=>'img/attackwithloss.png',
			'langName'=>'attackwithloss'
		),
		'attackfail'=>array
		(
			'image'=>'img/attackfail.png',
			'langName'=>'attackfail'
		),
		'gotvillagebyconquer'=>array
		(
			'image'=>'img/conqueredvillage.png',
			'langName'=>'gotvillagebyconquer'
		),
		'lostvillagebyconquer'=>array
		(
			'image'=>'img/lostvillage.png',
			'langName'=>'lostvillagebyconquer'
		),
		'destroyedvillage'=>array
		(
			'image'=>'img/destroyedvillage.png',
			'langName'=>'destroyedvillage'
		),
		'lostvillagebydestruct'=>array
		(
			'image'=>'img/lostdestroyedvillage.png',
			'langName'=>'lostvillagebydestruct'
		),
		'adminmessage'=>array
		(
			'image'=>'img/systemmessage.png',
			'langName'=>'adminmessage'
		),
		'incomingmove'=>array
		(
			'image'=>'img/transferin.png',
			'langName'=>'incomingmove'
		),
		'outgoingmove'=>array
		(
			'image'=>'img/transferout.png',
			'langName'=>'outgoingmove'
		)
	),
	'serverLanguage'=>'hu',         // Server's language.
	'serverSpeed'=>100,					// server speed
	'settlerUnit'=>'_4diplomat',				// unit you can settle width
	'startGold'=>'500',					// start amount of gold
	'superiorityExponent'=>'1.5',			// superiority exponent
	'timezone'=>1,						// timezone of the server the GMT+ value
	'units' => array						// The unit descriptor array
	(
		'_0spearman' => array
		(
			'attack' => array
			(
				'0' => 60,
				'1' => 0,
				'2' => 0
			),
			'cost' => 10,
			'countDbName' => 'spearmen',
			'defense' => array
			(
				'0' => 60,
				'1' => 30,
				'2' => 120
			),
			'image'=>'img/spearman.png',
			'speed' => 6,
			'trainedAt' => 'barracks',
			'trainingDbName'=>'spearmenTraining',
			'trainingTime' => 600,
			'languageEntry'=>'spearmen',
			'singularLanguageEntry'=>'spearman',
			'strength'=>15  // amount of gold the unit can carry.
		),
		'_1archer' => array
		(
			'attack' => array				// attack vector of an unit
			(
				'0' => 0,
				'1' => 60,
				'2' => 0
			),
			'cost' => 15,					// cost in gold
			'countDbName' => 'archers',			// the column name in the database
			'defense' => array				// the defense vector
			(
				'0' => 120,
				'1' => 60,
				'2' => 30
			),
			'image'=>'img/archer.png',
			'speed' => 7,					// the unit's speed (cells/hour)
			'trainedAt' => 'archeryrange',		// the building name where it's trained must match the name in the $config['buildings']
			'trainingDbName'=>'archersTraining', // the training count name in the database
			'trainingTime' => 900,				// Training time for a first level building
			'languageEntry'=>'archers', 				// Plural name of the unit
			'singularLanguageEntry'=>'archer',		// Singular name of the unit
			'strength'=>10  // amount of gold the unit can carry.
		),
		'_2knight' => array
		(
			'attack' => array
			(
				'0' => 0,
				'1' => 0,
				'2' => 120
			),
			'cost' => 25,
			'countDbName' => 'knights',
			'defense' => array
			(
				'0' => 60,
				'1' => 240,
				'2' => 120
			),
			'image'=>'img/knight.png',
			'speed' => 12,
			'trainedAt' => 'stables',
			'trainingDbName'=>'knightsTraining',
			'trainingTime' => 1200,
			'languageEntry'=>'knights',
			'singularLanguageEntry'=>'knight',
			'strength'=>50  // amount of gold the unit can carry.
		),
		'_3catapult' => array
		(
			'attack' => array
			(
				'0' => 20,
				'1' => 20,
				'2' => 20
			),
			'cost' => 100,
			'countDbName' => 'catapults',
			'defense' => array
			(
				'0' => 60,
				'1' => 60,
				'2' => 60
			),
			'image'=>'img/catapult.png',
			'demolisher'=>true,
			'speed' => 3,
			'trainedAt' => 'workshop',
			'trainingDbName'=>'catapultsTraining',
			'trainingTime' => 3600,
			'languageEntry'=>'catapults',
			'singularLanguageEntry'=>'catapult',
			'strength'=>30  // amount of gold the unit can carry.
		),
		'_4diplomat' => array
		(
			'attack' => array
			(
				'0' => 20,
				'1' => 20,
				'2' => 20
			),
			'cost' => 1000,
			'countDbName' => 'diplomats',
			'defense' => array
			(
				'0' => 20,
				'1' => 20,
				'2' => 20
			),
			'image'=>'img/diplomat.png',
			'speed' => 1,
			'trainedAt' => 'townhall',
			'trainingDbName'=>'diplomatsTraining',
			'trainingTime' => 86400,
			'languageEntry'=>'diplomats',
			'singularLanguageEntry'=>'diplomat',
			'strength'=>5  // amount of gold the unit can carry.
		)
	),
	'villageScoreFunction'=>create_function('$bLevelNames', 			// this function is used to generate the sql query for the village score. The argument is the array of building level names.
	'	$queryArray=array();
		foreach($bLevelNames as $key=>$value)
		{
			$queryArray[]="$value*$value";
		}
		return implode("+",$queryArray);
	'),
	'worldEvents'=>array
	(
		'settle'=>array
		(
			'langEntry'=>'worldeventsettle'
		),
		'destroy'=>array
		(
			'langEntry'=>'worldeventdestroy'
		),
		'conquer'=>array
		(
			'langEntry'=>'worldeventconquer'
		),
		'guildchange'=>array
		(
			'langEntry'=>'worldeventguildchange'
		),
		'rename'=>array
		(
			'langEntry'=>'worldeventrename'
		),
		'eventhappened'=>array
		(
			'langEntry'=>'worldeventeventhappened'
		),
		'diplomacychanged'=>array
		(
			'langEntry'=>'worldeventdiplomacychanged'
		),
		'scorechanged'=>array
		(
			'langEntry'=>'worldeventscorechanged'
		),
		'abandon'=>array
		(
			'langEntry'=>'abandonvillage'
		)
	)
);


?>
