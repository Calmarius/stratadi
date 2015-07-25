<?php

require_once("userworkerphps.php");

if (!isset($_GET['id']))
{
    bounceSessionOver();
    $_GET['id'] = 0;
	$r=runEscapedQuery("SELECT * FROM wtfb2_heroes WHERE (ownerId={0})",$_SESSION['userId']);
	$a=$r[0][0];
	if ($a!==FALSE)
		$_GET['id']=$a['id'];
}

$r=runEscapedQuery(
	"
	SELECT h.*,u.userName AS ownerName,u.id AS ownerId, v.villageName AS villageName,v.x AS villageX,v.y AS villageY
	FROM wtfb2_heroes h
	LEFT JOIN wtfb2_users u ON (h.ownerId=u.id)
	LEFT JOIN wtfb2_villages v ON (v.id=h.inVillage)
	WHERE (h.id={0})
	",$_GET['id']
);
if (isEmptyResult($r))
{
	$r=runEscapedQuery("SELECT * FROM wtfb2_heroes WHERE (ownerId={0})",$_SESSION['userId']);
	logText(count($r));
	if (isEmptyResult($r))
	{
		jumpTo("nohero.php");
	}
	else
	{
		jumpErrorPage($language['heronotexist']);
	}
}
$hero=$r[0][0];
$ownHero=isset($_SESSION['userId']) && ($hero['ownerId']==$_SESSION['userId']);

$xpToLevel=$config['experienceFunction'];
$levelToXp=$config['experienceFunctionInverse'];
$hero['attackskill']=$xpToLevel($hero['offense']);
$hero['defendskill']=$xpToLevel($hero['defense']);
$hero['attacknextxp']=(int)$levelToXp($hero['attackskill']+1)-$hero['offense'];
$hero['defendnextxp']=(int)$levelToXp($hero['defendskill']+1)-$hero['defense'];
$hero['level']=$hero['attackskill']+$hero['defendskill']+1;
if (!$ownHero)
{
	$hero['offense']=$language['na'];
	$hero['defense']=$language['na'];
	$hero['attackskill']=$language['na'];
	$hero['defendskill']=$language['na'];
	$hero['attacknextxp']=$language['na'];
	$hero['defendnextxp']=$language['na'];
}

showInBox('templates/viewherotemplate.php',array('hero'=>$hero));

?>
