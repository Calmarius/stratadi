<?php

require_once('presenterphps.php');
require_once('utils/gameutils.php');

if (secondsToStart()>0)
{
	jumpTo('countdown.php');
}

if (!isset($_SESSION['userId'])) jumpTo('login.php');
if ($_SESSION['permission']!='inactive') jumpTo('main.php');

if (!isset($_SESSION['activationparms'])) $_SESSION['activationparms']=array();
$contentTemplate=new Template('templates/activationform.php',$_SESSION['activationparms']);
$content=$contentTemplate->getContents();
$page=new Template('templates/basiclayout.php',array('title'=>'WTFBattles II','content'=>$content));
$page->render();

?>
