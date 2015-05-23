<?php


$villages=array
(
	array
	(
		'rate'=>1,
		'enqueued'=>0,
		'id'=>1
	),
	array
	(
		'rate'=>1,
		'enqueued'=>0,
		'id'=>2
	),
	array
	(
		'rate'=>1,
		'enqueued'=>0,
		'id'=>3
	),
	array
	(
		'rate'=>1.21,
		'enqueued'=>1000,
		'id'=>4
	),
	array
	(
		'rate'=>1,
		'enqueued'=>0,
		'id'=>5
	)
);
$amountToTrain=20000;


function getTimeToFinish(&$v)
{
	return $v['enqueued']/$v['rate'];
}

function enqueue(&$v,$amount)
{
	$v['enqueued']+=$amount;
	if (!isset($v['newTraining'])) $v['newTraining']=0;
	$v['newTraining']+=$amount;
}

function compareVillage(&$v1,&$v2)
{
	return (getTimeToFinish($v1))-(getTimeToFinish($v2));
}

function planMassTraining(&$villages,$amountToTrain)
{
	
	usort($villages,compareVillage);

	$n=count($villages);
	for($j=0;$j<$n;$j++)
	{
		if ($j<$n-1)
		{
			$sumAmount=0;
			for($k=0;$k<=$j;$k++)
			{
				$diff=getTimeToFinish($villages[$j+1])-getTimeToFinish($villages[$k]);
				$amount=$diff*$villages[$k]['rate'];
				$sumAmount+=$amount;
			}
			if ($sumAmount==0) continue;
			$krate=(double)$amountToTrain/(double)$sumAmount;
			if ($krate>1) $krate=1;
			for($k=0;$k<=$j;$k++)
			{
				$diff=getTimeToFinish($villages[$j+1])-getTimeToFinish($villages[$k]);
				$amount=$diff*$villages[$k]['rate']*$krate;
				$amountToTrain-=$amount;
				enqueue($villages[$k],$amount);
			}
			if ($krate<1) break;
		}
		else
		{
			$sr;
			for($k=0;$k<$n;$k++)
			{
				$sr+=$villages[$k]['rate'];
			}
			$unitPerTimeUnit=$amountToTrain/$sr;
			for($k=0;$k<$n;$k++)
			{
				$amount=$unitPerTimeUnit*$villages[$k]['rate'];
				enqueue($villages[$k],$amount);	
				$amountToTrain-=$amount;
			}
		}
	}

	$frac=0;
	for($i=0;$i<$n;$i++)
	{
		$onumber=$villages[$i]['newTraining'];
		$number=floor($onumber);
		$f=$onumber-$number;
		$frac+=$f;
		$ffrac=floor($frac);
		$villages[$i]['newTraining']=$number+$ffrac;
		$frac-=$ffrac;
	}
}

function printStuff($v,$a)
{
	print_r($v);
	echo $a."\n";
	echo "======================================\n";
}

header('content-type: text/plain');

printStuff($villages,$amountToTrain);
planMassTraining($villages,$amountToTrain);
printStuff($villages,$amountToTrain);
printStuff($villages,$amountToTrain);
planMassTraining($villages,$amountToTrain);
printStuff($villages,$amountToTrain);

/*
	for(int j=0;j<villages.size();j++)
	{
		printStuff(villages,amountToTrain);
		if (j<villages.size()-1)
		{
			double sumAmount=0;
			for(int k=0;k<=j;k++)
			{
				double diff=villages[j+1].getTimeToFinish()-villages[k].getTimeToFinish();
				double amount=diff*villages[k].rate;
				sumAmount+=amount;
			}
			double krate=amountToTrain/sumAmount;
			if (krate>1) krate=1;
			for(int k=0;k<=j;k++)
			{
				double diff=villages[j+1].getTimeToFinish()-villages[k].getTimeToFinish();
				double amount=diff*villages[k].rate*krate;
				amountToTrain-=amount;
				villages[k].enqueue(amount);
			}
			if (krate<1) break;
		}
		else
		{
			double sr;
			for(int k=0;k<villages.size();k++)
			{
				sr+=villages[k].rate;
			}
			double unitPerTimeUnit=amountToTrain/sr;
			for(int k=0;k<villages.size();k++)
			{
				villages[k].enqueue(unitPerTimeUnit*villages[k].rate);	
			}
		}
	}

*/

?>
