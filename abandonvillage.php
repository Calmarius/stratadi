<?php

require_once("userworkerphps.php");

bounceSessionOver();

$r=runEscapedQuery("SELECT * FROM wtfb2_villages WHERE (id={0}) AND (ownerId={1})",$_GET['id'],$_SESSION['userId']);
if (isEmptyResult($r))
{
	jumpErrorPage($language['accessdenied']);
}
$a=$r[0][0];

showInBox('templates/abandonvillage.php',array('village'=>$a));

?>
