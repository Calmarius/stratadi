<?php

require_once('presenterphps.php');
require_once('utils/gameutils.php');

/*if (isset($_SESSION['returnUserId']))
{
	$_SESSION['userId']=$_SESSION['returnUserId'];
	unset($_SESSION['returnUserId']);
	unset($_SESSION['asdeputy']);
	jumpTo("game.php");
}*/

//closeSession();

if (isset($_SESSION['previousSession']))
{
	$_SESSION=$_SESSION['previousSession'];
	jumpTo("game.php");
}

closeSession();


jumpTo("main.php");

?>
