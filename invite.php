<?php

require_once("userworkerphps.php");

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($_SESSION['userId'])));
$me=mysql_fetch_assoc($r);


$invitedPlayers=array();
if (isset($_SESSION['userId']))
{
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (refererId='{1}')",array($_SESSION['userId'])));
	while($row=mysql_fetch_assoc($r))
	{
		$invitedPlayers[]=$row;
	}
}

$inviteForm=new Template('templates/inviteplayergame.php',array('invited'=>$invitedPlayers,'me'=>$me));
$box=new Template("templates/standardboxtemplate.php",array('content'=>$inviteForm->getContents()));
$page=new Template('templates/basiclayout.php',array('title'=>'WTFBattles II','content'=>$box->getContents()));
$page->render();

?>
