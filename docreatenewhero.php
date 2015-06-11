<?php

require_once('userworkerphps.php');

$myId=$_SESSION['userId'];
$r=runEscapedQuery("SELECT * FROM wtfb2_heroes WHERE (ownerId={0})",$myId);
if (!isEmptyResult($r)) jumpErrorPage($language['youalreadyhaveahero']);

$whereToCreate=(int)$_GET['at'];
$r=runEscapedQuery("SELECT * FROM wtfb2_villages WHERE (id={0}) AND (ownerId={1})",$whereToCreate,$myId);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$village=$r[0][0];

// give him hero
runEscapedQuery("INSERT INTO wtfb2_heroes (ownerId,name,inVillage) VALUES ({0},{1},{2})",$myId,$language['newhero'],$whereToCreate);
// set him a world event
runEscapedQuery("INSERT INTO wtfb2_worldevents (x,y,type,recipientId,eventTime) VALUES ({0},{1},'eventhappened',{2},NOW())",$village['x'],$village['y'],$myId);

$_SESSION['successtitle']=$language['herocreated'];
$_SESSION['successcontent']=$language['heroinfo'];

jumpTo('success.php');

?>
