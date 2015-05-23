<?php
require_once('shadow.php');
define("MAXATTEMPTS",7);

// IMPORTANT. This new version DOES NOT ESCAPES!

function exitFn($s)
{
	die($s);
}

function explainQuery($query,$function="exitFn")
{
	return doQuery('EXPLAIN '.$query,$function);
}

function sqlPrintf($query,$args)
{
	$n=count($args);
	for($i=0;$i<$n;$i++)
	{
		$insertStr=mysql_real_escape_string($args[$i]);
		$query=str_replace('{'.($i+1).'}',$insertStr,$query);
	}
	return $query;
}

function doMySqlQuery($query,$function="exitFn")
{
	$r=mysql_query($query);
	if (!$r)
	{
		$bt=debug_backtrace();
		$lineToPass=$bt[0]['file'].':'.$bt[0]['line'].':'.mysql_errno().'-'.mysql_error().':'.$query;
		$f=fopen('failqueries_uccsetalalodkihahahfhjhsadjkfhsadf.txt','at');
		fwrite($f,'FAIL! '.$lineToPass."\n\n");
		if (isset($_SESSION))
		{
			foreach($_SESSION as $key=>$value)
			{
				fwrite($f,'In session: '.$key.'=>'.$value."\n");			
			}
		}
		fwrite($f,date('Y-m-d H:i:s')."\n");
		fwrite($f,"-------------------------------------------------\n");
		fclose($f);
		$function($lineToPass);
	}
	return $r;
}

function htmlError($title,$str)
{
	echo <<< X
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
	<html>
		<head>
			<meta http-equiv="content-type" content="text/html; charset=utf-8">
			<title>$title</title>
		</head>
		<body>
			<p>$str</p>
		</body>
	</html>
X;
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

function escapeArray(&$array)
{
	if (!is_array($array)) return;
	foreach($array as $key=>$value)
	{
		if (is_array($value))
		{
			escapeArray($array[$key]);
		}
		else
		{
			if (get_magic_quotes_gpc()) $value=stripslashes($value);
			$array[$key]=mysql_real_escape_string($value);
		}
	}
}

function htmlize(&$arr)
{
	if (!is_array($arr)) return;
	foreach($arr as $key=>$value)
	{
		if (is_array($value))
		{
			htmlize($arr[$key]);
		}
		else
		{
			$arr[$key]=htmlspecialchars($value);
		}
	}
}

$attempts=0;
$_conn=null;

$i=0;
for($i=0;$i<MAXATTEMPTS;$i++)
{
	$_conn=@mysql_connect('127.0.0.1',__MYSQL_USER_NAME,__MYSQL_PASSWORD);
	if (!$_conn)
	{
		if (mysql_errno()!=1040) // too many connections hibakódja
		{
			if (function_exists('mysqlConnectError'))
			{
				mysqlConnectError(mysql_error());
			}
			else
				die('Nem lehetett kapcsolódni az adatbázishoz! A hiba oka: '.mysql_error());
		}
		else
			sleep(1);
	}
	else
		break;
}
if ($i>=MAXATTEMPTS)
{
	header("HTTP/1.0 500 Database problems");
	die("Serious database problems detected");
}
mysql_set_charset("utf8");
mysql_select_db(__MYSQL_DB_NAME);
if (isset($_POST)) neutralizeGpc($_POST);
if (isset($_GET)) neutralizeGpc($_GET);
if (isset($_COOKIE)) neutralizeGpc($_COOKIE);

?>
