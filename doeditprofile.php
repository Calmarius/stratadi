<?php

require_once('userworkerphps.php');
htmlize($_POST);

if (!isset($_SESSION['userId'])) jumpErrorPage($language['sessionisover']);

function createAvatar($fileName,$type,$thumbsize)
{
	if ($type=="image/jpeg") $image=imagecreatefromjpeg($fileName);
	else if ($type=="image/png") $image=imagecreatefrompng($fileName);
	else if ($type=="image/bmp") $image=imagecreatefromwbmp($fileName);
	else return FALSE;
	$newImage=imagecreatetruecolor($thumbsize,$thumbsize);
	$size=getimagesize($fileName);
	$maxdim=$size[0]>$size[1] ? $size[0] : $size[1];
	$sfactor=$thumbsize/$maxdim;
	$newWidth=$size[0]*$sfactor;
	$newHeight=$size[1]*$sfactor;
	$topx=($thumbsize-$newWidth)*0.5;
	$topy=($thumbsize-$newHeight)*0.5;
	imagecopyresampled($newImage,$image,$topx,$topy,0,0,$newWidth,$newHeight,$size[0],$size[1]);
	return $newImage;
}

// checking hero name
$s=$_POST['heroname'];
if (strlen(trim($s))<$config['minHeroNameLength'])
{
	jumpErrorPage($language['heronametooshort']);
}
// checking passwords
$setPasswordText='';
/*if ($_POST['password']!='')
{
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}') AND (passwordHash=MD5('{2}') )",array($_SESSION['userId'],$_POST['oldpassword'])));
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
}*/

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
// checking and converting avatars
if ($_FILES['youravatar']['size']>0)
{
	$retImage=createAvatar($_FILES['youravatar']['tmp_name'],$_FILES['youravatar']['type'],$config['avatarSize']);
	if ($retImage==FALSE)
	{
		jumpErrorPage($language['invalidpictureformat']);
	}
	$avatarName="avatars/".time().rand(1,100000).".png";
	imagepng($retImage,$avatarName);
	$_POST['youravatar']=$avatarName;
}
if ($_FILES['heroavatar']['size']>0)
{
	$retImage=createAvatar($_FILES['heroavatar']['tmp_name'],$_FILES['heroavatar']['type'],$config['avatarSize']);
	if ($retImage==FALSE)
	{
		jumpErrorPage($language['invalidpictureformat']);
	}
	$avatarName="avatars/".time().rand(1,100000).".png";
	imagepng($retImage,$avatarName);
	$_POST['heroavatar']=$avatarName;
}

$date="${_POST['year']}-${_POST['month']}-${_POST['day']} 0:00:00";

$avLinkMod='';
$hLinkMod='';
if ($_POST['youravatar']!='') $avLinkMod=sqlPrintf("avatarLink='{1}',",array($_POST['youravatar']));
if ($_POST['heroavatar']!='') $hLinkMod=sqlPrintf("avatarLink='{1}',",array($_POST['heroavatar']));

$q=sqlPrintf(
	"
	UPDATE wtfb2_users
	SET $avLinkMod profile='{4}' $setPasswordText
	WHERE (id='{5}')
	",array($_POST['gender'],$_POST['town'],$date,$_POST['profile'],$_SESSION['userId']));
$r=doMySqlQuery($q,'jumpErrorPage');

/*// set languages
doMySqlQuery("DELETE FROM wtfb2_spokenlanguages WHERE (playerId=${_SESSION['userId']})");
$values=array();
foreach($_POST['spokenlanguages'] as $key=>$value)
{
	$values[]=sqlPrintf("('{1}','{2}')",array($_SESSION['userId'],$value));
	doMySqlQuery("INSERT INTO wtfb2_spokenlanguages (playerId,languageId) VALUES ".implode(',',$values));
}*/


$q=sqlPrintf("UPDATE wtfb2_heroes SET $hLinkMod name='{1}' WHERE (ownerId='{2}')",array($_POST['heroname'],$_SESSION['userId']));
$r=doMySqlQuery($q,'jumpErrorPage');

jumpTo('viewplayer.php');


?>
