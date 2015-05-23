<?php

require_once('userworkerphps.php');

bounceSessionOver();

$q=sqlPrintf("INSERT INTO wtfb2_guilds (guildName,profile) VALUES ('{1}','')",array($_POST['guildname']));
$r=doMySqlQuery($q,'jumpErrorPage'); 
$insertId=mysql_insert_id();

$q=sqlPrintf("UPDATE wtfb2_users SET guildId='{1}' WHERE (id='{2}')",array($insertId,$_SESSION['userId']));
$r=doMySqlQuery($q,jumpErrorPage);

$valuesText='';
$first=true;
foreach($config['guildPermissions'] as $key=>$value)
{
	if (!$first) $valuesText.=",\n";
	$first=false;
	$valuesText.=sqlPrintf("('{1}','{2}')",array($_SESSION['userId'],$key));
}

$q=sqlPrintf(
    "
        DELETE FROM wtfb2_guildpermissions WHERE userId='{1}'
    ",
    $_SESSION['userId']
);
$r=doMySqlQuery($q,'jumpErrorPage');


$q=sqlPrintf(
	"
	INSERT INTO wtfb2_guildpermissions (userId,permission) 
		VALUES
		$valuesText
	"
);
$r=doMySqlQuery($q,'jumpErrorPage');

doMySqlQuery(sqlPrintf("INSERT INTO wtfb2_worldevents (eventTime,playerId,type) VALUES (NOW(),'{1}','guildchange')",array($_SESSION['userId'])));		

jumpTo('guild.php');




?>
