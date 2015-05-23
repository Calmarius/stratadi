<?php

require_once('userworkerphps.php');

bounceSessionOver();

$myId=$_SESSION['userId'];
$reportId=$_POST['id'];

$hidden=(int)isset($_POST['hidden']);
$public=(int)isset($_POST['public']);

doMySqlQuery(sqlPrintf("UPDATE wtfb2_reports SET isHidden='{1}', isPublic='{2}' WHERE (id='{3}') AND (recipientId='$myId')",array($hidden,$public,$reportId)),'jumpErrorPage');

jumpTo(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']:'reports.php');

?>
