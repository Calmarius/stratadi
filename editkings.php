<?php

require_once('userworkerphps.php');
bounceSessionOver();

$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$_SESSION['userId']);
$kingdom=$r[0][0];

$allowManage=$kingdom['masterAccess']==$_SESSION['accessId'];

$r=runEscapedQuery("SELECT *,(id={1}) AS isMaster,(id={2}) AS isMe FROM wtfb2_accesses WHERE (accountId={0})",$_SESSION['userId'],$kingdom['masterAccess'],$_SESSION['accessId']);
$accesses=array();
foreach ($r[0] as $row)
{
	$accesses[]=$row;
}

showInBox('templates/editkings.php',array('accesses'=>$accesses,'canmanage'=>$allowManage));

?>
