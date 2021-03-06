<?php

require_once('userworkerphps.php');
htmlize($_POST);

function bounceBack()
{
	header('HTTP/1.0 301 Moved permanently');
	header('Location: registration.php');
	die();
}

// TODO: (task) Cleanup inactive accounts.
function cleanupInactiveAccounts()
{
	global $config;
	$r=runEscapedQuery("SELECT TIMESTAMPDIFF(SECOND,{0},NOW())  AS started",$config['gameStarted']);
	$stime=$r[0][0];
	if ((int)$stime['started']<0) return;
	$q="DELETE FROM wtfb2_users WHERE (permission='inactive') AND (TIMESTAMPDIFF(HOUR,regDate,NOW())>=24)";
	$r=runEscapedQuery($q);
}


function isRegisteredName($name)
{
	$q=sqlvprintf("SELECT * FROM wtfb2_users WHERE (userName={0})",array($name));
	$r=runEscapedQuery($q);
	return count($r[0]);
}

function isRegisteredKing($name)
{
	$q=sqlvprintf("SELECT * FROM wtfb2_accesses WHERE (userName={0})",array($name));
	$r=runEscapedQuery($q);
	return count($r[0]);
}

function isRegisteredEMail($mail)
{
	global $config;
	if ($mail==$config['adminMail']) return false;
	$q=sqlvprintf("SELECT * FROM wtfb2_accesses WHERE (eMail={0})",array($mail));
	$r=runEscapedQuery($q);
	return count($r[0]);
}

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

function registerUser($data)
{
	global $language;
	global $config;

	$lId=0;
	$genderStr=sqlvprintf("{0}", array($data['gender']!='' ? $data['gender'] : null));
	$activationToken='';
	
	if (!isset($data['year'])) $data['year'] = '';
	if (!isset($data['month'])) $data['month'] = '';
	if (!isset($data['day'])) $data['day'] = '';
	if (!isset($data['kingdomname'])) $data['kingdomname'] = '';
	if (!isset($data['password'])) $data['password'] = '';
	if (!isset($data['mail'])) $data['mail'] = '';
	if (!isset($data['city'])) $data['city'] = '';
	if (!isset($data['youravatar'])) $data['youravatar'] = '';
	if (!isset($data['referer'])) $data['referer'] = '';
	if (!isset($data['heroname'])) $data['heroname'] = '';
	if (!isset($data['heroavatar'])) $data['heroavatar'] = '';
	
	if ($data['year']=='') $data['year']='0000';
	if ($data['month']=='') $data['month']='00';
	if ($data['day']=='') $data['day']='00';
	for($i=0;$i<$config['activationCodeLength'];$i++)
	{
		$activationToken.=chr(ord('A')+rand(0,25));
	}
	if (isset($data['registerkingdom']))
	{
		// create kingdom
		// TODO: (refactor) Normalize this.
		$q=
		sqlvprintf(
		"
			INSERT INTO wtfb2_users (userName,regDate,avatarLink,lastUpdate,refererId,lastLoaded)
			VALUES
			({0},NOW(),{7},NOW(),{9},NOW());
		",array(
			$data['kingdomname'],
			$data['password'],
			$data['mail'],
			$data['city'],
			$data['year'],
			$data['month'],
			$data['day'],
			$data['youravatar'],
			$activationToken,
			$data['referer'])
		);
		$error="";
		$r=runEscapedQuery($q);
		$lId=getLastInsertId();
	}
	// create hero
	$q=sqlvprintf("INSERT INTO wtfb2_heroes (ownerId,name,avatarLink) VALUES ({0},{1},{2})",array($lId,@$data['heroname'],@$data['heroavatar']));
	$r=runEscapedQuery($q);
	// create access
	runEscapedQuery("
        INSERT INTO wtfb2_accesses (accountId,userName,passwordHash,eMail,city,birth,gender,permission,activationToken) 
        VALUES ({0},{1},MD5({2}),{3},{4},{5},$genderStr,'inactive',{6})",
		$lId,
		$data['username'],
		$data['password'],
		$data['mail'],
		$data['city'],
		"${data['year']}-${data['month']}-${data['day']} 0:00:00",
		$activationToken
	);
	$newAccess=getLastInsertId();
	runEscapedQuery("UPDATE wtfb2_users SET masterAccess={0} WHERE (id={1})",$newAccess,$lId);
	// create languages entry
	$values=array();
	if (isset($data['languages']))
	{
		foreach($data['languages'] as $key=>$value)
		{
			$values[]=sqlvprintf("({0},{1})",array($newAccess,$value));
		}
		runEscapedQuery("INSERT INTO wtfb2_spokenlanguages (playerId,languageId) VALUES ".implode(',',$values));
	}

	$recipient=$data['mail'];
	$from='=?UTF-8?B?'.base64_encode($config['adminName']).'?= <'.$config['adminMail'].'>';
	$subject='=?UTF-8?B?'.base64_encode($language['regmailsubject']).'?=';
	$header="MIME-Version: 1.0\r\nContent-type: text/plain; charset=utf-8"; // might sucks
	$message=xprintf($language['regmailcontent'],array($data['username'],$activationToken));

	if (!mail($recipient,$subject,$message,$header)) jumpErrorPage($language['unabletosendmail']);

}


