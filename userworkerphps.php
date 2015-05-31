<?php

require_once("presenterphps.php");
require_once("setupmysql.php");
require_once("setupmysqli.php");
require_once("utils/gameutils.php");

// do some logging.

$mode='normal';
if (isset($_SESSION['asdeputy'])) $mode='deputy';
else if (isset($_SESSION['returnUserId'])) $mode='admin';

/*if (!isset($_SESSION['returnUserId']) || (isset($_SESSION['asdeputy'])))
{*/
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_hitlog SET accessCount=accessCount+1 WHERE (accessDate=CURDATE()) AND (page='{1}') AND (clientIP='{2}')",array($_SERVER['SCRIPT_NAME'],$_SERVER['REMOTE_ADDR'])));
	if (mysql_affected_rows()==0)
	{
		doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_hitlog (accessDate,page,clientIP,accessCount) VALUES (CURDATE(),'{1}','{2}',1)",array($_SERVER['SCRIPT_NAME'],$_SERVER['REMOTE_ADDR'])));
	}

	if (isset($_SESSION['userId'])) // log the request itself
	{
		ob_start();
			print_r($_POST);
			$s=ob_get_contents();
		ob_end_clean();
		doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_requestlog (requestTime,clientIP,userId,requestedPage,requestType,queryGet,queryPost) VALUES (NOW(),'{1}','{2}','{3}','{4}','{5}','{6}')",
			array($_SERVER['REMOTE_ADDR'],$_SESSION['userId'],$_SERVER['SCRIPT_NAME'],$mode,$_SERVER['QUERY_STRING'],$s)));
	}
/*}*/

?>
