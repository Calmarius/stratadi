<?php

require_once("userworkerphps.php");
bounceNoAdmin();

$orderByText='';
$orderBy=str_replace("`","",@$_GET['orderby']);
if (isset($_GET['orderby']))
{
	$orderByText=sqlvprintf("ORDER BY {0}", array($orderBy));
}
if (isset($_GET['desc']))
{
	$orderByText.=" DESC";
}

if (isset($_GET['simple']))
	$r=runEscapedQuery("SELECT id,userName FROM wtfb2_users $orderByText ORDER BY userName");
else
	$r=runEscapedQuery("SELECT * FROM wtfb2_users $orderByText");
$userInfos=array();
foreach ($r[0] as $row)
{
	$userInfos[]=$row;
}

showInBox('templates/useradmintemplate.php',array('users'=>$userInfos));

?>
