<?php

require_once('userworkerphps.php');
require_once("villageupdater.php");
bounceNoAdmin();

$tmp=array();
foreach($_SESSION as $key=>$value)
{
	$tmp[$key]=$value;
}

$_SESSION['previousSession']=$tmp;
$_SESSION['returnUserId']=$_SESSION['userId'];
$_SESSION['userId']=(int)$_GET['id'];

loginUpdateAll($_SESSION['userId']);

jumpTo('game.php');

?>
