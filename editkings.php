<?php

require_once('userworkerphps.php');
bounceSessionOver();

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($_SESSION['userId'])));
$kingdom=mysql_fetch_assoc($r);

$allowManage=$kingdom['masterAccess']==$_SESSION['accessId'];

$r=doMySqlQuery(sqlPrintf("SELECT *,(id='{2}') AS isMaster,(id='{3}') AS isMe FROM wtfb2_accesses WHERE (accountId='{1}')",array($_SESSION['userId'],$kingdom['masterAccess'],$_SESSION['accessId'])));
$accesses=array();
while($row=mysql_fetch_assoc($r))
{
	$accesses[]=$row;
}

showInBox('templates/editkings.php',array('accesses'=>$accesses,'canmanage'=>$allowManage));

?>
