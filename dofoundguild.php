<?php

require_once('userworkerphps.php');

bounceSessionOver();

$q=sqlvprintf("INSERT INTO wtfb2_guilds (guildName,profile) VALUES ({0},'')",array($_POST['guildname']));
$r=runEscapedQuery($q);
$insertId=getLastInsertId();

$q=sqlvprintf("UPDATE wtfb2_users SET guildId={0} WHERE (id={1})",array($insertId,$_SESSION['userId']));
$r=runEscapedQuery($q);

$valuesText='';
$first=true;
foreach($config['guildPermissions'] as $key=>$value)
{
	if (!$first) $valuesText.=",\n";
	$first=false;
	$valuesText.=sqlvprintf("({0},{1})",array($_SESSION['userId'],$key));
}

$q=sqlvprintf(
    "
        DELETE FROM wtfb2_guildpermissions WHERE userId={0}
    ",
    array($_SESSION['userId'])
);
$r=runEscapedQuery($q);


$q=sqlvprintf(
	"
	INSERT INTO wtfb2_guildpermissions (userId,permission)
		VALUES
		$valuesText
	"
);
$r=runEscapedQuery($q);

runEscapedQuery("INSERT INTO wtfb2_worldevents (eventTime,playerId,type) VALUES (NOW(),{0},'guildchange')",$_SESSION['userId']);

jumpTo('guild.php');




?>
