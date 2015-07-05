<?php

require_once("userworkerphps.php");

$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$_SESSION['userId']);
$me=$r[0][0];


$invitedPlayers=array();
if (isset($_SESSION['userId']))
{
	$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (refererId={0})",$_SESSION['userId']);
	foreach ($r[0] as $row)
	{
		$invitedPlayers[]=$row;
	}
}

$inviteForm=new Template('templates/inviteplayergame.php',array('invited'=>$invitedPlayers,'me'=>$me));
$box=new Template("templates/standardboxtemplate.php",array('content'=>$inviteForm->getContents()));
$page=new Template('templates/basiclayout.php',array('title'=>'WTFBattles II','content'=>$box->getContents()));
$page->render();

?>
