<?php

require_once("userworkerphps.php");

$myId=@$_SESSION['userId'];
$reportId=(int)$_GET['id'];
$token=$_GET['token'];

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_reports WHERE (id='{1}') AND (token='{2}') AND ((recipientId='$myId') OR (isPublic=1))",array($reportId,$token)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['accessdenied']);
$report=mysql_fetch_assoc($r);

doMySqlQuery(sqlPrintf("UPDATE wtfb2_reports SET isRead=1 WHERE (id='{1}')",array($reportId)),'jumpErrorPage');

$showOptions=$report['recipientId']==$myId;

// find next and previous id.

$r=doMySqlQuery(sqlPrintf("SELECT MIN(id) AS id FROM wtfb2_reports WHERE (id>'{1}') AND ((recipientId='$myId'))",array($reportId)));
if (mysql_num_rows($r)>0)
{
	$a=mysql_fetch_assoc($r);
	$report['nextId']=$a['id'];
	$r=doMySqlQuery(sqlPrintf("SELECT token FROM wtfb2_reports WHERE (id='{1}')",array($a['id'])));
	$a=mysql_fetch_assoc($r);
	$report['nextToken']=$a['token'];
}

if (isset($_GET['op']) && ($_GET['op']=='delete'))
{
	doMySqlQuery(sqlPrintf("DELETE FROM wtfb2_reports WHERE (id='{1}')",array($reportId)));
	if (isset($report['nextId']))
		jumpTo('viewreport.php?id='.$report['nextId'].'&token='.$report['nextToken']);
	else
		jumpTo('reports.php');
}

$r=doMySqlQuery(sqlPrintf("SELECT MAX(id) AS id FROM wtfb2_reports WHERE (id<'{1}') AND (recipientId='$myId')",array($reportId)));
if (mysql_num_rows($r)>0)
{
	$a=mysql_fetch_assoc($r);
	$report['prevId']=$a['id'];
	$r=doMySqlQuery(sqlPrintf("SELECT token FROM wtfb2_reports WHERE (id='{1}')",array($a['id'])));
	$a=mysql_fetch_assoc($r);
	$report['prevToken']=$a['token'];
}

$link='http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?'."id=".$report['id']."&token=".$report['token'];
showInBox('templates/onereportview.php',array('report'=>$report,'showoptions'=>$showOptions,'reportLink'=>$link),$report['title'],'',$link);


?>
