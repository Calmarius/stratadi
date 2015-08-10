<?php

ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log');

require_once("presenterphps.php");
require_once("setupmysqli.php");
require_once("utils/gameutils.php");

// do some logging.

$mode='normal';
if (isset($_SESSION['asdeputy'])) $mode='deputy';
else if (isset($_SESSION['returnUserId'])) $mode='admin';

/*if (!isset($_SESSION['returnUserId']) || (isset($_SESSION['asdeputy'])))
{*/
    runEscapedQuery("
        INSERT INTO wtfb2_hitlog
        (accessDate,page,clientIP,accessCount)
        VALUES
        (CURDATE(),{0},{1},1)
        ON DUPLICATE KEY UPDATE accessCount = accessCount+1",
        $_SERVER['SCRIPT_NAME'],
        $_SERVER['REMOTE_ADDR']
    );

	if (isset($_SESSION['userId'])) // log the request itself
	{
		ob_start();
			print_r($_POST);
			$s=ob_get_contents();
		ob_end_clean();
		runEscapedQuery("INSERT INTO wtfb2_requestlog (requestTime,clientIP,userId,requestedPage,requestType,queryGet,queryPost) VALUES (NOW(),{0},{1},{2},{3},{4},{5})",
			$_SERVER['REMOTE_ADDR'],$_SESSION['userId'],$_SERVER['SCRIPT_NAME'],$mode,$_SERVER['QUERY_STRING'],$s);
	}
/*}*/

if (isset($_POST)) neutralizeGpc($_POST);
if (isset($_GET)) neutralizeGpc($_GET);
if (isset($_COOKIE)) neutralizeGpc($_COOKIE);


?>
