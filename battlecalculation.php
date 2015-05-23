<?php

require_once('configuration.php');

function calculateBattleCasualties($params)
{
	/**
	input:
	.agebonus: defender's age Bonus.
	.attacker_<unittype>: units on the attacker side
	.attackerhero: attacker hero level
	.defender_<unittype>: units on defender side
	.defenderhero: defender hero level
	.mode: attack mode (attack,raid or recon)
	.nightbonus: night bonus for the defender
	.targetlevel: level of the targeted building.
	.targetwall: set when the taret is the wall
	.walllevel: level of the wall	
	
	output:
	.attackerFalls: attacker fallen
	.attackerHeroXP: XP the attacker's hero would get
	.attackerNoCasualty: indicates that none of the attacker's troops killed
	.attacker.casualties.<unittype>: attacker's loss
	.defender.casualties.<unittype>: defender's loss
	.defender.targetdemolished: the new level of the target.
	.defenderFalls: attacker fallen
	.defenderHeroXP: XP the defender's hero would get
	.defenderNoCasualty: indicates that none of the defender's troops killed
	.wouldConquer: indicates the attacker can conquer the destination if he have enough expansion points.
	
	*/
	global $config;
	
	if (!isset($params['agebonus'])) $params['agebonus']=1;
	
	$retVal=array();
	
	$defenderVector=array();
	$attackerVector=array();
	$baseDefenderVector=array();
	$baseAttackerVector=array();
	$demolishers=0;
	$heroAttackFormula=$config['heroAttackFormula'];
	$heroDefendFormula=$config['heroDefendFormula'];
	// we calculate the base attack and defense vectors.
	foreach($config['units'] as $key=>$unitDescriptor)
	{
		$retVal['defender']['casualties'][$key]=0;
		$retVal['attacker']['casualties'][$key]=0;
		foreach($unitDescriptor['defense'] as $key2=>$defAmount)
		{
			if (!isset($baseDefenderVector[$key2])) $baseDefenderVector[$key2]=0;
			$unitCount=(int)floor($params['defender_'.$key]);
			if ($unitCount<0) $unitCount=0;
			$baseDefenderVector[$key2]+=$defAmount*$unitCount*$heroAttackFormula($params['defenderhero'])*(double)$params['nightbonus']*(double)$params['agebonus'];
		}
		foreach($unitDescriptor['attack'] as $key2=>$attAmount)
		{
			if (!isset($baseAttackerVector[$key2])) $baseAttackerVector[$key2]=0;
			$unitCount=(int)floor($params['attacker_'.$key]);
			if ($unitCount<0) $unitCount=0;
			$baseAttackerVector[$key2]+=$attAmount*(int)$params['attacker_'.$key]*$heroDefendFormula($params['attackhero']);
		}
		if (isset($unitDescriptor['demolisher']))
		{
			$demolishers+=$params['attacker_'.$key];
		}
	}
	// we calculate here the final attack and defense vectors
	foreach($baseDefenderVector as $key=>$value)
	{
		$defenderVector[$key]=$value*($params['walllevel']+1);
	}
	foreach($baseAttackerVector as $key=>$value)
	{
		$attackerVector[$key]=$value;
	}
	// we calculate here the power ratio.
	$noDefender=false;
	$powerRatio=0;
	foreach($attackerVector as $key=>$value)
	{
		$attValue=$value;
		$defValue=$defenderVector[$key];
		if ($defValue==0) 
		{
			$noDefender=true;
			break;
		}	
		$powerRatio+=$attValue/$defValue;
	}
	$attackerCasualtyRatio=0;
	$defenderCasualyRatio=0;
	$catsFireRatio=0;
	// we calculate here the casualties.
	if (!$noDefender)
	{
		// we calculate the casualties for normal attack.
		if ($params['mode']=='attack')
		{
			// if the target is wall, then we have to do a different calculation
			if (isset($params['targetwall']))
			{
				// first we calculate the result of a raid.
				$baseDefenderCasualtyRatio=$powerRatio/($powerRatio+1);
				$catsFireRatio=$baseDefenderCasualtyRatio;
				$baseAttackerCasualtyRatio=1-$baseDefenderCasualtyRatio;
/*				$baseDefenderCasualtyRatio=pow($baseDefenderCasualtyRatio,$config['superiorityExponent']);
				$baseAttackerCasualtyRatio=pow($baseAttackerCasualtyRatio,$config['superiorityExponent']);*/
				$bStrengthFn=$config['buildingStrengthFunction'];
				$bStrengthFnInv=$config['buildingStrengthFunctionInverse'];
				// after this phase we calculate the new wall level
				if (isset($params['targetwall']))
					$params['walllevel']=ceil($bStrengthFnInv($bStrengthFn($params['walllevel'])-$catsFireRatio*$demolishers));
				// so we need to recalculate all the vectors
				foreach($baseDefenderVector as $key=>$value)
				{
					$defenderVector[$key]=$value*(1-$baseDefenderCasualtyRatio)*($params['walllevel']+1);
				}
				foreach($baseAttackerVector as $key=>$value)
				{
					$attackerVector[$key]=$value*(1-$baseAttackerCasualtyRatio);
				}
				// and the new power ratio
				$powerRatio=0;
				foreach($attackerVector as $key=>$value)
				{
					$attValue=$value;
					$defValue=$defenderVector[$key];
					$powerRatio+=$attValue/$defValue;
				}
				if ($powerRatio>=1)
				{
					$additionalAttackerCasualtyRatio=1/$powerRatio;
					$additionalDefenderCasualtyRatio=1;
				}
				else
				{
					$additionalAttackerCasualtyRatio=1;
					$additionalDefenderCasualtyRatio=$powerRatio;
				}
				// so we know the casualty final ratio here
				$attackerCasualtyRatio=$baseAttackerCasualtyRatio+(1-$baseAttackerCasualtyRatio)*$additionalAttackerCasualtyRatio;
				$defenderCasualtyRatio=$baseDefenderCasualtyRatio+(1-$baseDefenderCasualtyRatio)*$additionalDefenderCasualtyRatio;
			}
			else
			{
				// its stratightforward to know how the casualties here.
				if ($powerRatio>=1)
				{
					$attackerCasualtyRatio=1/$powerRatio;
					$defenderCasualtyRatio=1;
				}
				else
				{
					$attackerCasualtyRatio=1;
					$defenderCasualtyRatio=$powerRatio;
				}
				$catsFireRatio=$powerRatio/($powerRatio+1);
			}
			// apply superiority bonus
			$defenderCasualtyRatio=pow($defenderCasualtyRatio,$config['superiorityExponent']);
			$attackerCasualtyRatio=pow($attackerCasualtyRatio,$config['superiorityExponent']);
		}
		else if ($params['mode']=='raid')
		{
			// for raids the summary of the casualty ratios are 1
			$defenderCasualtyRatio=$powerRatio/($powerRatio+1);
			$attackerCasualtyRatio=1-$defenderCasualtyRatio;
			$defenderCasualtyRatio=pow($defenderCasualtyRatio,$config['superiorityExponent']);
			$attackerCasualtyRatio=pow($attackerCasualtyRatio,$config['superiorityExponent']);
		}
		else if ($params['mode']=='recon')
		{
			// recon attack is a bit different than the raid. defender have superiority bonus, so the attacker.
			$defenderCasualtyRatio=$powerRatio/($powerRatio+1);
			$attackerCasualtyRatio=1-$defenderCasualtyRatio;
			$attackerCasualtyRatio=pow($attackerCasualtyRatio,1+$attackerCasualtyRatio);
			$defenderCasualtyRatio=pow($defenderCasualtyRatio,2);
		}
		else die('Invalid mode passed to the battle simulation. This should not happen in normal circumstances. Please report it to the admin if you see this.');
	}
	if ($noDefender)
	{
	 	if ($params['mode']=='attack') $catsFireRatio=1;
	 	$defenderCasualtyRatio=1;
	 	$attackerCasualtyRatio=0;
	}
	$demsFired=$demolishers*$catsFireRatio;
	$retVal['attackerHeroXP']=0;
	$retVal['defenderHeroXP']=0;
	foreach($config['units'] as $key=>$unitDescriptor)
	{
		$retVal['attackerHeroXP']+=$retVal['defender']['casualties'][$key]=(int)round(floor($params['defender_'.$key])*$defenderCasualtyRatio);
		$retVal['defenderHeroXP']+=$retVal['attacker']['casualties'][$key]=(int)round(floor($params['attacker_'.$key])*$attackerCasualtyRatio);
	}
	$retVal['defenderFalls']=true;
	$retVal['attackerFalls']=true;
	$retVal['defenderNoCasualties']=true;
	$retVal['attackerNoCasualties']=true;
	foreach($config['units'] as $key=>$unitDescriptor)
	{
		if ($retVal['defender']['casualties'][$key]>=1) $retVal['defenderNoCasualties']=false;
		if ($retVal['attacker']['casualties'][$key]>=1) $retVal['attackerNoCasualties']=false;
		if ($retVal['defender']['casualties'][$key]!=$params['defender_'.$key]) $retVal['defenderFalls']=false;
		if ($retVal['attacker']['casualties'][$key]!=$params['attacker_'.$key]) 	$retVal['attackerFalls']=false;
	}
	// would conquer?
	$conquerorUnit=$config['conquerorUnit'];
	$retVal['wouldConquer']=($params['mode']=='attack') && ($params['attacker_'.$conquerorUnit]>$retVal['attacker']['casualties'][$conquerorUnit]);
	$bStrengthFn=$config['buildingStrengthFunction'];
	$bStrengthFnInv=$config['buildingStrengthFunctionInverse'];
//	print_r($demolishers);
	$retVal['defender']['targetdemolished']=$demsFired>0 ? ceil($bStrengthFnInv($bStrengthFn($params['targetlevel'])-$demsFired)):$params['targetlevel'];
	return $retVal;
}

?>
