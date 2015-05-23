<?php

require_once("userworkerphps.php");

if (secondsToStart()>0)
{
	jumpTo('countdown.php');
}

function activateAccount($userId,$activationCode)
{
	global $language;
	$activationCode=trim($activationCode);
	$q=sqlPrintf("SELECT * FROM wtfb2_accesses WHERE (accountId='{1}')",array($userId));
	$r=doMySqlQuery($q,'jumpErrorPage');
	if (mysql_num_rows($r)<1) jumpErrorPage($language['usernamenotexist']);
	$a=mysql_fetch_assoc($r);
	if ($a['activationToken']!=$activationCode)
	{
		$_SESSION['activationparms']=$_POST;
		$_SESSION['activationparms']['activationcodeError']=makeErrorMessage($language['wrongactivationcode']);
		jumpTo('activate.php');
	}
	$q=sqlPrintf("UPDATE wtfb2_accesses SET permission='user' WHERE (id='{1}')",array($a['id']));
	$_SESSION['permission']='user';
	$r=doMySqlQuery($q,'jumpErrorPage');
	$_SESSION['activationparms']=array();
	jumpTo('game.php');
}

if (!isset($_SESSION['userId'])) jumpTo('login.php');

activateAccount($_SESSION['userId'],$_POST['activationcode']);


?>
