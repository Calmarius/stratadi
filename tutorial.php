<?php

require_once("presenterphps.php");
include_once($language['tutorialfile']); //TODO: possible security risk, from the language admins.

if (isset($_GET['step']))
{
	$_SESSION['tutorialStep']=$_GET['step'];
}
if (!isset($_SESSION['tutorialStep']))
{
	$_SESSION['tutorialStep']=1;
}
if (isset($_GET['turnoff']))
{
	require_once("setupmysql.php");
	require_once("utils/gameutils.php");
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET needsTutorial=0 WHERE (id='{1}')",array($_SESSION['userId'])));
	jumpSuccessPage($language['youfinishedthetutorial'],$language['youcanreenterthetutorial']);
}

$content=new Template('templates/tutorialtemplate.php',array('content'=>$tutorialText[$_SESSION['tutorialStep']]));
$box=new Template('templates/standardboxtemplate.php',array('content'=>$content->getContents()));
$page=new Template('templates/basiclayout.php',array('title'=>'WTFBattles II','content'=>$box->getContents()));
$page->render();

?>