$_SESSION['registrationparms']=$_POST;

//checking username
$s=$_POST['username'];
if (strlen(trim($s))<$config['minUserNameLength'])
{
	$_SESSION['registrationparms']['userNameError']=makeErrorMessage($language['usernameshort']);
	bounceBack();
}
if (strlen($s)>$config['maxUserNameLength'])
{
	$_SESSION['registrationparms']['userNameError']=makeErrorMessage($language['usernamelong']);
	bounceBack();
}
if (isRegisteredKing($s))
{
	$_SESSION['registrationparms']['kingNameError']=makeErrorMessage($language['usernamealreadyregistered']);
	bounceBack();
}
//checking kingdom name
if (isset($_POST['registerkingdom']))
{
	$s=$_POST['kingdomname'];
	if (strlen(trim($s))<$config['minUserNameLength']) // TODO: (refactor) make function from this checking stuff
	{
		$_SESSION['registrationparms']['userNameError']=makeErrorMessage($language['usernameshort']);
		bounceBack();
	}
	if (strlen($s)>$config['maxUserNameLength'])
	{
		$_SESSION['registrationparms']['userNameError']=makeErrorMessage($language['usernamelong']);
		bounceBack();
	}
	if (isRegisteredName($s))
	{
		$_SESSION['registrationparms']['userNameError']=makeErrorMessage($language['usernamealreadyregistered']);
		bounceBack();
	}
	// checking hero name
	$s=$_POST['heroname'];
	if (strlen(trim($s))<$config['minHeroNameLength'])
	{
		$_SESSION['registrationparms']['heroNameError']=makeErrorMessage($language['heronametooshort']);
		bounceBack();
	}
}
//cleanupInactiveAccounts();
//checking e-mail
$s=$_POST['mail'];
if (!preg_match('/^[^@]+@([^\.]+\.)+[^\.]+$/',$s))
{
	$_SESSION['registrationparms']['mailError']=makeErrorMessage($language['invalidemail']);
	bounceBack();
}
if (isRegisteredEMail($s))
{
	$_SESSION['registrationparms']['mailError']=makeErrorMessage($language['emailalreadyused']);
	bounceBack();
}
// checking passwords
$s=$_POST['password'];
if (strlen($s)<$config['minUserPasswordLength'])
{
	$_SESSION['registrationparms']['passwordError']=makeErrorMessage($language['passwordtooshort']);
	bounceBack();
}
if ($_POST['password']!=$_POST['password2'])
{
	$_SESSION['registrationparms']['passwordError']=makeErrorMessage($language['passwordnotmatch']);
	bounceBack();
}
//checking day
$s=$_POST['day'];
if (($s!="") && ((int)$s<1) || ((int)$s>31))
{
	$_SESSION['registrationparms']['dateError']=makeErrorMessage($language['invalidday']);
	bounceBack();
}
// checking year
$s=$_POST['year'];
$date=getdate();
if (($s!="") && ($date['year']<(int)$s))
{
	$_SESSION['registrationparms']['dateError']=makeErrorMessage($language['invalidyear']);
	bounceBack();
}
// checking and converting avatars
if (isset($_POST['registerkingdom']))
{
	if ($_FILES['youravatar']['size']>0)
	{
		$retImage=createAvatar($_FILES['youravatar']['tmp_name'],$_FILES['youravatar']['type'],$config['avatarSize']);
		if ($retImage==FALSE)
		{
			$_SESSION['registrationparms']['yourAvatarError']=makeErrorMessage($language['invalidpictureformat']);
			bounceBack();
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
			$_SESSION['registrationparms']['heroAvatarError']=makeErrorMessage($language['invalidpictureformat']);
			bounceBack();
		}
		$avatarName="avatars/".time().rand(1,100000).".png";
		imagepng($retImage,$avatarName);
		$_POST['heroavatar']=$avatarName;
	}
}
registerUser($_POST);
$_SESSION['regEmail']=$_POST['mail'];
$_SESSION['regName']=$_POST['username'];

header('HTTP/1.0 301');
header('Location: successfulregistration.php');

?>
