<?php

require_once('userworkerphps.php');
bounceNoAdmin();

$r=doMySqlQuery("SELECT * FROM wtfb2_users");
while($user=mysql_fetch_assoc($r))
{
	doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,reportType,token) VALUES ('{1}','{2}','{3}',NOW(),'{4}',MD5(RAND()))",array($user['id'],$_POST['subject'],$_POST['text'],'adminmessage'))); // megcsinálni jobbra.
}

jumpTo('massreport.php');

?>
