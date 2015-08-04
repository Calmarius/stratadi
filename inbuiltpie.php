<?php

require_once('userworkerphps.php');

function colorHSV($hue,$saturation,$value)
{
	$hue%=360;
	if ($hue<0) $hue+=360;
	
	// calculate color from hue
	$red=(2-($hue>=240 ? 360-$hue:$hue)/60)*255;
	$green=(2-(abs(120-$hue))/60)*255;
	$blue=(2-(abs(240-$hue))/60)*255;
	
	if ($red>255) $red=255;
	if ($green>255) $green=255;
	if ($blue>255) $blue=255;
	if ($red<0) $red=0;
	if ($green<0) $green=0;
	if ($blue<0) $blue=0;
	// apply saturation
	$saturation/=256.0;
	$red=255*(1-$saturation)+$red*$saturation;
	$green=255*(1-$saturation)+$green*$saturation;
	$blue=255*(1-$saturation)+$blue*$saturation;
	// apply value
	$value/=256.0;
	$red*=$value;
	$green*=$value;
	$blue*=$value;
//	echo $red.' '.$green.' '.$blue.' ';
	return (round($red)<<16) + (round($green)<<8) + round($blue);
}

//echo '0x'.dechex(colorHSV(270,256,256));
//die();

function getInbuiltPie()
{
	global $config;
	global $serverLanguage;
	define('PADDING',20);
	define('ROW_SIZE',20);
	define('PIE_DIAMETER',200);
	define('PIE_CENTER',PIE_DIAMETER*0.5+PADDING);
	$fontPath = realpath('Verdana.ttf');

	$colors=
	array
	(
		0xFF0000,
		0x00FF00,
		0xFFFF00,
		0x0000FF,
		0xFF00FF,
		0x00FFFF,
		0xFFFFFFF,
		0x000000,
	
	);

	$buildingLevelDbNames=array();
	foreach($config['buildings'] as $key=>$value)
	{
		$buildingLevelDbNames[]=$value['buildingLevelDbName'];
	}

	$q=
	"
		SELECT SUM(".implode('+',$buildingLevelDbNames).") AS built,guildName
		FROM wtfb2_villages v
		JOIN wtfb2_users u ON (u.id=v.ownerId)
		LEFT JOIN wtfb2_guilds g ON (u.guildId=g.id)
		GROUP BY g.id
		ORDER BY built DESC
	";
	$r=runEscapedQuery($q);
	$sum=0;
	$count=0;
	$data=array();
	foreach ($r[0] as $row)
	{
		$data[]=$row;
		$sum+=$row['built'];
		$count++;
	}
	if ($count>17) $count=17;
	// get string sizes
	$maxWidth=0;
	$strings=array();
	$percents=array();
	$psum=0;
	for($i=0;$i<16;$i++)
	{
		if (!isset($data[$i])) break;
		$datarow=$data[$i];
		$builtLevels=(int)$datarow['built'];
		$percent=$builtLevels/(double)$sum;
		$percents[$i]=$percent;
		$psum+=$percent;
		$strings[$i]=($datarow['guildName']===null ? $serverLanguage['outsideguild']:$datarow['guildName']).' '.round($percent*100,2).'% ('.$builtLevels.')';
		$bbox=imagettfbbox(10,0,$fontPath,$strings[$i]);
		$width=$bbox[2]-$bbox[0];
		if ($maxWidth<$width) $maxWidth=$width;
	}
	if ($i==16)
	{
		$percents[16]=1-$psum;
		$strings[16]=$serverLanguage['others'];
	}

	$twWidth=$maxWidth>PIE_DIAMETER ? $maxWidth:PIE_DIAMETER;
	$imageWidth=2*PADDING+$twWidth;
	$imageHeight=2*PADDING+PIE_DIAMETER+$count*ROW_SIZE;
	$img=imagecreatetruecolor($imageWidth,$imageHeight);
	imagealphablending($img,false);
	imagefilledrectangle($img,0,0,$imageWidth,$imageHeight,0x7FFFFFFF);
	imagealphablending($img,true);
	$angle=0;
	$n=count($strings);
	for($i=0;$i<$n;$i++)
	{
		$size=$percents[$i]*360.0;
		$color=colorHSV($i*45,256,200);
		if ($size>=1)
			imagefilledarc($img,$imageWidth*0.5,PIE_CENTER,PIE_DIAMETER,PIE_DIAMETER,$angle,$angle+$size,$color,IMG_ARC_PIE);
		$angle+=$size;
		if (imagettftext($img,10,0,PADDING,2*PADDING+PIE_DIAMETER+$i*ROW_SIZE,$color, $fontPath ,$strings[$i]) === FALSE) die('Failed to draw text.');
	}
	imagesavealpha($img,true);
	return $img;
}

?>
