<?php

require_once('userworkerphps.php');
bounceSessionOver();

$myId=$_SESSION['userId'];
$myPass=$_POST['password'];

$r=runEscapedQuery("SELECT * FROM wtfb2_accesses WHERE (accountId={0}) AND (passwordHash=MD5({1}))",$myId,$myPass);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);

runEscapedQuery("UPDATE wtfb2_users SET guildId=NULL WHERE (id={0})",$myId);
runEscapedQuery("INSERT INTO wtfb2_worldevents (eventTime,playerId,type) VALUES (NOW(),{0},'guildchange')",$myId);

jumpSuccessPage($language['youlefttheguild']);


?>
