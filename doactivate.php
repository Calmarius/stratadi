<?php

require_once("userworkerphps.php");

if (secondsToStart()>0)
{
    jumpTo('countdown.php');
}

function activateAccount($userId,$activationCode)
{
    global $language;
    $activationCode=trim($activationCode);
    $access=runEscapedQuery("SELECT * FROM wtfb2_accesses WHERE (id={0})",$userId);
    if (!isset($access[0][0])) jumpErrorPage($language['usernamenotexist']);
    $access = $access[0][0];
    if ($access['activationToken']!=$activationCode)
    {
        $_SESSION['activationparms']=$_POST;
        $_SESSION['activationparms']['activationcodeError']=makeErrorMessage($language['wrongactivationcode']);
        jumpTo('activate.php');
    }
    $_SESSION['permission']='user';
    runEscapedQuery("UPDATE wtfb2_accesses SET permission='user' WHERE (id={0})",$access['id']);
    $_SESSION['activationparms']=array();
    jumpTo('game.php');
}

if (!isset($_SESSION['userId'])) jumpTo('login.php');

activateAccount($_SESSION['accessId'],$_POST['activationcode']);


?>
