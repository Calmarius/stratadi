<?php

require_once('presenterphps.php');
require_once('utils/gameutils.php');


$param=array();
$param['time']=secondsToStart();

$contentTemplate=new Template('templates/countdowntemplate.php',$param);
$content=$contentTemplate->getContents();
$page=new Template('templates/basiclayout.php',array('title'=>'WTFBattles II','content'=>$content,'scripts'=>array('timer.js'),'loadScript'=>'initializeTimers()'));
$page->render();


?>
