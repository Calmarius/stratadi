<?php

/*require_once("presenterphps.php");

$latitude=$config['latitude'];
$longitude=$config['longitude'];
$timezone=$config['timezone'];

for($i=-18;$i<=5;$i++)
{
	echo "Sun rises above ${i}°: ".date_sunrise(time(),SUNFUNCS_RET_STRING,$latitude,$longitude,90-$i,$timezone)."<br>";
}
echo "<br>";
for($i=5;$i>=-18;$i--)
{
	echo "Sun set below ${i}°: ".date_sunset(time(),SUNFUNCS_RET_STRING,$latitude,$longitude,90-$i,$timezone)."<br>";
}*/

require_once("configuration.php");

function getNightBonusInfo($at)
{
	global $config;
	$latitude=$config['latitude'];
	$longitude=$config['longitude'];
//	$timezone=$config['timezone'];
	$timezone=((int)date('Z'))/3600;
	$cTime=$at;
	$found=false;
	$index=0;
	$i;
	for($i=-18;$i<=5;$i++)
	{
		$time=(int)date_sunrise($at,SUNFUNCS_RET_TIMESTAMP,$latitude,$longitude,90-$i,$timezone)."<br>";
		if (!$time) continue;
		if ($time>$cTime)
		{
			$index=$i;
			$found=true;
			break; 
		}
	}
	if (!$found)
	{
		for($i=5;$i>=-18;$i--)
		{
			$time=(int)date_sunset($at,SUNFUNCS_RET_TIMESTAMP,$latitude,$longitude,90-$i,$timezone)."<br>";
			if (($time>$cTime) || (!$time))
			{
				$index=$i+1;
				$found=true;
				break;
			}
		}
	}
	if (!$found)
	{
		$index=$i+1;	
	}
	$nightScale=$index<0 ? -$index/18: 0;
	$nightBonus=((1-$nightScale)+$nightScale*$config['nightBonusMax']);
	return array('bonus'=>$nightBonus,'index'=>$index);
}

/*$cTime=time()+20000;
echo "The current time is: ".date("G:i",$cTime)."<br>";
$nbInfo=getNightBonusInfo($cTime);
echo "The night bonus index is: ".$nbInfo['index']."<br>";
echo "The night bonus multiplier is: ".$nbInfo['bonus']."<br>";*/















?>
