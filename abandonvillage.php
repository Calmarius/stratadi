<?php

require_once("userworkerphps.php");

bounceSessionOver();

$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_villages WHERE (id='{1}') AND (ownerId='{2}')",array($_GET['id'],$_SESSION['userId'])));
if (mysql_num_rows($r)==0) 
{
	jumpErrorPage($language['accessdenied']);
}
$a=mysql_fetch_assoc($r);

showInBox('templates/abandonvillage.php',array('village'=>$a));

?>
