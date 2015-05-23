<?php

require_once('userworkerphps.php');

bounceSessionOver();

if (!is_array($_POST['reports'])) jumpErrorPage($language['accessdenied']);
foreach($_POST['reports'] as $key => $value)
{
	doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_reports WHERE (id='{1}') AND (recipientId='{2}')",array($value,$_SESSION['userId'])));
}

jumpTo("reports.php");

?>
