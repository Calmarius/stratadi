<?php

require_once('presenterphps.php');
require_once('utils/gameutils.php');

$content=new Template('templates/infotemplate.php',array('title'=>$_SESSION['infotitle'],'content'=>$_SESSION['infocontent']));
$page=new Template('templates/basiclayout.php',array('title'=>$language['wtfbattles'],'content'=>$content->getContents()));

$page->render();


?>
