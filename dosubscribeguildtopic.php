<?php
require_once('userworkerphps.php');

bounceSessionOver();
$myId=$_SESSION['userId'];
$threadId=$_GET['id'];
$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$myId);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$myself=$r[0][0];

$r=runEscapedQuery("SELECT * FROM wtfb2_threads WHERE (id={0}) AND (guildId={1})",$threadId,$myself['guildId']);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$thread=$r[0][0];

runEscapedQuery("INSERT INTO wtfb2_threadlinks (threadId,userId,`read`) VALUES ({0},{1},0)",$threadId,$myId);

jumpTo("messages.php");


?>
