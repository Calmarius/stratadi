<?php

require_once("userworkerphps.php");

$myId=@$_SESSION['userId'];
$reportId=(int)$_GET['id'];
$token=$_GET['token'];

$r=runEscapedQuery("SELECT * FROM wtfb2_reports WHERE (id={0}) AND (token={1}) AND ((recipientId='$myId') OR (isPublic=1))",$reportId,$token);
if (isEmptyResult($r)) jumpErrorPage($language['accessdenied']);
$report=$r[0][0];

runEscapedQuery("UPDATE wtfb2_reports SET isRead=1 WHERE (id={0})",$reportId);

$showOptions=$report['recipientId']==$myId;

// find next and previous id.

$r=runEscapedQuery("SELECT MIN(id) AS id FROM wtfb2_reports WHERE (id>{0}) AND ((recipientId='$myId'))",$reportId);
if (!isEmptyResult($r))
{
	$a=$r[0][0];
	$report['nextId']=$a['id'];
	$r=runEscapedQuery("SELECT token FROM wtfb2_reports WHERE (id={0})",$a['id']);
	if (!isEmptyResult($r))
	{
	    $a=$r[0][0];
	    $report['nextToken']=$a['token'];
	}
}

if (isset($_GET['op']) && ($_GET['op']=='delete'))
{
	runEscapedQuery("DELETE FROM wtfb2_reports WHERE (id={0})",$reportId);
	if (isset($report['nextId']))
		jumpTo('viewreport.php?id='.$report['nextId'].'&token='.$report['nextToken']);
	else
		jumpTo('reports.php');
}

$r=runEscapedQuery("SELECT MAX(id) AS id FROM wtfb2_reports WHERE (id<{0}) AND (recipientId='$myId')",$reportId);
if (!isEmptyResult($r))
{
	$a=$r[0][0];
	$report['prevId']=$a['id'];
	$r=runEscapedQuery("SELECT token FROM wtfb2_reports WHERE (id={0})",$a['id']);
	if (!isEmptyResult($r))
	{
	    $a=$r[0][0];
	    $report['prevToken']=$a['token'];
	}
}

$link='http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?'."id=".$report['id']."&token=".$report['token'];
showInBox('templates/onereportview.php',array('report'=>$report,'showoptions'=>$showOptions,'reportLink'=>$link),$report['title'],'',$link);


?>
