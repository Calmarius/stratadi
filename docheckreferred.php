<?php

require_once('userworkerphps.php');


$myId=$_SESSION['userId'];
$referredId=$_GET['id'];

// Check whether the the user is really referred
$r=runEscapedQuery(
	"SELECT * FROM wtfb2_users WHERE (id={0}) AND (refererId={1})",$referredId,$myId
);
if (isEmptyResult($r)) jumpErrorPage($language['playerisnotreferredbyyou']);

// Check whether he reached the village count
$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0}) AND (villageCount>={1})", $referredId,$config['referredRewardVillageCount']);
if (isEmptyResult($r)) jumpErrorPage(xprintf($language['playernotreachedthevillagecount'],array($config['referredRewardVillageCount'])));
// Remove the referred status
runEscapedQuery("UPDATE wtfb2_users SET refererId=0 WHERE (id={0})",$referredId);
// Get the maximum amount of expansion points on the server
$maxE=runEscapedQuery
(
	'
	SELECT MAX(cnt) AS maxExpansion FROM 
	(
		SELECT COUNT(*)+u.expansionPoints AS cnt,u.userName FROM wtfb2_villages v
		INNER JOIN wtfb2_users u ON (u.id=v.ownerId)
		GROUP BY u.id
	) tmp
	'
);
$maxE=$maxE[0][0]['maxExpansion'];
// Get the player's expansion value;
$myE=runEscapedQuery
(
	"
		SELECT COUNT(*)+u.expansionPoints AS cnt FROM wtfb2_villages v
		INNER JOIN wtfb2_users u ON (u.id=v.ownerId)
		WHERE (u.id={0})
		GROUP BY u.id
	",$myId
);
$myE=$myE[0][0];
$maxExpansionCanBeGiven=$maxE-$myE;

// Check whether they use the same internet connection the most
$pointsYouGet=0;
$myRow=runEscapedQuery("SELECT *,MAX(useCount) FROM wtfb2_iplog WHERE (userId={0}) GROUP BY userId",$myId);
$refRow=runEscapedQuery("SELECT *,MAX(useCount) FROM wtfb2_iplog WHERE (userId={0}) GROUP BY userId",$referredId);
$myRow=$myRow[0][0];
$refRow=$refRow[0][0];
if ($myRow['ip']==$refRow['ip'])
{
	if ($maxExpansionCanBeGiven<$pointsYouGet) $pointsYouGet=$maxExpansionCanBeGiven;
	runEscapedQuery("UPDATE wtfb2_users SET expansionPoints=expansionPoints+{0} WHERE (id={1})",$pointsYouGet,$myId);
	jumpSuccessPage($language['playerreachedthelevel'],xprintf($language['yougotexpansionpoints'],array($pointsYouGet)));
}
// Check whether others use the referrers most used internet access the most.
$pointsYouGet=1;

	// we know the referred users most used ip address $refRow
	// which users are using the same ip address the most
$r=runEscapedQuery
(
	"SELECT *,MAX(useCount) FROM wtfb2_iplog WHERE (ip={0})  AND (TIMESTAMPDIFF(SECOND,{1},NOW())<86400*7) GROUP BY userId",$refRow['ip'],$refRow['lastUsed']
);
if (count($r[0])>1)
{
	// if more users using it then we are ready
	if ($maxExpansionCanBeGiven<$pointsYouGet) $pointsYouGet=$maxExpansionCanBeGiven;
	runEscapedQuery("UPDATE wtfb2_users SET expansionPoints=expansionPoints+{0} WHERE (id={1})",$pointsYouGet,$myId);
	jumpSuccessPage($language['playerreachedthelevel'],xprintf($language['yougotexpansionpoints'],array($pointsYouGet)));
}
// anybody ever logged in from the referred's ip address except him?
$r=runEscapedQuery("SELECT * FROM wtfb2_iplog WHERE (ip={0}) AND (userId<>{1})",$refRow['ip'],$refRow['userId']);
if (!isEmptyResult($r))
{
	$pointsYouGet=2;
}
else
{
	$pointsYouGet=5;
}
if ($maxExpansionCanBeGiven<$pointsYouGet) $pointsYouGet=$maxExpansionCanBeGiven;
runEscapedQuery("UPDATE wtfb2_users SET expansionPoints=expansionPoints+{0} WHERE (id={1})",$pointsYouGet,$myId);
jumpSuccessPage($language['playerreachedthelevel'],xprintf($language['yougotexpansionpoints'],array($pointsYouGet)));

?>
