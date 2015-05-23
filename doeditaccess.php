<?php

require_once('userworkerphps.php');
htmlize($_POST);

bounceSessionOver();

// checking passwords
$setPasswordText='';
if ($_POST['password']!='')
{
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_accesses WHERE (userName='{1}') AND (id='{2}')",array($config['demoAccountName'], $_SESSION['accessId'])));
	if (mysql_num_rows($r)!=0) jumpErrorPage($language['demoaccesscannotbechanged']);
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_accesses WHERE (id='{1}') AND (passwordHash=MD5('{2}') )",array($_SESSION['accessId'],$_POST['oldpassword'])));
	if (mysql_num_rows($r)==0) jumpErrorPage($language['badpassword']);
	$s=$_POST['password'];
	if (strlen($s)<$config['minUserPasswordLength'])
	{
		jumpErrorPage($language['passwordtooshort']);
	}
	if ($_POST['password']!=$_POST['password2'])
	{
		jumpErrorPage($language['passwordnotmatch']);
	}
	$setPasswordText=sqlPrintf(",passwordHash=MD5('{1}')",array($s));
}

//checking day
$s=$_POST['day'];
if (($s!="") && ((int)$s<1) || ((int)$s>31))
{
	jumpErrorPage($language['invalidday']);
}
// checking year
$s=$_POST['year'];
$date=getdate();
if (($s!="") && ($date['year']<(int)$s))
{
	jumpErrorPage($language['invalidyear']);
}

$date="${_POST['year']}-${_POST['month']}-${_POST['day']} 0:00:00";

$genderStr=$_POST['gender']=='' ? "NULL" : sqlPrintf("'{1}'",array($_POST['gender']));

$q=sqlPrintf(
	"
	UPDATE wtfb2_accesses
	SET gender=$genderStr,city='{1}',birth='{2}' $setPasswordText
	WHERE (id='{3}')
	",array($_POST['town'],$date,$_SESSION['accessId']));
$r=doMySqlQuery($q,'jumpErrorPage');

// set languages
doMySqlQuery("DELETE FROM wtfb2_spokenlanguages WHERE (playerId='${_SESSION['accessId']}')");
$values=array();
foreach($_POST['spokenlanguages'] as $key=>$value)
{
	$values[]=sqlPrintf("('{1}','{2}')",array($_SESSION['accessId'],$value));
}
if (count($values))
	doMySqlQuery("INSERT INTO wtfb2_spokenlanguages (playerId,languageId) VALUES ".implode(',',$values));

jumpTo('viewaccess.php');


?>
