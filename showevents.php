<?php

require_once("userworkerphps.php");
bounceSessionOver();

$r=runEscapedQuery("SELECT * FROM wtfb2_heroes WHERE (ownerId={0})",$_SESSION['userId']);
$heroId=-1;
if (!isEmptyResult($r))
{
	$a=$r[0][0];
	$heroId=$a['id'];
}

$direction=$_GET['category'];
$type=$_GET['type'];
$dstring='';
if ($direction=='incoming') $dstring='v2.ownerId=u.id';
else if ($direction=='outgoing')
{
	if ($type=='return')
		$dstring='(h.ownerId=u.id) AND (e.heroId='.$heroId.')';
	else
		$dstring='(v.ownerId=u.id) OR (e.heroId='.$heroId.')';
}
else die();

//if (($type=='return') && ($direction=='outgoing')) jumpErrorPage($language['accessdenied']);

if (!isset($_GET['p'])) $_GET['p']=0;

$q=
sqlvprintf(
"
	SELECT SQL_CALC_FOUND_ROWS e.*,
		IF((e.eventType IN ('attack','raid','recon')) AND (v2.ownerId=u.id),'incomingattack',e.eventType) AS eventType,
		IF((e.eventType IN ('attack','raid','recon')) AND (v2.ownerId=u.id),'incomingattack',e.eventType) AS type,
		TIMESTAMPDIFF(SECOND,NOW(),e.estimatedTime) AS happensIn,
		v.villageName AS source,
		v2.villageName destination,
		v.x AS srcX,
		v.y AS srcY,
		v2.x AS dstX,
		v2.y AS dstY,
		h.name AS heroName,
		(((v.ownerId={0}) OR (e.heroId={3})) AND (e.eventType<>'return')) AS cancellable
	FROM wtfb2_events e
	LEFT JOIN wtfb2_villages v ON (v.id=e.launcherVillage) 
	LEFT JOIN wtfb2_villages v2 ON (v2.id=e.destinationVillage) 
	LEFT JOIN wtfb2_heroes h ON (h.id=e.heroId)
	LEFT JOIN wtfb2_users u ON ($dstring)
	WHERE (u.id={1})
	HAVING (type={2})
	ORDER BY e.estimatedTime
	LIMIT {4},{5}
",array($_SESSION['userId'],$_SESSION['userId'],$type,$heroId,(int)$_GET['p']*$config['pageSize'],(int)$config['pageSize'])
);

$r=runEscapedQuery($q,'jumpErrorPage'); // hackish a bit
$events=array();
foreach ($r[0] as $row)
{
		$type=$row['eventType'];
		if ($type=='incomingattack')
		{
			foreach($config['units'] as $key=>$value)
			{
				$row[$value['countDbName']]='?';
			}
			$row['catapultTarget']='';
		}
	$events[]=$row;
}

$r=runEscapedQuery("SELECT FOUND_ROWS() AS allRows");
$a=$r[0][0];
$cnt=ceil($a['allRows']/$config['pageSize']);

$content=new Template('templates/eventstemplate.php',array('events'=>$events,'pages'=>$cnt,'type'=>$_GET['type'],'category'=>$_GET['category']));
$box=new Template('templates/standardboxtemplate.php',array('content'=>$content->getContents()));
$page=new Template('templates/basiclayout.php',array('title'=>'WTFBattles II','content'=>$box->getContents(),'scripts'=>array('timer.js'),'loadScript'=>'initializeTimers()'));
$page->render();


?>
