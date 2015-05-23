<?php

require_once('userworkerphps.php');

$myId=$_SESSION['userId'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_heroes WHERE (ownerId='{1}')",array($myId)),'jumpErrorPage');
if (mysql_num_rows($r)>0) jumpErrorPage($language['youalreadyhaveahero']);

$whichHero=(int)$_GET['id'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_heroes WHERE (ownerId=0) AND (id='{1}')",array($whichHero)));
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);

doMySqlQuery(sqlPrintf("UPDATE wtfb2_heroes SET ownerId='{1}' WHERE (id='{2}')",array($myId,$whichHero)));

jumpSuccessPage($language['heroisnowyours'],$language['heroinfo']);

?>
