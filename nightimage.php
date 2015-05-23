<?php

require_once("nightbonus.php");

$nbInfo=getNightBonusInfo(time());

$colorArray=array
(
	6=>array('r'=>1.0,'g'=>1.0,'b'=>1.0),
	5=>array('r'=>1.0,'g'=>1.0,'b'=>1.0),
	4=>array('r'=>1.0,'g'=>0.95,'b'=>0.9),
	3=>array('r'=>1.0,'g'=>0.9,'b'=>0.8),
	2=>array('r'=>1.0,'g'=>0.85,'b'=>0.7),
	1=>array('r'=>1.0,'g'=>0.8,'b'=>0.6),
	0=>array('r'=>1.0,'g'=>0.75,'b'=>0.5),
	-1=>array('r'=>1.0,'g'=>0.75,'b'=>0.5),
	-2=>array('r'=>0.95,'g'=>0.725,'b'=>0.5),
	-3=>array('r'=>0.9,'g'=>0.7,'b'=>0.5),
	-4=>array('r'=>0.85,'g'=>0.675,'b'=>0.5),
	-5=>array('r'=>0.8,'g'=>0.65,'b'=>0.5),
	-6=>array('r'=>0.75,'g'=>0.625,'b'=>0.5),
	-7=>array('r'=>0.7,'g'=>0.6,'b'=>0.5),
	-8=>array('r'=>0.65,'g'=>0.575,'b'=>0.5),
	-9=>array('r'=>0.6,'g'=>0.55,'b'=>0.5),
	-10=>array('r'=>0.55,'g'=>0.525,'b'=>0.5),
	-11=>array('r'=>0.5,'g'=>0.5,'b'=>0.5),
	-12=>array('r'=>0.45,'g'=>0.475,'b'=>0.5),
	-13=>array('r'=>0.4,'g'=>0.45,'b'=>0.5),
	-14=>array('r'=>0.35,'g'=>0.425,'b'=>0.5),
	-15=>array('r'=>0.3,'g'=>0.4,'b'=>0.5),
	-16=>array('r'=>0.20,'g'=>0.35,'b'=>0.5),
	-17=>array('r'=>0.1,'g'=>0.3,'b'=>0.5),
	-18=>array('r'=>0.0,'g'=>0.25,'b'=>0.5)
);

$color=$colorArray[$nbInfo['index']];
$red=$color['r'];
$green=$color['g'];
$blue=$color['b'];

$_GET['img']=str_replace('.','',$_GET['img']);
$_GET['img']=str_replace('/','',$_GET['img']);
$img=imagecreatefrompng('img/'.$_GET['img'].'.png');

$xs=imagesx($img);
$ys=imagesy($img);

for($i=0;$i<$xs;$i++)
{
	for($j=0;$j<$ys;$j++)
	{
		$clr=imagecolorat($img,$i,$j);
		$r=(($clr >> 16)&255)*$red;
		$g=(($clr >> 8)&255)*$green;
		$b=($clr & 255)*$blue;
		imagesetpixel($img,$i,$j,($b) | ($g<<8) | ($r<<16));
	}
}

header('Content-type: image/png');
imagepng($img);


?>
