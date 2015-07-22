<?php

require_once('userworkerphps.php');
bounceSessionOver();

$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$_SESSION['userId']);
$me=$r[0][0];

$depName=$_POST['deputyname'];
$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (userName={0})",$depName);
if (isEmptyResult($r)) jumpErrorPage($language['usernamenotexist']);

$a=$r[0][0];
runEscapedQuery("INSERT INTO wtfb2_deputies (sponsorId,deputyId) VALUES ({0},{1})",$_SESSION['userId'],$a['id']);

runEscapedQuery("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ({0},{1},{2},NOW(),MD5(RAND()))",$a['id'],$language['delegationrequest'],xprintf($language['playerrequestyoutodelegatehim'],array($me['userName'])));

jumpTo('doreset.php');


?>
