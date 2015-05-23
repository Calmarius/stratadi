<?php

require_once('userworkerphps.php');

if (!isset($_SESSION['userId'])) jumpErrorPage($language['sessionisover']);

$q=
sqlPrintf(
	"
		SELECT wtfb2_users.*,wtfb2_heroes.avatarLink AS heroAvatar,wtfb2_heroes.name AS heroName,wtfb2_heroes.id AS heroId
		FROM wtfb2_users
		LEFT JOIN wtfb2_heroes ON (wtfb2_heroes.ownerId=wtfb2_users.id )
		WHERE (wtfb2_users.id='{1}')
	",array($_SESSION['userId']));
$r=doMySqlQuery($q,'jumpErrorPage');
$a=mysql_fetch_assoc($r);

if ($a['heroId']=='')
{
	$a['nohero']=true;
}


showInBox('templates/profileedit.php',$a);

?>
