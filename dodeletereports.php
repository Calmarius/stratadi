<?php

require_once('userworkerphps.php');

bounceSessionOver();

if (!is_array($_POST['reports'])) jumpErrorPage($language['accessdenied']);
foreach($_POST['reports'] as $key => $value)
{
	runEscapedQuery("DELETE FROM wtfb2_reports WHERE (id={0}) AND (recipientId={1})",$value,$_SESSION['userId']);
}

jumpTo("reports.php");

?>
