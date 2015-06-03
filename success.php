<?php

require_once('presenterphps.php');
require_once('utils/gameutils.php');

$content=new Template('templates/successtemplate.php',array('title'=>@$_SESSION['successtitle'],'content'=>@$_SESSION['successcontent']));
$page=new Template('templates/basiclayout.php',array('title'=>$language['wtfbattles'],'content'=>$content->getContents()));

$page->render();


?>
