<?php

require_once('userworkerphps.php');
require_once('villageupdater.php');

function bounceBack()
{
	header('HTTP/1.0 301');
	header('Location: main.php');
	die();
}

function login($username,$password)
{
	global $language;
	global $config;
	$q=sqlPrintf("SELECT * FROM wtfb2_accesses WHERE (userName='{1}')",array($username));
	$r=doMySqlQuery($q,'jumpErrorPage');
	if (mysql_num_rows($r)<1)
	{
		$_SESSION['loginparms']['usernameError']=makeErrorMessage($language['usernamenotexist']);
		bounceBack();
	}
	$passwordPart="AND (a.passwordHash=MD5('{2}'))";
	if (in_array($username,$config['openAccounts'])) $passwordPart='';
	$q=sqlPrintf(
		"
			SELECT a.*,u.id AS userId
			FROM wtfb2_accesses a
			LEFT JOIN wtfb2_users u ON (a.accountId=u.id)
			WHERE (a.userName='{1}') $passwordPart
		",array($username,$password)
	);
	$r=doMySqlQuery($q,'jumpErrorPage');
	if ((mysql_num_rows($r)<1))
	{
		$_SESSION['loginparms']['passwordError']=makeErrorMessage($language['badpassword']);
		bounceBack();		
	}
	$a=mysql_fetch_assoc($r);
	if ($a['userId']===null)
	{
		jumpInformationPage($language['noaccountassociated'],$language['noaccountassociatedinfo']);
	}
	$_SESSION['loginparms']=array();
	if ($a['permission']=='banned') jumpErrorPage($language['youarebanned']);
	$_SESSION['userId']=$a['accountId'];
	$_SESSION['accessId']=$a['id'];
	$_SESSION['permission']=$a['permission'];
	if ($a['permission']=='inactive') jumpTo('activate.php');
	$playerId=$a['accountId'];
/*	$q=sqlPrintf("SELECT * FROM wtfb2_villages WHERE (ownerId='{1}') LIMIT 0,1",array($playerId));
	$r=doMySqlQuery($q,'jumpErrorPage');*/
	$a=mysql_fetch_assoc($r);
	loginUpdateAll($playerId);
	jumpTo('game.php');
}

// delete those kingdoms who need to be deleted

$r=doMySqlQuery("SELECT * FROM wtfb2_users WHERE (willDeleteAt IS NOT NULL) AND (willDeleteAt<NOW())");
while($row=mysql_fetch_assoc($r))
{
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_accesses SET accountId=0 WHERE (accountId='{1}')",array($row['id'])));
}


doMySqlQuery("DELETE FROM wtfb2_users WHERE (willDeleteAt IS NOT NULL) AND (willDeleteAt<NOW())");


// login user

$_SESSION['loginparms']=$_POST;

login($_POST['username'],$_POST['password']);

?>
