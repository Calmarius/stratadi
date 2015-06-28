<?php

require_once('userworkerphps.php');

$r=runEscapedQuery('SELECT * FROM wtfb2_worldupdate WHERE (lastStatGenerated<DATE_ADD(NOW(),INTERVAL -1 HOUR))');
if (isEmptyResult($r))
{
	header('Content-Type: image/png');
	readfile('statimage.png');
	die();
}

define('LINEHEIGHT',12);
define('IMGWIDTH',300);
define('IMGHEIGHT',80);
define('FONTUSED',4);

$img=imagecreatetruecolor(IMGWIDTH,IMGHEIGHT);
imagefilledrectangle($img,0,0,IMGWIDTH,IMGHEIGHT,0xFFFFFF);

$vPos=0;

$r=runEscapedQuery('SELECT COUNT(*) AS cnt FROM wtfb2_accesses');
$a=$r[0][0];
imagestring($img,FONTUSED,0,$vPos,'Users: '.$a['cnt'],0x000000);
$vPos+=LINEHEIGHT;

$r=runEscapedQuery('SELECT COUNT(*) AS cnt FROM wtfb2_users');
$a=$r[0][0];
imagestring($img,FONTUSED,0,$vPos,'Kingdoms: '.$a['cnt'],0x000000);
$vPos+=LINEHEIGHT;

$r=runEscapedQuery('SELECT COUNT(*) AS cnt FROM `wtfb2_iplog` WHERE lastUsed>DATE_ADD(NOW(),INTERVAL -1 DAY)');
$a=$r[0][0];
imagestring($img,FONTUSED,0,$vPos,'Active: '.$a['cnt'],0x000000);
$vPos+=LINEHEIGHT;

$r=runEscapedQuery('SELECT COUNT(*) AS cnt FROM `wtfb2_iplog` WHERE lastUsed>DATE_ADD(NOW(),INTERVAL -1 HOUR)');
$a=$r[0][0];
imagestring($img,FONTUSED,0,$vPos,'Online: '.$a['cnt'],0x000000);
$vPos+=LINEHEIGHT;

$r=runEscapedQuery('SELECT COUNT(*) AS cnt FROM `wtfb2_villages`');
$a=$r[0][0];
imagestring($img,FONTUSED,0,$vPos,'Villages: '.$a['cnt'],0x000000);
$vPos+=LINEHEIGHT;

imagestring($img,FONTUSED,0,$vPos,'Game start: '.$config['gameStarted'],0x000000);
$vPos+=LINEHEIGHT;



header('Content-Type: image/png');
imagepng($img);
imagepng($img,'statimage.png');

runEscapedQuery('UPDATE wtfb2_worldupdate SET lastStatGenerated=NOW()');

?>
