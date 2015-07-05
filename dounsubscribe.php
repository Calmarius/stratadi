<?php

require_once('userworkerphps.php');

if (!isset($_SESSION['userId'])) jumpErrorPage($language['sessionisover']); // TODO: (task) Can it be replaced with bounceSessionOver?

foreach($_POST['thread'] as $key=>$value)
{
	echo $value;
	$q=sqlvprintf("DELETE FROM wtfb2_threadlinks WHERE (id={0}) AND (userId={1})",array($value,$_SESSION['userId']));
	$r=runEscapedQuery($q,'jumpErrorPage');
}

jumpTo('messages.php');

?>
