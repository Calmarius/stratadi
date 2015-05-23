<?php

require_once('userworkerphps.php');

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($_SESSION['userId'])));
$me=mysql_fetch_assoc($r);

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_deputies WHERE (sponsorId='{1}')",array($_SESSION['userId'])));
while($row=mysql_fetch_assoc($r))
{
	doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ('{1}','{2}','{3}',NOW(),MD5(RAND()))",array($row['deputyId'],$language['delegationfinished'],xprintf($language['playerfinishedyourdelegation'],array($me['userName'])))));
}

$r=doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_deputies WHERE (sponsorId='{1}')",array($_SESSION['userId'])));


jumpTo('game.php');

?>
