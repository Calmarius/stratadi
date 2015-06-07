<?php

require_once('userworkerphps.php');
bounceSessionOver();

if (!isset($_GET['id'])) $_GET['id']=$_SESSION['accessId'];

// TODO: Never use $_GET for operations!

$r=runEscapedQuery("SELECT * FROM wtfb2_accesses WHERE (id={0})", $_GET['id']);
if (isEmptyResult($r))
{
	jumpErrorPage($language['usernamenotexist']);
}
$access=$r[0][0];

if ($_GET['cmd']=='kick')
{
	if (!imAccountMaster()) jumpErrorPage($language['accessdenied']);
	if ($_GET['id']==$_SESSION['accessId']) jumpErrorPage($language['youmustgiveawaymasteraccessfirst']);
	$r=runEscapedQuery("SELECT COUNT(*) AS cnt FROM wtfb2_accesses WHERE (accountId={0})",$_SESSION['userId']);
	$a=$r[0][0];
	if ($a['cnt']<=1) jumpErrorPage($language['cantkicklastking']);
	runEscapedQuery("UPDATE wtfb2_accesses SET accountId=0 WHERE (id={0})",$_GET['id']);
	runEscapedQuery("INSERT INTO wtfb2_worldevents (eventTime,type,recipientId) VALUES (NOW(),'forcelogout',{0})",$_SESSION['userId']);
}
if ($_GET['cmd']=='setmaster')
{
	if (!imAccountMaster()) jumpErrorPage($language['accessdenied']);
	runEscapedQuery("UPDATE wtfb2_users SET masterAccess={1} WHERE (id={0})",$_SESSION['userId'],$_GET['id']);
}
if ($_GET['cmd']=='leave')
{
	if (imAccountMaster()) jumpErrorPage($language['youmustgiveawaymasteraccessfirst']);
	$r=runEscapedQuery("SELECT COUNT(*) AS cnt FROM wtfb2_accesses WHERE (accountId={0})",$_SESSION['userId']);
	$a=$r[0][0];
	if ($a['cnt']<=1) jumpErrorPage($language['cantkicklastking']);
	runEscapedQuery("UPDATE wtfb2_accesses SET accountId=0 WHERE (id={0})",$_SESSION['accessId']);
	jumpTo('doreset.php');
}
if ($_GET['cmd']=='newking')
{
	$r=runEscapedQuery("SELECT * FROM wtfb2_accesses WHERE (userName={0})",$_POST['newking']);
	if (isEmptyResult($r)) jumpErrorPage($language['usernamenotexist']);
	$a=$r[0][0];
	$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$a['accountId']);
	if (!isEmptyResult($r)) jumpErrorPage($language['thiskingisalreadycontrollingakingdom']);
	runEscapedQuery("UPDATE wtfb2_accesses SET accountId={1} WHERE (id={0})",$a['id'],$_SESSION['userId']);
}

jumpTo('editkings.php');

?>
