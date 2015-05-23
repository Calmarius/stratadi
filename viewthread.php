<?php

require_once("userworkerphps.php");

if (!isset($_SESSION['userId'])) jumpErrorPage($language['sessionisover']);

$threadId=(int)$_GET['id'];
$linkId=(int)$_GET['link'];

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($_SESSION['userId'])),jumpErrorPage);
$me=mysql_fetch_assoc($r);

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_threads WHERE (id='{1}') AND ((guildId IS NULL) OR (guildId='{2}'))",array($threadId,$me['guildId'])),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['threadnotexist']);
$threadInfo=mysql_fetch_assoc($r);

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_threadlinks WHERE (userId='{1}') AND ((id='{2}')".($threadInfo['guildId']!='' ? " OR 1":'').")",array($_SESSION['userId'],$linkId)),'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['threadnotexist']);

$r=doMySqlQuery(sqlPrintf("UPDATE wtfb2_threadlinks SET `read`=1 WHERE (userId='{1}') AND (id='{2}')",array($_SESSION['userId'],$linkId)),'jumpErrorPage');


$r=doMySqlQuery(sqlPrintf("SELECT wtfb2_users.userName,wtfb2_threadlinks.* FROM wtfb2_threadlinks INNER JOIN wtfb2_users ON (wtfb2_threadlinks.userId=wtfb2_users.id) WHERE (threadId='{1}')",array($threadId)),'jumpErrorPage');
$participants=array();
while($row=mysql_fetch_assoc($r))
{
	$participants[]=$row;
}

$r=doMySqlQuery
(
	sqlPrintf
	(
		"SELECT COUNT(*) AS cnt
		 FROM
		 	wtfb2_threadentries
		 WHERE (threadId='{1}')
		 ORDER BY  `when`DESC"
		,array($threadId)
	)
	,'jumpErrorPage'
);

$a=mysql_fetch_assoc($r);
$cnt=ceil($a['cnt']/$config['pageSize']);
if (!isset($_GET['p'])) $_GET['p']=0;

$r=doMySqlQuery
(
	sqlPrintf
	(
		"SELECT wtfb2_threadentries.*,wtfb2_users.userName
		 FROM
		 	wtfb2_threadentries
		 	LEFT JOIN wtfb2_users ON (wtfb2_threadentries.posterId=wtfb2_users.id)
		 WHERE (threadId='{1}')
		 ORDER BY  `when`DESC
		 LIMIT {2},{3}"
		,array($threadId,(int)$_GET['p']*$config['pageSize'],$config['pageSize'])
	)
	,'jumpErrorPage'
);

$params=array();
$params['thread']=$threadInfo;
$params['participants']=$participants;
$params['entries']=array();
$params['guildLetter']=is_numeric($threadInfo['guildId']);
$params['pages']=$cnt;
$params['id']=$_GET['id'];
$params['link']=$_GET['link'];
while($row=mysql_fetch_assoc($r))
{
	$params['entries'][]=$row;
}

showInBox('templates/threadviewtemplate.php',$params);

?>
