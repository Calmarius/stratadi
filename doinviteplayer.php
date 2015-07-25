<?php

require_once('userworkerphps.php');

bounceSessionOver();

$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$_SESSION['userId']);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$mySelf=$r[0][0];
$r=runEscapedQuery("SELECT * FROM wtfb2_guilds WHERE (id={0})",$mySelf['guildId']);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$myGuild=$r[0][0];
$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (userName={0})",$_POST['playertoinvite']);
if (isEmptyResult($r)) jumpErrorPage($language['usernamenotexist']);
$user=$r[0][0];

runEscapedQuery("INSERT INTO wtfb2_guildinvitations (recipientId,guildId) VALUES ({0},{1})",$user['id'],$myGuild['id']);
$repTitle=$language['youareinvitedtoguild'];
$repContent=xprintf($language['guildinvitationreport'],array($mySelf['id'],$mySelf['userName'],$myGuild['id'],$myGuild['guildName']));
runEscapedQuery("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ({0},{1},{2},NOW(),MD5(RAND()))",$user['id'],$repTitle,$repContent);

$_SESSION['successtitle']=$language['invitationsent'];
$_SESSION['successcontent']='';

jumpTo('success.php');

?>
