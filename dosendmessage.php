<?php

require_once('userworkerphps.php');
htmlize($_POST);

function sendThreadLink($threadId,$userId,$isRead)
{
	$q=sqlvprintf("SELECT * FROM wtfb2_threadlinks WHERE (threadId={0}) AND (userId={1})",array($threadId,$userId));
	$r=runEscapedQuery($q);
	if (isEmptyResult($r))
	{
		$q=sqlvprintf("INSERT INTO wtfb2_threadlinks (threadId,userId,`read`) VALUES ({0},{1},{2})",array($threadId,$userId,$isRead));
		$r=runEscapedQuery($q);
	}
	else
	{
		$q=sqlvprintf("UPDATE wtfb2_threadlinks SET `read`={0} WHERE (threadId={1}) AND (userId={2})",array($isRead,$threadId,$userId));
		$r=runEscapedQuery($q);
	}
}

function updateThreadLinks($threadId)
{
	$q=sqlvprintf("UPDATE wtfb2_threadlinks SET `read`=0 WHERE (threadId={0})",array($threadId));
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
	$q=sqlvprintf("SELECT * FROM wtfb2_users WHERE (userName={0})",array($_POST['recipient']));
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
			$extraValues=sqlvprintf(",{0}",array($me['guildId']));
		}
	}
	$q=sqlvprintf("INSERT INTO wtfb2_threads (updated,lastPosterId,subject $extraFields) VALUES ('$now',{0},{1} $extraValues)",array($_SESSION['userId'],$_POST['subject']));
	$r=runEscapedQuery($q);
	$threadId=getLastInsertId();
}
else
{
	$threadId=(int)$_POST['thread'];
	$q=sqlvprintf("SELECT * FROM wtfb2_threadlinks WHERE (threadId={0}) AND (userId={1})",array($threadId,$_SESSION['userId']));
	$r=runEscapedQuery($q);
	if (isEmptyResult($r)) jumpErrorPage($language['threadnotexist']);
	$q=sqlvprintf("SELECT *,UNIX_TIMESTAMP(updated) AS tsUpdated FROM wtfb2_threads WHERE (id={0})",array($threadId));
	$r=runEscapedQuery($q);
	if (isEmptyResult($r)) jumpErrorPage($language['threadnotexist']);
	$a=$r[0][0];
	if ((int)$a['tsUpdated']>(int)$_POST['nowstamp'])
	{
		jumpTo('compose.php?thread='.urlencode($threadId).'&notification='.urlencode($language['newreplywhileyouwrote']));
	}

	$q=sqlvprintf("UPDATE wtfb2_threads SET updated='$now',subject={0},lastPosterId={2} WHERE (id={1})",array($_POST['subject'],$threadId,$_SESSION['userId']));
	$r=runEscapedQuery($q);
}

$q=sqlvprintf("INSERT INTO wtfb2_threadentries (threadId,posterId,text,`when`) VALUES ({0},{1},{2},'$now')",array($threadId,$_SESSION['userId'],$_POST['content']));
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
