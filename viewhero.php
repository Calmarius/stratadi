<?php

require_once("userworkerphps.php");

if (!isset($_GET['id']))
{
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_heroes WHERE (ownerId='{1}')",array($_SESSION['userId'])));
	$a=mysql_fetch_assoc($r);
	if ($a!==FALSE)
		$_GET['id']=$a['id'];
}

$r=doMySqlQuery(
sqlPrintf(
	"
	SELECT h.*,u.userName AS ownerName,u.id AS ownerId, v.villageName AS villageName,v.x AS villageX,v.y AS villageY
	FROM wtfb2_heroes h
	LEFT JOIN wtfb2_users u ON (h.ownerId=u.id)
	LEFT JOIN wtfb2_villages v ON (v.id=h.inVillage)
	WHERE (h.id='{1}')
	",array($_GET['id'])
)
,'jumpErrorPage');
if (mysql_num_rows($r)==0)
{
	$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_heroes WHERE (ownerId='{1}')",array($_SESSION['userId'])));
	logText(mysql_num_rows($r));
	if (mysql_num_rows($r)==0)
	{
		jumpTo("nohero.php");
	}
	else
	{
		jumpErrorPage($language['heronotexist']);
	}
}
$hero=mysql_fetch_assoc($r);
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
