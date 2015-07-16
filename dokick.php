<?php

require_once('userworkerphps.php');

bounceSessionOver();
$myId=$_SESSION['userId'];
$r=runEscapedQuery("SELECT * FROM wtfb2_guildpermissions WHERE (userId={0}) AND (permission='kick')",$myId);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$myId);
$me=$r[0][0];
$r=runEscapedQuery("SELECT * FROM wtfb2_guilds WHERE (id={0})",$me['guildId']);
$guild=$r[0][0];

$kickeeId=(int)$_GET['id'];
$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$kickeeId);
$kickee=$r[0][0];
runEscapedQuery("INSERT INTO wtfb2_worldevents (eventTime,playerId,type) VALUES (NOW(),{0},'guildchange')",$kickeeId);	 // guild change
runEscapedQuery("
    INSERT INTO wtfb2_worldevents
    (eventTime,guildId,type,recipientId,needFullRefresh)
    VALUES
    (NOW(),{0},'diplomacychange',{1},1)",
    $me['guildId'],
    $kickeeId
); // notify the player too.
// kick from the guild
runEscapedQuery("UPDATE wtfb2_users SET guildId=NULL WHERE (id={0})",$kickeeId);
// revoke his rights
runEscapedQuery("DELETE FROM wtfb2_guildpermissions WHERE (userId={0})",$kickeeId);
// send him a farewell report
$repTitle=$language['youhavebeenkicked'];
$repContent=xprintf($language['someonekickedyoufromthesomeguild'],array($myId,$me['userName'],$guild['id'],$guild['guildName']));
runEscapedQuery("INSERT INTO wtfb2_reports (recipientId,title,text,reportTime,token) VALUES ({0},{1},{2},NOW(),MD5(RAND()))",$kickeeId,$repTitle,$repContent);
// well done
jumpSuccessPage(xprintf($language['playerhasbeenkicked'],array($kickeeId,$kickee['userName'])),'');

?>
