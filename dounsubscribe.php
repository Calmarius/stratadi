<?php

require_once('userworkerphps.php');

if (!isset($_SESSION['userId'])) jumpErrorPage($language['sessionisover']);

/*print_r($_POST);
die();*/


foreach($_POST['thread'] as $key=>$value)
{
	echo $value;
	$q=sqlPrintf("DELETE FROM wtfb2_threadlinks WHERE (id='{1}') AND (userId='{2}')",array($value,$_SESSION['userId']));
	$r=doMySqlQuery($q,'jumpErrorPage');	
}

jumpTo('messages.php');

?>
