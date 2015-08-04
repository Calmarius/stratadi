<?php

require_once('userworkerphps.php');

if (!isset($_SESSION['userId'])) jumpErrorPage($language['sessionisover']);

$q=
sqlvprintf(
	"
		SELECT * FROM wtfb2_accesses
		WHERE (id={0})
	",array($_SESSION['accessId']));
$r=runEscapedQuery($q,'jumpErrorPage');
$a=$r[0][0];


$tm=date_parse($a['birth']);
$a['year']=$tm['year'] < 1900 ? '':$tm['year'];
$a['month']=$tm['month'];
$a['day']=$tm['day'] < 1 ? '':$tm['day'];

$q=
	"
		SELECT languageId FROM wtfb2_spokenlanguages WHERE (playerId='${_SESSION['accessId']}')
	";

$r=runEscapedQuery($q);
$langIds=array();
foreach ($r[0] as $row) $langIds[]=$row['languageId'];

$a['languages']=$langIds;

showInBox('templates/accessedit.php',$a);

?>
