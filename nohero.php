<?php


require_once('userworkerphps.php');

$myId=$_SESSION['userId'];
$r=runEscapedQuery("SELECT * FROM wtfb2_heroes WHERE (ownerId={0})",$myId);
if (!isEmptyResult($r)) jumpErrorPage($language['youalreadyhaveahero']);

$r=runEscapedQuery
(
	"
		SELECT h.*,v.villageName,v.x,v.y
		FROM wtfb2_heroes h
		INNER JOIN wtfb2_villages v ON (v.id=h.inVillage)
		WHERE (v.ownerId={0}) AND (h.ownerId=0)
	",$myId
);
$heroes=array();
$xpFn=$config['experienceFunction'];
foreach ($r[0] as $row)
{
	$row['level']=$xpFn($row['offense'])+$xpFn($row['defense'])+1;
	$heroes[]=$row;
}

showInBox('templates/noherotemplate.php',array('heroes'=>$heroes));



?>
