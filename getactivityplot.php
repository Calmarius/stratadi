<?php

ini_set('memory_limit', '-1');

require_once("setupsession.php");// outputs image don't user userworkerphps.php then.
require_once("setupmysqli.php");// outputs image don't user userworkerphps.php then.
require_once("utils/gameutils.php");
bounceSessionOver();
$r=runEscapedQuery("SELECT * FROM wtfb2_accesses WHERE (id={0}) AND (permission='admin')",$_SESSION['accessId']);
$isAdmin=!isEmptyResult($r);

$g=-(int)$_GET['daybefore'];
$plotWidth=(int)$_GET['width'];
if ($plotWidth<1000) $plotWidth=1000;
$byIp=isset($_GET['byip']);

$restrictUser='';
if (!$isAdmin)
	$restrictUser=sqlvprintf("AND (u.id={0})",array($_SESSION['userId']));

$r=runEscapedQuery("SELECT ADDDATE(CURDATE(),INTERVAL {0} DAY) AS imageDate",$g);
$curDate=$r[0][0];
$curDate=$curDate['imageDate'];

$q=
sqlvprintf(
"
	SELECT r.*,u.userName,TIMESTAMPDIFF(SECOND,DATE(requestTime),requestTime) AS secondsDay
	FROM `wtfb2_requestlog` r
	JOIN wtfb2_users u ON (r.userId=u.id)
	WHERE (requestTime BETWEEN ADDDATE(CURDATE(),INTERVAL {0} DAY) AND ADDDATE(CURDATE(),INTERVAL ({0}+1) DAY) ) $restrictUser
",array($g)
);

$r=runEscapedQuery($q);
$selectedData=array();
$first=true;
$time;
foreach ($r[0] as $row)
{
	if ($first) $time=$row['requestTime'];
	$first=false;
	$key=$byIp ? $row['clientIP'].'@'.$row['userName'] : $row['userName'].'@'.$row['clientIP'];
	$selectedData[$key][]=$row;
}
$lines=count(array_keys($selectedData));

$titleHeight=20;
$lineHeight=15;
$extraPadding=10;
$nameBarWidth=200;
$writeNameStep=500;
$fluentStyle=array(0xFFFFFF);
$legendHeight=30;
$dashedStyle=array(0x404080,0x404080,0x404080,0x404080,0,0);

$imageHeight=$lines*$lineHeight+$titleHeight;

$img=imagecreatetruecolor($plotWidth+$nameBarWidth+$extraPadding,$imageHeight+$extraPadding+$legendHeight);

// put title
$fontPath = realPath('Verdana.ttf');
imagettftext($img,8,0,0,12,0xFFFFFF,$fontPath,"Activity plot beginning from: ".$time);
// put hour lines
for($i=0;$i<=24;$i++)
{
	if ($i%6)
		$color=0x800000;
	else
		$color=0xFF0000;
	imageline($img,$nameBarWidth+$i*($plotWidth/24),$titleHeight,$nameBarWidth+$i*($plotWidth/24),$imageHeight,$color);
	imagettftext($img,8,0,$nameBarWidth+$i*($plotWidth/24),$titleHeight,0x303030,$fontPath,$i);
}
// plot data
$height=$titleHeight+$lineHeight;
imagesetstyle($img,$dashedStyle);
ksort($selectedData);
foreach($selectedData as $key=>$value)
{
	// write name@IP
	imagettftext($img,8,0,0,$height,0xFFFFFF,$fontPath,$key);
	for($x=$writeNameStep;$x<$plotWidth;$x+=$writeNameStep)
	{
		imagettftext($img,8,0,$x,$height,0x303030,$fontPath,$key);
	}
	// put lines below names
	imageline($img,0,$height,$plotWidth+$nameBarWidth+$extraPadding,$height,IMG_COLOR_STYLED);
	// plot activities
	foreach($selectedData[$key] as $key2=>$value2)
	{
		$sd=(int)$value2['secondsDay'];
		$location=$plotWidth*($sd/86400.0);
		$color=0xFFFFFF;
		if ($value2['requestType']=='normal') $color=0x00FF00;
		else if ($value2['requestType']=='deputy') $color=0x0000FF;
		else if ($value2['requestType']=='admin') $color=0xFFFF00;
		imageline($img,$location+$nameBarWidth,$height,$location+$nameBarWidth,$height-$lineHeight,$color);
	}
	$height+=$lineHeight;
}
// draw legend
$x=5;
imagefilledrectangle($img,$x,$height,$x+20,$height+20,0x00FF00);
$x+=25;
$text=$language['youractivity'];
$bbox=imagettfbbox(10,0,$fontPath,$text);
$w=$bbox[2]-$bbox[0];
imagettftext($img,10,0,$x,$height+10,0xFFFFFF,$fontPath,$text);
$x+=$w+5;

imagefilledrectangle($img,$x,$height,$x+20,$height+20,0x0000FF);
$x+=25;
$text=$language['sitteractivity'];
$bbox=imagettfbbox(10,0,$fontPath,$text);
$w=$bbox[2]-$bbox[0];
imagettftext($img,10,0,$x,$height+10,0xFFFFFF,$fontPath,$text);
$x+=$w+5;

imagefilledrectangle($img,$x,$height,$x+20,$height+20,0xFFFF00);
$x+=25;
$text=$language['adminactivity'];
$bbox=imagettfbbox(10,0,$fontPath,$text);
$w=$bbox[2]-$bbox[0];
imagettftext($img,10,0,$x,$height+10,0xFFFFFF,$fontPath,$text);
$x+=$w+5;

header('Content-type: image/png');
header('Content-disposition: inline; filename='.$curDate.'.png');
imagepng($img);


?>
