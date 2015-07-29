<?php

require_once('userworkerphps.php');
require_once('villageupdater.php');

$r=runEscapedQuery("SELECT * FROM wtfb2_deputies WHERE (sponsorId={0}) AND (deputyId={1})",$_GET['id'],$_SESSION['userId']);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);

$tmp=array();
foreach($_SESSION as $key=>$value)
{
	$tmp[$key]=$value;
}

$_SESSION['previousSession']=$tmp;
$_SESSION['returnUserId']=$_SESSION['userId'];
$_SESSION['userId']=(int)$_GET['id'];
$_SESSION['asdeputy']=true;

loginUpdateAll($_SESSION['userId']);

jumpTo('game.php');


?>
