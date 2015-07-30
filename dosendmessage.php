<?php

require_once('userworkerphps.php');
htmlize($_POST);

function sendThreadLink($threadId,$userId,$isRead)
{
	$q=sqlPrintf("SELECT * FROM wtfb2_threadlinks WHERE (threadId='{1}') AND (userId='{2}')",array($threadId,$userId));
	$r=runEscapedQuery($q);
	if (isEmptyResult($r))
	{
		$q=sqlPrintf("INSERT INTO wtfb2_threadlinks (threadId,userId,`read`) VALUES ({1},{2},{3})",array($threadId,$userId,$isRead));
		$r=runEscapedQuery($q);
	}
	else
	{
		$q=sqlPrintf("UPDATE wtfb2_threadlinks SET `read`='{1}' WHERE (threadId='{2}') AND (userId='{3}')",array($isRead,$threadId,$userId));
		$r=runEscapedQuery($q);
	}
}

function updateThreadLinks($threadId)
{
	$q=sqlPrintf("UPDATE wtfb2_threadlinks SET `read`=0 WHERE (threadId='{1}')",array($threadId));
	$r=runEscapedQuery($q);
}

if (!isset($_SESSION['userId'])) jumpErrorPage($language['sessionisover']);
$userId=$_SESSION['userId'];
$r=runEscapedQuery("SELECT NOW() AS now");
$a=$r[0][0];
$now=$a['now'];

if (isset($_POST['extra']))
{
	$extra=$_POST['extra'];
	if ($extra=='circular')
	{
		$r=runEscapedQuery("SELECT * FROM wtfb2_guildpermissions WHERE (userId={0}) AND (permission='circular')", $_SESSION['userId']);
		if (isEmptyResult($r)) jumpErrorPage('accessdenied');
	}
	if ($extra=='guildthread')
	{
		$r=runEscapedQuery("SELECT * FROM wtfb2_guildpermissions WHERE (userId={0}) AND (permission='moderate')", $_SESSION['userId']);
		if (isEmptyResult($r)) jumpErrorPage('accessdenied');
	}
}

$_SESSION['lastcomposition']['subject']=$_POST['subject'];
$_SESSION['lastcomposition']['content']=$_POST['content'];


$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})", $_SESSION['userId']);
$me=$r[0][0];

$recipientId=0;
if ($_POST['recipient']!='')
{
	$q=sqlPrintf("SELECT * FROM wtfb2_users WHERE (userName='{1}')",array($_POST['recipient']));
	$r=runEscapedQuery($q);
	if (isEmptyResult($r)) jumpErrorPage($language['recipientisnotexist']);
	$a=$r[0][0];
	$recipientId=$a['id'];
}

$threadId=0;
if ($_POST['thread']=='')
{
	$extraFields='';
	$extraValues='';
	if (isset($_POST['extra']))
	{
		$extra=$_POST['extra'];
		if ($extra=='guildthread')
		{
			$extraFields=',guildId';
			$extraValues=sqlPrintf(",'{1}'",array($me['guildId']));
		}
	}
	$q=sqlPrintf("INSERT INTO wtfb2_threads (updated,lastPosterId,subject $extraFields) VALUES ('$now','{1}','{2}' $extraValues)",array($_SESSION['userId'],$_POST['subject']));
	$r=runEscapedQuery($q);
	$threadId=getLastInsertId();
}
else
{
	$threadId=(int)$_POST['thread'];
	$q=sqlPrintf("SELECT * FROM wtfb2_threadlinks WHERE (threadId='{1}') AND (userId='{2}')",array($threadId,$_SESSION['userId']));
	$r=runEscapedQuery($q);
	if (isEmptyResult($r)) jumpErrorPage($language['threadnotexist']);
	$q=sqlPrintf("SELECT *,UNIX_TIMESTAMP(updated) AS tsUpdated FROM wtfb2_threads WHERE (id='{1}')",array($threadId));
	$r=runEscapedQuery($q);
	if (isEmptyResult($r)) jumpErrorPage($language['threadnotexist']);
	$a=$r[0][0];
	if ((int)$a['tsUpdated']>(int)$_POST['nowstamp'])
	{
		jumpTo('compose.php?thread='.urlencode($threadId).'&notification='.urlencode($language['newreplywhileyouwrote']));
	}

	$q=sqlPrintf("UPDATE wtfb2_threads SET updated='$now',subject='{1}',lastPosterId='{3}' WHERE (id='{2}')",array($_POST['subject'],$threadId,$_SESSION['userId']));
	$r=runEscapedQuery($q);
}

$q=sqlPrintf("INSERT INTO wtfb2_threadentries (threadId,posterId,text,`when`) VALUES ('{1}','{2}','{3}','$now')",array($threadId,$_SESSION['userId'],$_POST['content']));
$r=runEscapedQuery($q);

updateThreadLinks($threadId);
sendThreadLink($threadId,$_SESSION['userId'],"1");
if (isset($_POST['extra']))
{
	$extra=$_POST['extra'];
	if ($extra=='circular')
	{
		$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (guildId={0})", $me['guildId']);
		foreach ($r[0] as $row)
		{
			sendThreadLink($threadId,$row['id'],"0");
		}
	}
}
if ($recipientId!=0)
{
	sendThreadLink($threadId,$recipientId,"0");
}

unset($_SESSION['lastcomposition']);
$_SESSION['successtitle']=$language['lettersent'];
$_SESSION['successcontent']='';
jumpTo('success.php');

?>
