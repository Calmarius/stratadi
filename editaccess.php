<?php

require_once('userworkerphps.php');

if (!isset($_SESSION['userId'])) jumpErrorPage($language['sessionisover']);

$q=
sqlPrintf(
	"
		SELECT * FROM wtfb2_accesses
		WHERE (id='{1}')
	",array($_SESSION['accessId']));
$r=doMySqlQuery($q,'jumpErrorPage');
$a=mysql_fetch_assoc($r);


$tm=date_parse($a['birth']);
$a['year']=$tm['year'] < 1900 ? '':$tm['year'];
$a['month']=$tm['month'];
$a['day']=$tm['day'] < 1 ? '':$tm['day'];

$q=
	"
		SELECT languageId FROM wtfb2_spokenlanguages WHERE (playerId='${_SESSION['accessId']}')
	";

$r=doMySqlQuery($q);
$langIds=array();
while($row=mysql_fetch_assoc($r)) $langIds[]=$row['languageId'];

$a['languages']=$langIds;

showInBox('templates/accessedit.php',$a);

?>
