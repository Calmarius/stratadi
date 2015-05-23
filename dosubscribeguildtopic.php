<?php
require_once('userworkerphps.php');

bounceSessionOver();
$myId=$_SESSION['userId'];
$threadId=$_GET['id'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($myId)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$myself=mysql_fetch_assoc($r);

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_threads WHERE (id='{1}') AND (guildId='{2}')",array($threadId,$myself['guildId'])),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$thread=mysql_fetch_assoc($r);

doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_threadlinks (threadId,userId,`read`) VALUES ('{1}','{2}',0)",array($threadId,$myId)),'jumpErrorPage');

jumpTo("messages.php");


?>
