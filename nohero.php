<?php


require_once('userworkerphps.php');

$myId=$_SESSION['userId'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_heroes WHERE (ownerId='{1}')",array($myId)),'jumpErrorPage');
if (mysql_num_rows($r)>0) jumpErrorPage($language['youalreadyhaveahero']);

$r=doMySqlQuery
(
	sqlPrintf(
	"
		SELECT h.*,v.villageName,v.x,v.y
		FROM wtfb2_heroes h
		INNER JOIN wtfb2_villages v ON (v.id=h.inVillage)
		WHERE (v.ownerId='{1}') AND (h.ownerId=0)
	",array($myId)
	)
);
$heroes=array();
$xpFn=$config['experienceFunction'];
while($row=mysql_fetch_assoc($r))
{
	$row['level']=$xpFn($row['offense'])+$xpFn($row['defense'])+1;
	$heroes[]=$row;
}

showInBox('templates/noherotemplate.php',array('heroes'=>$heroes));



?>
