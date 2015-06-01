<?php

require_once("userworkerphps.php");
bounceNoAdmin();

$content=new Template('templates/massreporttemplate.php',array());
$box=new Template('templates/standardboxtemplate.php',array('content'=>$content->getContents()));
$page=new Template('templates/basiclayout.php',array('title'=>'WTFBattles II','content'=>$box->getContents()));
$page->render();


?>
