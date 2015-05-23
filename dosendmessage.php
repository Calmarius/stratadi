<?php

require_once('userworkerphps.php');
htmlize($_POST);

function sendThreadLink($threadId,$userId,$isRead)
{
	$q=sqlPrintf("SELECT * FROM wtfb2_threadlinks WHERE (threadId='{1}') AND (userId='{2}')",array($threadId,$userId));
	$r=doMySqlQuery($q,'jumpErrorPage');
	if (mysql_num_rows($r)==0)
	{
		$q=sqlPrintf("INSERT INTO wtfb2_threadlinks (threadId,userId,`read`) VALUES ({1},{2},{3})",array($threadId,$userId,$isRead));
		$r=doMySqlQuery($q,'jumpErrorPage');	
	}
	else
	{
		$q=sqlPrintf("UPDATE wtfb2_threadlinks SET `read`='{1}' WHERE (threadId='{2}') AND (userId='{3}')",array($isRead,$threadId,$userId));
		$r=doMySqlQuery($q,'jumpErrorPage');	
	}
}

function updateThreadLinks($threadId)
{
	$q=sqlPrintf("UPDATE wtfb2_threadlinks SET `read`=0 WHERE (threadId='{1}')",array($threadId));
	$r=doMySqlQuery($q,'jumpErrorPage');	
}

if (!isset($_SESSION['userId'])) jumpErrorPage($language['sessionisover']);
$userId=$_SESSION['userId'];
$r=doMySqlQuery("SELECT NOW() AS now");
$a=mysql_fetch_assoc($r);
$now=$a['now'];

if (isset($_POST['extra']))
{
	$extra=$_POST['extra'];
	if ($extra=='circular')
	{
		$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId='{1}') AND (permission='circular')",array($_SESSION['userId'])));
		if (mysql_num_rows($r)==0) jumpErrorPage('accessdenied');
	}
	if ($extra=='guildthread')
	{
		$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_guildpermissions WHERE (userId='{1}') AND (permission='moderate')",array($_SESSION['userId'])));
		if (mysql_num_rows($r)==0) jumpErrorPage('accessdenied');
	}
}

$_SESSION['lastcomposition']['subject']=$_POST['subject'];
$_SESSION['lastcomposition']['content']=$_POST['content'];


$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($_SESSION['userId'])));
$me=mysql_fetch_assoc($r);

$recipientId=0;
if ($_POST['recipient']!='')
{
	$q=sqlPrintf("SELECT * FROM wtfb2_users WHERE (userName='{1}')",array($_POST['recipient']));
	$r=doMySqlQuery($q,'jumpErrorPage');
	if (mysql_num_rows($r)==0) jumpErrorPage($language['recipientisnotexist']);
	$a=mysql_fetch_assoc($r);
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
	$r=doMySqlQuery($q,'jumpErrorPage');
	$threadId=mysql_insert_id();
}
else
{
	$threadId=(int)$_POST['thread'];
	$q=sqlPrintf("SELECT * FROM wtfb2_threadlinks WHERE (threadId='{1}') AND (userId='{2}')",array($threadId,$_SESSION['userId']));
	$r=doMySqlQuery($q,'jumpErrorPage');
	if (mysql_num_rows($r)==0) jumpErrorPage($language['threadnotexist']);
	$q=sqlPrintf("SELECT *,UNIX_TIMESTAMP(updated) AS tsUpdated FROM wtfb2_threads WHERE (id='{1}')",array($threadId));
	$r=doMySqlQuery($q,'jumpErrorPage');
	if (mysql_num_rows($r)==0) jumpErrorPage($language['threadnotexist']);
	$a=mysql_fetch_assoc($r);
/*	print_r($_POST);	print_r($a);
	echo  (int)$a['tsUpdated']>(int)$_POST['nowstamp'];
	die();*/
	if ((int)$a['tsUpdated']>(int)$_POST['nowstamp'])
	{
		jumpTo('compose.php?thread='.urlencode($threadId).'&notification='.urlencode($language['newreplywhileyouwrote']));
	}
		
	$q=sqlPrintf("UPDATE wtfb2_threads SET updated='$now',subject='{1}',lastPosterId='{3}' WHERE (id='{2}')",array($_POST['subject'],$threadId,$_SESSION['userId']));
	$r=doMySqlQuery($q,'jumpErrorPage');
}

$q=sqlPrintf("INSERT INTO wtfb2_threadentries (threadId,posterId,text,`when`) VALUES ('{1}','{2}','{3}','$now')",array($threadId,$_SESSION['userId'],$_POST['content']));
$r=doMySqlQuery($q,'jumpErrorPage');

updateThreadLinks($threadId);
sendThreadLink($threadId,$_SESSION['userId'],"1");
if (isset($_POST['extra']))
{
	$extra=$_POST['extra'];
	if ($extra=='circular')
	{
		$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (guildId='{1}')",array($me['guildId'])));
		while($row=mysql_fetch_assoc($r))
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
