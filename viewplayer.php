<?php

require_once("userworkerphps.php");
//bounceSessionOver();

$myId=$_SESSION['userId'];
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($myId)));
$me=mysql_fetch_assoc($r);

if (!isset($_GET['id'])) $_GET['id']=$myId;

$q=sqlPrintf(<<< X
	SELECT u.*,
		g.guildName AS guildName, h.id AS heroId,
		h.name AS heroName,
		TIMESTAMPDIFF(SECOND,'{2}',NOW())/TIMESTAMPDIFF(SECOND,regDate,NOW()) AS ageBonus
	FROM wtfb2_users u
	LEFT JOIN wtfb2_guilds g ON (u.guildId=g.id)
	LEFT JOIN wtfb2_heroes h ON (h.ownerId=u.id)
	WHERE (u.id='{1}')
	GROUP BY u.id
X
,array($_GET['id'],$me['regDate']));
$r=doMySqlQuery($q,'jumpErrorPage');
if (mysql_num_rows($r)==0) jumpErrorPage($language['']);
$a=mysql_fetch_assoc($r);
$a['own']=$myId==$a['id'];

if ((double)$a['ageBonus']<1) $a['ageBonus']=1;

$r=doMySqlQuery(sqlPrintf("SELECT *,(id='{2}') AS isMaster FROM wtfb2_accesses WHERE (accountId='{1}')",array($_GET['id'],$_a['masterAccess'])));
$kings=array();
while($row=mysql_fetch_assoc($r))
{
	$kings[]=$row;
}

$a['kings']=$kings;


showInBox('templates/profiletemplate.php',$a);


?>
