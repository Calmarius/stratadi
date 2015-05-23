<?php

require_once('userworkerphps.php');

$myId=$_SESSION['userId'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_heroes WHERE (ownerId='{1}')",array($myId)));
if (mysql_num_rows($r)>0) jumpErrorPage($language['youalreadyhaveahero']);

$whereToCreate=(int)$_GET['at'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_villages WHERE (id='{1}') AND (ownerId='{2}')",array($whereToCreate,$myId)));
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$village=mysql_fetch_assoc($r);

// give him hero
doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_heroes (ownerId,name,inVillage) VALUES ('{1}','{2}','{3}')",array($myId,$language['newhero'],$whereToCreate)),'jumpErrorPage');
// set him a world event
doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (x,y,type,recipientId,eventTime) VALUES ('{1}','{2}','eventhappened','{3}',NOW())",array($village['x'],$village['y'],$myId)));

$_SESSION['successtitle']=$language['herocreated'];
$_SESSION['successcontent']=$language['heroinfo'];

jumpTo('success.php');

?>
