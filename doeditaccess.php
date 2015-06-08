<?php

require_once('userworkerphps.php');
htmlize($_POST);

bounceSessionOver();

// checking passwords
$setPasswordText='';
if ($_POST['password']!='')
{
	$r=runEscapedQuery("SELECT * FROM wtfb2_accesses WHERE (userName={0}) AND (id={1})",$config['demoAccountName'], $_SESSION['accessId']);
	if (!isEmptyResult($r)) jumpErrorPage($language['demoaccesscannotbechanged']);
	$r=runEscapedQuery("SELECT * FROM wtfb2_accesses WHERE (id={0}) AND (passwordHash=MD5({1}) )",$_SESSION['accessId'],$_POST['oldpassword']);
	if (isEmptyResult($r)) jumpErrorPage($language['badpassword']);
	$s=$_POST['password'];
	if (strlen($s)<$config['minUserPasswordLength'])
	{
		jumpErrorPage($language['passwordtooshort']);
	}
	if ($_POST['password']!=$_POST['password2'])
	{
		jumpErrorPage($language['passwordnotmatch']);
	}
	$setPasswordText=sqlvprintf(",passwordHash=MD5({0})",array($s));
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

$genderStr=$_POST['gender']=='' ? "NULL" : sqlvprintf("{0}",array($_POST['gender']));

$q=sqlvprintf(
	"
	UPDATE wtfb2_accesses
	SET gender=$genderStr,city={0},birth={1} $setPasswordText
	WHERE (id={2})
	",array($_POST['town'],$date,$_SESSION['accessId']));
$r=runEscapedQuery($q);

// set languages
runEscapedQuery("DELETE FROM wtfb2_spokenlanguages WHERE (playerId={0})", $_SESSION['accessId']);
$values=array();
foreach($_POST['spokenlanguages'] as $key=>$value)
{
	$values[]=sqlvprintf("({0},{1})",array($_SESSION['accessId'],$value));
}
if (count($values))
	runEscapedQuery("INSERT INTO wtfb2_spokenlanguages (playerId,languageId) VALUES ".implode(',',$values));

jumpTo('viewaccess.php');


?>
