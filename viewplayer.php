<?php

require_once("userworkerphps.php");
//bounceSessionOver();

// TODO: (refactoring) Add fetch player by id function.
if (isset($_SESSION['userId']))
{
    $myId=$_SESSION['userId'];
    $r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$myId);
    $me=$r[0][0];
}
else
{
    $me['regDate'] = date('Y-m-d H:i:s');
    $myId = -1;
}

if (!isset($_GET['id'])) $_GET['id']=$myId;

$q=sqlvprintf(<<< X
	SELECT u.*,
		g.guildName AS guildName, h.id AS heroId,
		h.name AS heroName,
		TIMESTAMPDIFF(SECOND,{1},NOW())/TIMESTAMPDIFF(SECOND,regDate,NOW()) AS ageBonus
	FROM wtfb2_users u
	LEFT JOIN wtfb2_guilds g ON (u.guildId=g.id)
	LEFT JOIN wtfb2_heroes h ON (h.ownerId=u.id)
	WHERE (u.id={0})
	GROUP BY u.id
X
,array($_GET['id'],$me['regDate']));
$r=runEscapedQuery($q);
if (isEmptyResult($r)) jumpErrorPage($language['']);
$a=$r[0][0];
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

$r=runEscapedQuery("SELECT *,(id={1}) AS isMaster FROM wtfb2_accesses WHERE (accountId={0})",$_GET['id'],$a['masterAccess']);
$kings=array();
foreach ($r[0] as $row)
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
