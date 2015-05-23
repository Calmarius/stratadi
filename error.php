<?php

require_once('presenterphps.php');

$content=new Template('templates/errortemplate.php',array('errormsg'=>$_SESSION['errorState']));
$page=new Template('templates/basiclayout.php',array('title'=>$language['wtfbattles'],'content'=>$content->getContents()));

$page->render();

?>
