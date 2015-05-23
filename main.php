<?php

header('content-type:text/html; charset=utf-8');

require_once('templateclass.php');
require_once('setupsession.php');
require_once('utils/gameutils.php');

$content='';
if (isset($_SESSION['userId']))
{
/*	$topBar=new Template('templates/topbar.php',array());
	$content=$topBar->getContents();*/
	jumpTo("game.php");
}
else
{
/*	if (!isset($_SESSION['loginparms'])) $_SESSION['loginparms']=array();
	$loginForm=new Template('templates/loginform.php',$_SESSION['loginparms']);
	$content=$loginForm->getContents();*/
	jumpTo("login.php");
}

?>


