<?php

require_once('userworkerphps.php');
bounceNoAdmin();

$r=doMySqlQuery("SELECT * FROM wtfb2_users");

while($row=mysql_fetch_assoc($r))
{
	doMySqlQuery("UPDATE wtfb2_accesses SET city='".$row['city']."', birth='".$row['birth']."', gender='".$row['gender']."' WHERE (accountId=".$row['id'].")");
}


echo "STUFF OK";

/*$r=doMySqlQuery("SELECT id FROM wtfb2_users");
$values=array();
while($row=mysql_fetch_assoc($r))
{
	$values[]="(${row['id']},126)";
}
doMySqlQuery("INSERT INTO wtfb2_spokenlanguages (playerId,languageId) VALUES ".implode(',',$values));

echo 'STUFF OK!';*/




/*require_once("presenterphps.php");
require_once("setupmysql.php");
require_once("utils/gameutils.php");
require_once("villageupdater.php");

$r=doQuery("SELECT * FROM wtfb2_users");
while($row=mysql_fetch_assoc($r))
{
	updateAllVillages($row['id']);
	updatePlayer($row['id']);
}

die('WELL DONE!');*/


?>
