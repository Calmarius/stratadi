<?php

require_once("userworkerphps.php");

if (!isset($_SESSION['userId'])) jumpErrorPage($language['sessionisover']);

$threadId=(int)$_GET['id'];
$linkId=(int)$_GET['link'];

$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$_SESSION['userId']);
$me=$r[0][0];

$r=runEscapedQuery("SELECT * FROM wtfb2_threads WHERE (id={0}) AND ((guildId IS NULL) OR (guildId={1}))",$threadId,$me['guildId']);
if (isEmptyResult($r)) jumpErrorPage($language['threadnotexist']);
$threadInfo=$r[0][0];

$r=runEscapedQuery("SELECT * FROM wtfb2_threadlinks WHERE (userId={0}) AND ((id={1})".($threadInfo['guildId']!='' ? " OR 1":'').")",$_SESSION['userId'],$linkId);
if (isEmptyResult($r)) jumpErrorPage($language['threadnotexist']);

$r=runEscapedQuery("UPDATE wtfb2_threadlinks SET `read`=1 WHERE (userId={0}) AND (id={1})",$_SESSION['userId'],$linkId);


$r=runEscapedQuery(
    "SELECT wtfb2_users.userName,wtfb2_threadlinks.*
    FROM wtfb2_threadlinks
    INNER JOIN wtfb2_users ON (wtfb2_threadlinks.userId=wtfb2_users.id)
    WHERE (threadId={0})",
    $threadId
);
$participants=array();
foreach ($r[0] as $row)
{
	$participants[]=$row;
}

$r=runEscapedQuery
(
	"SELECT COUNT(*) AS cnt
	 FROM
	 	wtfb2_threadentries
	 WHERE (threadId={0})
	 ORDER BY  `when`DESC"
	,$threadId
);

$a=$r[0][0];
$cnt=ceil($a['cnt']/$config['pageSize']);
if (!isset($_GET['p'])) $_GET['p']=0;

$r=runEscapedQuery
(
    "SELECT wtfb2_threadentries.*,wtfb2_users.userName
	 FROM
	 	wtfb2_threadentries
	 	LEFT JOIN wtfb2_users ON (wtfb2_threadentries.posterId=wtfb2_users.id)
	 WHERE (threadId={0})
	 ORDER BY  `when`DESC
	 LIMIT {1},{2}"
	,$threadId,(int)$_GET['p']*$config['pageSize'],$config['pageSize']
);

$params=array();
$params['thread']=$threadInfo;
$params['participants']=$participants;
$params['entries']=array();
$params['guildLetter']=is_numeric($threadInfo['guildId']);
$params['pages']=$cnt;
$params['id']=$_GET['id'];
$params['link']=$_GET['link'];
foreach ($r[0] as $row)
{
	$params['entries'][]=$row;
}

showInBox('templates/threadviewtemplate.php',$params);

?>
