<?php

require_once('userworkerphps.php');

bounceSessionOver();

$myId=$_SESSION['userId'];
$reportId=$_POST['id'];

$hidden=(int)isset($_POST['hidden']);
$public=(int)isset($_POST['public']);

runEscapedQuery("UPDATE wtfb2_reports SET isHidden={0}, isPublic={1} WHERE (id={2}) AND (recipientId='$myId')",$hidden,$public,$reportId);

jumpTo(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']:'reports.php');

?>
