<?php

require_once("userworkerphps.php");
require_once("villageupdater.php");

$access=runEscapedQuery("SELECT id FROM wtfb2_accesses WHERE (accountId={0}) AND (passwordHash=MD5({1}))",$_SESSION['accessId'],$_POST['password']);
if (!isset($access[0][0])) jumpErrorPage($language['accessdenied']);

$village=runEscapedQuery("SELECT * FROM wtfb2_villages WHERE (id={0}) AND (ownerId={1})",$_POST['id'],$_SESSION['userId']);
if (!isset($village[0][0])) jumpErrorPage($language['accessdenied']);
$village=$village[0][0];

runEscapedQuery("UPDATE wtfb2_villages SET ownerId=0 WHERE (id={0})",$_POST['id']);
runEscapedQuery("UPDATE wtfb2_users SET expansionPoints=expansionPoints+1 WHERE (id={0})",$_SESSION['userId']);

runEscapedQuery("INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ({0},{1},NOW(),'abandon')",$village['x'],$village['y']);

updateAllVillages($_SESSION['userId']);
jumpSuccessPage($language['villageisabandoned']);


?>
