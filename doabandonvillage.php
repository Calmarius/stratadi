<?php

require_once("userworkerphps.php");
require_once("villageupdater.php");

$r=doMySqlQuery(sqlPrintf("SELECT id FROM wtfb2_accesses WHERE (accountId='{1}') AND (passwordHash=MD5('{2}'))",array($_SESSION['userId'],$_POST['password'])));
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_villages WHERE (id='{1}') AND (ownerId='{2}')",array($_POST['id'],$_SESSION['userId'])));
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$village=mysql_fetch_assoc($r);

doMySqlQuery(sqlPrintf("UPDATE wtfb2_villages SET ownerId=0 WHERE (id='{1}')",array($_POST['id'])));
doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=expansionPoints+1 WHERE (id='{1}')",array($_SESSION['userId'])));

doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (x,y,eventTime,type) VALUES ('{1}','{2}',NOW(),'abandon')",array($village['x'],$village['y'])));

updateAllVillages($_SESSION['userId']);
jumpSuccessPage($language['villageisabandoned']);


?>
