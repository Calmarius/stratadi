<?php

require_once("presenterphps.php");

if (!isset($_SESSION['loginparms'])) $_SESSION['loginparms']=array();
if (isset($_GET['referer']))
{
	$_SESSION['loginparms']['referer']=$_GET['referer'];
}
else
{
	unset($_SESSION['loginparms']['referer']);
}
//print_r($_SESSION);
$loginForm=new Template('templates/loginform.php',$_SESSION['loginparms']);
$content=$loginForm->getContents();
$page=new Template('templates/basiclayout.php',array('title'=>'WTFBattles II','content'=>$content));
$page->render();


?>
