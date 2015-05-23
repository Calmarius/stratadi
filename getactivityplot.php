<?php

ini_set('memory_limit', '-1');

require_once("setupsession.php");// outputs image don't user userworkerphps.php then.
require_once("setupmysql.php");// outputs image don't user userworkerphps.php then.
require_once("utils/gameutils.php");
bounceSessionOver();
//bounceNoAdmin();
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_accesses WHERE (id='{1}') AND (permission='admin')",array($_SESSION['accessId'])));
$isAdmin=mysql_num_rows($r)>0;
//if (!$isAdmin) die('nem admin');

$g=-(int)$_GET['daybefore'];
$plotWidth=(int)$_GET['width'];
if ($plotWidth<1000) $plotWidth=1000;
$byIp=isset($_GET['byip']);

$restrictUser='';
if (!$isAdmin)
	$restrictUser=sqlPrintf("AND (u.id='{1}')",array($_SESSION['userId']));

/*$q=
sqlPrintf(
"
	SELECT r.*,u.userName,TIMESTAMPDIFF(SECOND,DATE(requestTime),requestTime) AS secondsDay
	FROM `wtfb2_requestlog` r
	JOIN wtfb2_users u ON (r.userId=u.id)
	WHERE (TIMESTAMPDIFF(DAY,DATE(requestTime),DATE(NOW()))='{1}') $restrictUser
",array($g)
);*/

$r=doMysqlQuery(sqlPrintf("SELECT ADDDATE(CURDATE(),INTERVAL '{1}' DAY) AS imageDate",array($g)));
$curDate=mysql_fetch_assoc($r);
$curDate=$curDate['imageDate'];

$q=
sqlPrintf(
"
	SELECT r.*,u.userName,TIMESTAMPDIFF(SECOND,DATE(requestTime),requestTime) AS secondsDay
	FROM `wtfb2_requestlog` r
	JOIN wtfb2_users u ON (r.userId=u.id)
	WHERE (requestTime BETWEEN ADDDATE(CURDATE(),INTERVAL '{1}' DAY) AND ADDDATE(CURDATE(),INTERVAL ('{1}'+1) DAY) ) $restrictUser
",array($g)
);

$r=doMySqlQuery($q);
$selectedData=array();
$first=true;
$time;
while($row=mysql_fetch_assoc($r))
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
imagettftext($img,8,0,0,12,0xFFFFFF,'Verdana',"Activity plot beginning from: ".$time);
// put hour lines
for($i=0;$i<=24;$i++)
{
	if ($i%6)
		$color=0x800000;
	else
		$color=0xFF0000;
	imageline($img,$nameBarWidth+$i*($plotWidth/24),$titleHeight,$nameBarWidth+$i*($plotWidth/24),$imageHeight,$color);	
	imagettftext($img,8,0,$nameBarWidth+$i*($plotWidth/24),$titleHeight,0x303030,'Verdana',$i);	
}
// plot data
$height=$titleHeight+$lineHeight;
imagesetstyle($img,$dashedStyle);
ksort($selectedData);
foreach($selectedData as $key=>$value)
{
	// write name@IP
	imagettftext($img,8,0,0,$height,0xFFFFFF,'Verdana',$key);	
	for($x=$writeNameStep;$x<$plotWidth;$x+=$writeNameStep)
	{
		imagettftext($img,8,0,$x,$height,0x303030,'Verdana',$key);	
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
$bbox=imagettfbbox(10,0,'Verdana',$text);
$w=$bbox[2]-$bbox[0];
imagettftext($img,10,0,$x,$height+10,0xFFFFFF,'Verdana',$text);
$x+=$w+5;

imagefilledrectangle($img,$x,$height,$x+20,$height+20,0x0000FF);
$x+=25;
$text=$language['sitteractivity'];
$bbox=imagettfbbox(10,0,'Verdana',$text);
$w=$bbox[2]-$bbox[0];
imagettftext($img,10,0,$x,$height+10,0xFFFFFF,'Verdana',$text);
$x+=$w+5;

imagefilledrectangle($img,$x,$height,$x+20,$height+20,0xFFFF00);
$x+=25;
$text=$language['adminactivity'];
$bbox=imagettfbbox(10,0,'Verdana',$text);
$w=$bbox[2]-$bbox[0];
imagettftext($img,10,0,$x,$height+10,0xFFFFFF,'Verdana',$text);
$x+=$w+5;

header('Content-type: image/png');
header('Content-disposition: inline; filename='.$curDate.'.png');
imagepng($img);


?>
