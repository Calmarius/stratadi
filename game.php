<?php


require_once("userworkerphps.php");
require_once("villageupdater.php");

$asGuest=(int)isset($_GET['guest']);

if (!$asGuest)
{
	if ($_SESSION['permission']=='inactive') jumpTo('activate.php');
	if (!isset($_SESSION['userId'])) jumpTo('login.php');
}

$deputized=false;
if (!isset($_SESSION['asdeputy']))
{
	$r=runEscapedQuery("SELECT d.*,u.userName FROM wtfb2_deputies d JOIN wtfb2_users u ON (d.deputyId=u.id) WHERE (sponsorId={0})",$_SESSION['userId']);
	$deputized= !isEmptyResult($r);
}
if ($deputized)
{
	$deps=array();
	foreach ($r[0] as $row)
	{
		$deps[]=$row['userName'];
	}
	$gameViewTemplate=new Template('templates/finishsittingtemplate.php',array('deputy'=>implode(',',$deps)));
}
else
{
	$r=runEscapedQuery("SELECT id,x,y FROM wtfb2_villages WHERE (ownerId={0})",$_SESSION['userId']);
	$villages=array();
	foreach ($r[0] as $row)
	{
		$villages[$row['id']]=$row;
	}

	if(!isset($_GET['x']))
	{
		if ($asGuest)
		{
			$_GET['x']=0;
			$_GET['y']=0;
		}
		else
		{
			$first=reset($villages);
			$_GET['x']=@$first['x'];
			$_GET['y']=@$first['y'];
		}
	}

	$r=runEscapedQuery("SELECT * FROM wtfb2_users WHERE (id={0})",$_SESSION['userId']);
	$player=array();
	if (!isEmptyResult($r)) $player=$r[0][0];
	$r=runEscapedQuery("SELECT * FROM wtfb2_accesses WHERE (id={0})",$_SESSION['accessId']);
	$access=array();
	if (!isEmptyResult($r)) $access=$r[0][0];

	$gameViewTemplate;
	$needsTutorial=false;
	$enteredGame=0;
	if ($player['willDeleteAt']!==null)
	{
		$gameViewTemplate=new Template('templates/canceldeletiontemplate.php',array('deleteTime'=>$player['willDeleteAt']));
		$enteredGame=false;		
	}
	else if ((count($villages)==0) && !$asGuest)
	{
		$gameViewTemplate=new Template('templates/createnewvillage.php',array());
		$enteredGame=false;
	}
	else
	{
		$isAdmin=isset($access['permission']) && ($access['permission']=='admin') && (!isset($_SESSION['asdeputy']));
		$needsTutorial=isset($player['needsTutorial']) && ($player['needsTutorial']!=0);
		$gameViewTemplate=new Template('templates/gameview.php',array('admin'=>$isAdmin,'tutorial'=>$needsTutorial,'villageInfo'=>$villages,'guest'=>$asGuest,
			'slownet'=>(int)isset($_GET['slownet']),'tilesize'=>@(int)$_GET['tilesize'],'nomerge'=>(int)isset($_GET['notilemerge']),'cellCount'=>(isset($_GET['cellcount']) ? (int)$_GET['cellcount'] : 200)));
		$enteredGame=true;
	}
}

$content=$gameViewTemplate->getContents();
$page=new Template('templates/basiclayout.php',array('title'=>'WTFBattles II','content'=>$content,'scripts'=>(@$enteredGame ? array('timer.js','map.js.php','ajax.js','debug.js'):''),'loadScript'=>'initMap('.(int)@$_GET['x'].','.(int)@$_GET['y'].'); getPlayerInfo(); loadAllVillages(); '.(@$needsTutorial ? 'openInWindow(\'tutorial.php\')':'')));
$page->render();

?>
