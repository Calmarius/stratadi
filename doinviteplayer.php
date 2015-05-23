<?php

require_once('userworkerphps.php');

bounceSessionOver();

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($_SESSION['userId'])),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$mySelf=mysql_fetch_assoc($r);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guilds WHERE (id='{1}')",array($mySelf['guildId'])),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$myGuild=mysql_fetch_assoc($r);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (userName='{1}')",array($_POST['playertoinvite'])),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['usernamenotexist']);
$user=mysql_fetch_assoc($r);

doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_guildinvitations (recipientId,guildId) VALUES ('{1}','{2}')",array($user['id'],$myGuild['id'])),'jumpErrorPage');
$repTitle=$language['youareinvitedtoguild'];
$repContent=xprintf($language['guildinvitationreport'],array($mySelf['id'],$mySelf['userName'],$myGuild['id'],$myGuild['guildName']));
doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ('{1}','{2}','{3}',NOW(),MD5(RAND()))",array($user['id'],$repTitle,$repContent)));

$_SESSION['successtitle']=$language['invitationsent'];
$_SESSION['successcontent']='';

jumpTo('success.php');

?>
