<?php

require_once('userworkerphps.php');

if (!isset($_GET['id']))
{
	$_GET['id']=$_SESSION['accessId'];
}
$r=runEscapedQuery(
	"
		SELECT a.*,TIMESTAMPDIFF(YEAR,birth,NOW()) AS age, GROUP_CONCAT(l.language ORDER BY l.language SEPARATOR ', ' ) AS languages
		FROM wtfb2_accesses a
		LEFT JOIN wtfb2_spokenlanguages s ON (s.playerId=a.id)
		LEFT JOIN wtfb2_languages l ON (l.id=s.languageId)
		WHERE (a.id={0})
		GROUP BY a.id
	", $_GET['id']);
$a=$r[0][0];
$a['own']=$_GET['id']==@$_SESSION['accessId'];

showInBox('templates/accesstemplate.php',$a);

?>
