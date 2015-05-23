<?php

require_once("userworkerphps.php");

$q=
sqlPrintf
(
	"
	SELECT COUNT(*) AS cnt
	FROM
		wtfb2_threadlinks
		LEFT JOIN wtfb2_threads ON (wtfb2_threadlinks.threadId=wtfb2_threads.id)
		LEFT JOIN wtfb2_users ON (wtfb2_users.id=wtfb2_threads.lastPosterId)
	WHERE (wtfb2_threadlinks.userId='{1}')
	ORDER BY wtfb2_threads.updated DESC
	",array($_SESSION['userId'])
);

$r=doMySqlQuery($q,'jumpErrorPage');
$a=mysql_fetch_assoc($r);
$cnt=ceil($a['cnt']/$config['pageSize']);

if (!isset($_GET['p'])) $_GET['p']=0;

$q=
sqlPrintf
(
	"
	SELECT wtfb2_users.userName,wtfb2_users.id AS senderId,wtfb2_threads.subject,wtfb2_threads.updated,wtfb2_threads.id AS messageId,wtfb2_threadlinks.`read`,wtfb2_threadlinks.id AS linkId
	FROM
		wtfb2_threadlinks
		LEFT JOIN wtfb2_threads ON (wtfb2_threadlinks.threadId=wtfb2_threads.id)
		LEFT JOIN wtfb2_users ON (wtfb2_users.id=wtfb2_threads.lastPosterId)
	WHERE (wtfb2_threadlinks.userId='{1}')
	ORDER BY wtfb2_threads.updated DESC
	LIMIT {2},{3}
	",array($_SESSION['userId'],((int)$_GET['p'])*$config['pageSize'],$config['pageSize'])
);
// 
$r=doMySqlQuery($q,'jumpErrorPage');
$params=array();
$params['letterlinks']=array();
while($row=mysql_fetch_assoc($r))
{
	$params['letterlinks'][]=$row;
}
$params['pages']=$cnt;


showInBox('templates/messagestemplate.php',$params)

?>
