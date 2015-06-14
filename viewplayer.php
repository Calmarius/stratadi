<?php

require_once("userworkerphps.php");
//bounceSessionOver();

// TODO: Add fetch player by id function.
if (isset($_SESSION['userId']))
{
    $myId=$_SESSION['userId'];
    $r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}')",array($myId)));
    $me=mysql_fetch_assoc($r);
}
else
{
    $me['regDate'] = date('Y-m-d H:i:s');
    $myId = -1;
}

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

if ($a['ageBonus'] != 0)
{
    $a['revAgeBonus'] = 1/(float)$a['ageBonus'];
}
else
{
    $a['revAgeBonus'] = 1;
}
if ((double)$a['ageBonus']<1) $a['ageBonus']=1;
if ((double)$a['revAgeBonus']<1) $a['revAgeBonus']=1;

$r=doMySqlQuery(sqlPrintf("SELECT *,(id='{2}') AS isMaster FROM wtfb2_accesses WHERE (accountId='{1}')",array($_GET['id'],$a['masterAccess'])));
$kings=array();
while($row=mysql_fetch_assoc($r))
{
	$kings[]=$row;
}

$a['kings']=$kings;

$r = runEscapedQuery("SELECT x, y, villageName FROM wtfb2_villages WHERE (ownerId = {0})", $_GET['id']);
$villages = array();
foreach ($r[0] as $vill)
{
    $villages[] = $vill;
}
$a['villages'] = $villages;


showInBox('templates/profiletemplate.php',$a);


?>
