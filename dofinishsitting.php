<?php

require_once('userworkerphps.php');

$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$_SESSION['userId']);
$me=$r[0][0];

$r=runEscapedQuery("SELECT * FROM wtfb2_deputies WHERE (sponsorId={0})",$_SESSION['userId']);
foreach ($r[0] as $row)
{
	runEscapedQuery("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ({0},{1},{2},NOW(),MD5(RAND()))",$row['deputyId'],$language['delegationfinished'],xprintf($language['playerfinishedyourdelegation'],array($me['userName'])));
}

$r=runEscapedQuery("DELETE FROM wtfb2_deputies WHERE (sponsorId={0})",$_SESSION['userId']);


jumpTo('game.php');

?>
