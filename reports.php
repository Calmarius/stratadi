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

$r=runEscapedQuery("SELECT COUNT(*) AS cnt FROM wtfb2_reports WHERE (recipientid={0}) $hideHidden",$myId);
$cnt=$r[0][0];
$cnt=ceil($cnt['cnt']/20);
$r=runEscapedQuery("SELECT * FROM wtfb2_reports WHERE (recipientid={0}) $hideHidden ORDER BY reportTime DESC LIMIT {1},{2}",$myId,(int)($_GET['p'])*$config['pageSize'],$config['pageSize']);
$reports=array();
foreach ($r[0] as $row)
{
	$reports[]=$row;
}

showInBox('templates/reportview.php',array('reports'=>$reports,'showhidden'=>$showHidden,'count'=>$cnt));

?>
