<?php

require_once('userworkerphps.php');

$myId=$_SESSION['userId'];
$r=runEscapedQuery("SELECT * FROM wtfb2_heroes WHERE (ownerId={0})",$myId);
if (!isEmptyResult($r)) jumpErrorPage($language['youalreadyhaveahero']);

$whichHero=(int)$_GET['id'];
$r=runEscapedQuery("SELECT * FROM wtfb2_heroes WHERE (ownerId=0) AND (id={0})",$whichHero);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);

runEscapedQuery("UPDATE wtfb2_heroes SET ownerId={0} WHERE (id={1})",$myId,$whichHero);

jumpSuccessPage($language['heroisnowyours'],$language['heroinfo']);

?>
