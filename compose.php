<?php

require_once("userworkerphps.php");

bounceSessionOver();

$r=runEscapedQuery("SELECT UNIX_TIMESTAMP(NOW()) AS nowstamp");
$a=$r[0][0];


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
	$r=runEscapedQuery("SELECT * FROM wtfb2_threadlinks WHERE (userId={0}) AND (threadId={1})",$_SESSION['userId'],$_GET['thread']);
	if (isEmptyResult($r)) jumpErrorPage($language['threadnotexist']);
	$r=runEscapedQuery
	(
		"
			SELECT wtfb2_threadentries.*,wtfb2_users.userName
			FROM wtfb2_threadentries LEFT JOIN wtfb2_users ON (wtfb2_threadentries.posterId=wtfb2_users.id)
			WHERE (threadId={0}) ORDER BY  `when` DESC LIMIT 0, {1}
	    ",$_GET['thread'], (int)$config['pageSize']
	);
	foreach ($r[0] as $row)
	{
		$param['entries'][]=$row;
	}
}

showInBox('templates/composemessage.php',$param);


?>
