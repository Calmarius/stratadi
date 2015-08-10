<?php

function randomizeTime($time,$maxDiff=0.01) // TODO: (wishlist) to config
{
	return $time+$time*mt_rand(-100,100)*$maxDiff*0.01;
}


function mysqlConnectError($str) // for mySQL connection errors
{
	global $language;
	jumpErrorPage(xprintf($language['databaseerror'],array($str)));
}

function loginUpdateAll($playerId)
{
	updateAllVillages($playerId);
	updatePlayer($playerId);
	runEscapedQuery("UPDATE wtfb2_users SET lastLoaded=NOW() WHERE (id={0})", $playerId);
}

function logText($str)
{
	$f=fopen("log.txt","a+");
	fwrite($f,$str."\n");
	fclose($f);
}

function shitHappen()
{
	$bt=debug_backtrace();
	$callerFrame=$bt[0];
	die("Shit happened in ${callerFrame['file']} at ${callerFrame['line']}");
}

function bounceSessionOver()
{
	global $language;
	if (!isset($_SESSION['userId'])) jumpErrorPage($language['sessionisover']);
}

function bounceNoAdmin()
{
	global $language;
	if (isset($_SESSION['asdeputy'])) jumpErrorPage($language['accessdenied']);
	$r=runEscapedQuery("SELECT * FROM wtfb2_accesses WHERE (accountId={0}) AND (permission='admin')", $_SESSION['userId']);
	if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
}

function jumpTo($url)
{
	header("HTTP/1.0 301");
	header("Location: $url");
	die();
}

function generateRandomId()
{
	$ret='';
	for($i=0;$i<30;$i++)
	{
		$ret.=chr(ord('A')+rand(0,25));
	}
	return $ret;
}

function villageCount($userId)
{
    $r = runEscapedQuery("SELECT COUNT(*) AS vc FROM wtfb2_villages WHERE (ownerId={0})", $userId);
	$a=$r[0][0];
	return (int)$a['vc'];
}

function secondsToStart()
{
	global $config;
	$dt=date_parse($config['gameStarted']);
	$startTime=mktime($dt['hour'],$dt['minute'],$dt['second'],$dt['month'],$dt['day'],$dt['year']);
	$nowTime=time();
	return $startTime-$nowTime;
}

function closeSession()
{
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
	    $params = session_get_cookie_params();
	    setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]
	    );
	}
	session_destroy();
}

function jumpErrorPage($error)
{
	header('HTTP/1.0 301');
	$_SESSION['errorState']=$error;
	header('Location: error.php');
	die();
}

function jumpSuccessPage($title,$message)
{
	header('HTTP/1.0 301');
	$_SESSION['successtitle']=$title;
	$_SESSION['successcontent']=$message;
	header('Location: success.php');
	die();
}

function jumpInformationPage($title,$message)
{
	header('HTTP/1.0 301');
	$_SESSION['infotitle']=$title;
	$_SESSION['infocontent']=$message;
	header('Location: info.php');
	die();
}

function imAccountMaster()
{
	$r=runEscapedQuery("SELECT id FROM wtfb2_users WHERE (id={0}) AND (masterAccess={1})", $_SESSION['userId'],$_SESSION['accessId']);
	return !isEmptyResult($r);
}

function makeErrorMessage($msg)
{
	return '<span class="negative">'.$msg.'</span>';
}

function xprintf($format,$arguments)
{
	$n=count($arguments);
	if (is_string($format))
	{
		$formatArray['format']=$format;
	}
	else if (is_array($format))
	{
		$formatArray=$format;
	}
	else return "Invalid format argument";
	
	
	
	$result=$formatArray['format'];
	for($i=0;$i<$n;$i++)
	{
		$insertStr=$arguments[$i];
		if (isset($formatArray[(string)($i+1)]))
		{
			$insertStr=$formatArray[$i+1]($insertStr);
		}
		$result=str_replace('{'.($i+1).'}',$insertStr,$result);
	}
	return $result;
}

function neutralizeGpc(&$array)
{
	if (!get_magic_quotes_gpc()) return;
	if (!is_array($array)) return;
	foreach($array as $key=>$value)
	{
		if (is_array($value))
		{
			escapeArray($array[$key]);
		}
		else
		{
			$array[$key]=stripslashes($value);
		}
	}
}

?>
