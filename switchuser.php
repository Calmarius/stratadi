<?php

require_once("userworkerphps.php");
bounceNoAdmin();

$orderByText='';
$orderBy=str_replace("`","",$_GET['orderby']);
if (isset($_GET['orderby']))
{
	$orderByText=sqlPrintf("ORDER BY {1}",array($orderBy));
}
if (isset($_GET['desc']))
{
	$orderByText.=" DESC";
}

if (isset($_GET['simple']))
	$r=doMySqlQuery("SELECT id,userName FROM wtfb2_users $orderByText ORDER BY userName");
else
	$r=doMySqlQuery("SELECT * FROM wtfb2_users $orderByText");
$userInfos=array();
while($row=mysql_fetch_assoc($r))
{
	$userInfos[]=$row;
}

showInBox('templates/useradmintemplate.php',array('users'=>$userInfos));

?>
