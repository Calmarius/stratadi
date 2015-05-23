<?php

require_once('userworkerphps.php');
bounceSessionOver();

if (!isset($_GET['id'])) $_GET['id']=$_SESSION['accessId'];

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_accesses WHERE (id='{1}')",array($_GET['id'])));
if (mysql_num_rows($r)==0)
{
	jumpErrorPage($language['usernamenotexist']);
}
$access=mysql_fetch_assoc($r);

if ($_GET['cmd']=='kick')
{
	if (!imAccountMaster()) jumpErrorPage($language['accessdenied']);
	if ($_GET['id']==$_SESSION['accessId']) jumpErrorPage($language['youmustgiveawaymasteraccessfirst']);
	$r=doMySqlQuery(sqlPrintf("SELECT COUNT(*) AS cnt FROM wtfb2_accesses WHERE (accountId='{1}')",array($_SESSION['userId'])));
	$a=mysql_fetch_assoc($r);
	if ($a['cnt']<=1) jumpErrorPage($language['cantkicklastking']);
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_accesses SET accountId=0 WHERE (id='{1}')",array($_GET['id'])));
	doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (eventTime,type,recipientId) VALUES (NOW(),'forcelogout','{1}')",array($_SESSION['userId'])));
}
if ($_GET['cmd']=='setmaster')
{
	if (!imAccountMaster()) jumpErrorPage($language['accessdenied']);
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET masterAccess='{2}' WHERE (id='{1}')",array($_SESSION['userId'],$_GET['id'])));
}
if ($_GET['cmd']=='leave')
{
	if (imAccountMaster()) jumpErrorPage($language['youmustgiveawaymasteraccessfirst']);
	$r=doMySqlQuery(sqlPrintf("SELECT COUNT(*) AS cnt FROM wtfb2_accesses WHERE (accountId='{1}')",array($_SESSION['userId'])));
	$a=mysql_fetch_assoc($r);
	if ($a['cnt']<=1) jumpErrorPage($language['cantkicklastking']);
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_accesses SET accountId=0 WHERE (id='{1}')",array($_SESSION['accessId'])));
	jumpTo('doreset.php');
}
if ($_GET['cmd']=='newking')
{
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_accesses WHERE (userName='{1}')",array($_POST['newking'])));
	if (mysql_num_rows($r)<=0) jumpErrorPage($language['usernamenotexist']);
	$a=mysql_fetch_assoc($r);
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($a['accountId'])));
	if (mysql_num_rows($r)>0) jumpErrorPage($language['thiskingisalreadycontrollingakingdom']);
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_accesses SET accountId='{2}' WHERE (id='{1}')",array($a['id'],$_SESSION['userId'])));
}

jumpTo('editkings.php');

?>
