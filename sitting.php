<?php

require_once('userworkerphps.php');

$r=doMySqlQuery(sqlPrintf("SELECT d.*,u.userName FROM wtfb2_deputies d JOIN wtfb2_users u ON (d.sponsorId=u.id) WHERE (deputyId='{1}')",array($_SESSION['userId'])));
$deputies=array();
while($a=mysql_fetch_assoc($r))
{
	$deputies[]=$a;
}

showInBox('templates/sittingtemplate.php',array('deputies'=>$deputies));

?>
