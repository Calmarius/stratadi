<?php

function setLevels(&$levelsArray,&$bpArray,$spareArray,$maxGold,$maxLevel,$costFn)
{
	$tmpLevels=$levelsArray;
	$n=count($levelsArray);
	$spent=0;
	$maxIter=10;
	$built=false;
	for($i=0;$i<$n;$i++)
	{
		$iLevel=$tmpLevels[$i];
	
		if (($iLevel>=$maxLevel) && ($maxLevel>0))
		{
			echo "X";
			return $spent;
		}
	
		$bLevel=$levelsArray[$i];
		$uCost=$costFn($bLevel);
		$bp=$bpArray[$i];
	
		if ($bp>=$spareArray[$i]+1) 
		{
			if ($spent+$uCost>$maxGold)
			{
				echo "Y";
				return $spent;
			}		
			$levelsArray[$i]++;
			$bpArray[$i]--;
			$spent+=$uCost;
			$built=true;
		}
	
		$tmpLevels[$i]++;
	
	
		if ($i+1<$n)
		{
			if ($tmpLevels[$i+1]>$iLevel)
			{
				$i=-1;
				$built=false;
			}
		}
		else
		{
			if ((($iLevel<$maxLevel) || ($maxLevel==-1)) && ($built))
			{
				$i=-1;
				$built=false;
			}
		}
		
		echo "tmpArray:\n";
		print_r($tmpLevels);
		echo "levelArray:\n";
		print_r($levelsArray);
		echo "bpArray:\n";
		print_r($bpArray);
		echo "-----------\n";
		$maxIter--;
//		if ($maxIter<=0) return $spent;
	}
	return $spent;
}

$levels=array(5,6,6,7,8, 10,11,12,13,13);
$bpArray=array(1,7,10,1,1, 4,8,1,10,11);
$spareArray=array(0,5,0,0,0, 0,0,0,0,0);

echo '<pre>';
print_r($levels);
print_r($bpArray);

$spent=setLevels($levels,$bpArray,$spareArray,100000,30,create_function('$level','return 300*pow(1.3,$level);'));

echo 'Spent '.$spent.' gold'."\n\n";

print_r($levels);
print_r($bpArray);

echo '</pre>';



?>
