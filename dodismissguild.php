<?php

require_once('userworkerphps.php');
bounceSessionOver();

$myId=$_SESSION['userId'];
$myPass=$_POST['password'];

$r=runEscapedQuery("SELECT * FROM wtfb2_guildpermissions WHERE (userId={0}) AND (permission='dismiss')",$myId);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);

$r=runEscapedQuery("SELECT * FROM wtfb2_accesses WHERE (accountId={0}) AND (passwordHash=MD5({1}))",$myId,$myPass);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$access=$r[0][0];
$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$access['accountId']);
$myself=$r[0][0];

$r=runEscapedQuery("SELECT id FROM wtfb2_users WHERE (guildId={0})",$myself['guildId']);
$values=array();
foreach ($r[0] as $row)
{
	$values[]=sqlvprintf("(NOW(),{0},'guildchange')",array($row['id']));
}
runEscapedQuery("UPDATE wtfb2_users SET guildId=NULL WHERE (guildId={0})",$myself['guildId']);
runEscapedQuery("DELETE FROM wtfb2_guilds WHERE (id={0})",$myself['guildId']);
runEscapedQuery("DELETE FROM wtfb2_guildpermissions WHERE (userId={0})",$myId);
if (count($values)>0)
	runEscapedQuery("INSERT INTO wtfb2_worldevents (eventTime,playerId,type) VALUES ".implode(',',$values)); // is values escaped?

$_SESSION['successtitle']=$language['youdismissedtheguild'];

jumpTo("success.php");

?>
