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
    $access = runEscapedQuery("SELECT * FROM wtfb2_accesses WHERE (userName={0})", $username);
	if (!isset($access[0][0]))
	{
		$_SESSION['loginparms']['usernameError']=makeErrorMessage($language['usernamenotexist']);
		bounceBack();
	}

	$passwordPart="AND (a.passwordHash=MD5({1}))";
	if (in_array($username,$config['openAccounts'])) $passwordPart='';
    $access = runEscapedQuery(
		"
			SELECT a.*,u.id AS userId
			FROM wtfb2_accesses a
			LEFT JOIN wtfb2_users u ON (a.accountId=u.id)
			WHERE (a.userName={0}) $passwordPart
		",$username,$password
    );

	if (!isset($access[0][0]))
	{
		$_SESSION['loginparms']['passwordError']=makeErrorMessage($language['badpassword']);
		bounceBack();		
	}
    $access = $access[0][0];

	if ($access['userId']===null)
	{
		jumpInformationPage($language['noaccountassociated'],$language['noaccountassociatedinfo']);
	}
	$_SESSION['loginparms']=array();
	if ($access['permission']=='banned') jumpErrorPage($language['youarebanned']);
	$_SESSION['userId']=$access['accountId'];
	$_SESSION['accessId']=$access['id'];
	$_SESSION['permission']=$access['permission'];
	if ($access['permission']=='inactive') jumpTo('activate.php');
	$playerId=$access['accountId'];
	loginUpdateAll($playerId);
	jumpTo('game.php');
}

// delete those kingdoms who need to be deleted

$kingdomsToDelete=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (willDeleteAt IS NOT NULL) AND (willDeleteAt<NOW())");
foreach ($kingdomsToDelete[0] as $kingdomToDelete)
{
	runEscapedQuery("UPDATE wtfb2_accesses SET accountId=0 WHERE (accountId={0})",$kingdomToDelete['id']);
}


runEscapedQuery("DELETE FROM wtfb2_users WHERE (willDeleteAt IS NOT NULL) AND (willDeleteAt<NOW())");


// login user

$_SESSION['loginparms']=$_POST;

login($_POST['username'],$_POST['password']);

?>
