<?php

require_once('userworkerphps.php');
require_once('villageupdater.php');

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_deputies WHERE (sponsorId='{1}') AND (deputyId='{2}')",array($_GET['id'],$_SESSION['userId'])));
if (mysql_num_rows($r)<1) jumpErrorPage($language['accessdenied']);

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
