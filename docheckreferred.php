<?php

require_once('userworkerphps.php');


$myId=$_SESSION['userId'];
$referredId=$_GET['id'];

// Check whether the the user is really referred
$r=doMySqlQuery(
	sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}') AND (refererId='{2}')",array($referredId,$myId))
	);
if (mysql_num_rows($r)==0) jumpErrorPage($language['playerisnotreferredbyyou']);
// Check whether he reached the village count
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_users WHERE (id='{1}') AND (villageCount>='{2}')",array($referredId,$config['referredRewardVillageCount'])));
if (mysql_num_rows($r)==0) jumpErrorPage(xprintf($language['playernotreachedthevillagecount'],array($config['referredRewardVillageCount'])));
// Remove the referred status
doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET refererId=0 WHERE (id='{1}')",array($referredId)));
// Get the maximum amount of expansion points on the server
$r=doMySqlQuery
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
$a=mysql_fetch_assoc($r);
$maxE=$a['maxExpansion'];
// Get the player's expansion value;
$r=doMySqlQuery
(
	sqlPrintf(
	"
		SELECT COUNT(*)+u.expansionPoints AS cnt FROM wtfb2_villages v
		INNER JOIN wtfb2_users u ON (u.id=v.ownerId)
		WHERE (u.id='{1}')
		GROUP BY u.id
	",array($myId)
	)
);
$a=mysql_fetch_assoc($r);
$myE=$a['cnt'];
$maxExpansionCanBeGiven=$maxE-$myE;
//die($maxExpansionCanBeGiven);



// Check whether they use the same internet connection the most
$pointsYouGet=0;
$r=doMySqlQuery(sqlPrintf("SELECT *,MAX(useCount) FROM wtfb2_iplog WHERE (userId='{1}') GROUP BY userId",array($myId)),'jumpErrorPage');
$s=doMySqlQuery(sqlPrintf("SELECT *,MAX(useCount) FROM wtfb2_iplog WHERE (userId='{1}') GROUP BY userId",array($referredId)),'jumpErrorPage');
$myRow=mysql_fetch_assoc($r);
$refRow=mysql_fetch_assoc($s);
if ($myRow['ip']==$refRow['ip'])
{
	if ($maxExpansionCanBeGiven<$pointsYouGet) $pointsYouGet=$maxExpansionCanBeGiven;
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=expansionPoints+{1} WHERE (id={2})",array($pointsYouGet,$myId)));
	jumpSuccessPage($language['playerreachedthelevel'],xprintf($language['yougotexpansionpoints'],array($pointsYouGet)));
}
// Check whethet others use the referrers most used internet access the most.
$pointsYouGet=1;

	// we know the referred users most used ip address $refRow
	// which users are using the same ip address the most
$r=doMySqlQuery
(
	sqlPrintf("SELECT *,MAX(useCount) FROM wtfb2_iplog WHERE (ip='{1}')  AND (TIMESTAMPDIFF(SECOND,'{2}',NOW())<86400*7) GROUP BY userId",array($refRow['ip'],$refRow['lastUsed'])),'jumpErrorPage'
);
if (mysql_num_rows($r)>1)
{
	// if more users using it then we are ready
	if ($maxExpansionCanBeGiven<$pointsYouGet) $pointsYouGet=$maxExpansionCanBeGiven;
	doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=expansionPoints+{1} WHERE (id='{2}')",array($pointsYouGet,$myId)));
	jumpSuccessPage($language['playerreachedthelevel'],xprintf($language['yougotexpansionpoints'],array($pointsYouGet)));
}
// anybody ever logged in from the referred's ip address except him?
$r=doMySqlQuery(sqlPrintf("SELECT * FROM wtfb2_iplog WHERE (ip='{1}') AND (userId<>'{2}')",array($refRow['ip'],$refRow['userId'])));
if (mysql_num_rows($r)>0)
{
	$pointsYouGet=2;
}
else
{
	$pointsYouGet=5;
}
if ($maxExpansionCanBeGiven<$pointsYouGet) $pointsYouGet=$maxExpansionCanBeGiven;
doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET expansionPoints=expansionPoints+{1} WHERE (id={2})",array($pointsYouGet,$myId)));
jumpSuccessPage($language['playerreachedthelevel'],xprintf($language['yougotexpansionpoints'],array($pointsYouGet)));





//jumpSuccessPage('FOO','BAR');


?>
