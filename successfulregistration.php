<?php

require_once('presenterphps.php');
require_once('utils/gameutils.php');

$content=new Template('templates/successfulreg.php',array('mail'=>$_SESSION['regEmail'],'name'=>$_SESSION['regName']));
$page=new Template('templates/basiclayout.php',array('title'=>$language['wtfbattles'],'content'=>$content->getContents()));

$page->render();


?>
