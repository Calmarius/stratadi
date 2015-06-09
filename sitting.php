<?php

require_once('userworkerphps.php');

$r=runEscapedQuery("SELECT d.*,u.userName FROM wtfb2_deputies d JOIN wtfb2_users u ON (d.sponsorId=u.id) WHERE (deputyId={0})",$_SESSION['userId']);
$deputies=array();
foreach ($r[0] as $a)
{
	$deputies[]=$a;
}

showInBox('templates/sittingtemplate.php',array('deputies'=>$deputies));

?>
