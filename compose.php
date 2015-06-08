<?php

require_once("userworkerphps.php");

bounceSessionOver();

$r=doMySqlQuery("SELECT UNIX_TIMESTAMP(NOW()) AS nowstamp");
$a=mysql_fetch_assoc($r);


$param=array();
$param['recipient']=@$_GET['name'];
$param['subject']=isset($_GET['subject']) ? $_GET['subject']:@$_SESSION['lastcomposition']['subject'];
$param['thread']=@$_GET['thread'];
$param['extra']=@$_GET['extra'];
$param['entries']=array();
$param['content']=@$_SESSION['lastcomposition']['content'];
$param['nowstamp']=$a['nowstamp'];
$param['notification']=@$_GET['notification'];

if (isset($_GET['thread']))
{
//	$q="SELECT * FROM wtfb2_threadentries WHERE (posterId='${_SESSION['userId']}') AND (threadId='${_GET['thread']}')";
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_threadlinks WHERE (userId='{1}') AND (threadId='{2}')",array($_SESSION['userId'],$_GET['thread'])),'jumpErrorPage');
	if (mysql_num_rows($r)==0) jumpErrorPage($language['threadnotexist']);
	$r=doMySqlQuery
	(
		sqlPrintf
		(
			"
				SELECT wtfb2_threadentries.*,wtfb2_users.userName FROM wtfb2_threadentries LEFT JOIN wtfb2_users ON (wtfb2_threadentries.posterId=wtfb2_users.id) WHERE (threadId='{1}') ORDER BY  `when` DESC LIMIT 0,{2}
			",array($_GET['thread'],$config['pageSize'])
		)
	,'jumpErrorPage'
	);
	while($row=mysql_fetch_assoc($r))
	{
		$param['entries'][]=$row;
	}
}

showInBox('templates/composemessage.php',$param);


?>
