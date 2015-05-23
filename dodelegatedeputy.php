<?php

require_once('userworkerphps.php');
bounceSessionOver();

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($_SESSION['userId'])));
$me=mysql_fetch_assoc($r);

$depName=$_POST['deputyname'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (userName='{1}')",array($depName)));
if (mysql_num_rows($r)<1) jumpErrorPage($language['usernamenotexist']);

$a=mysql_fetch_assoc($r);
doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_deputies (sponsorId,deputyId) VALUES ('{1}','{2}')",array($_SESSION['userId'],$a['id'])));

doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ('{1}','{2}','{3}',NOW(),MD5(RAND()))",array($a['id'],$language['delegationrequest'],xprintf($language['playerrequestyoutodelegatehim'],array($me['userName'])))));

jumpTo('doreset.php');


?>
