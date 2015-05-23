<?php

require_once("userworkerphps.php");

bounceSessionOver();

$myId=$_SESSION['userId'];

$hideHidden='AND (isHidden=0)';
$showHidden=0;
if (isset($_GET['showhidden']))
{
	$hideHidden='';
	$showHidden=1;
}

if (!isset($_GET['p'])) $_GET['p']=0;

$r=doMySqlQuery(sqlPrintf("SELECT COUNT(*) AS cnt FROM wtfb2_reports WHERE (recipientid={1}) {2}",array($myId,$hideHidden,(int)$_GET['p'])));
$cnt=mysql_fetch_assoc($r);
$cnt=ceil($cnt['cnt']/20);
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_reports WHERE (recipientid={1}) {2} ORDER BY reportTime DESC LIMIT {3},{4}",array($myId,$hideHidden,(int)($_GET['p'])*$config['pageSize'],$config['pageSize'])));
$reports=array();
while($row=mysql_fetch_assoc($r))
{
	$reports[]=$row;
}

showInBox('templates/reportview.php',array('reports'=>$reports,'showhidden'=>$showHidden,'count'=>$cnt));

?>
