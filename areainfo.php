<?php

/*require_once('setupsession.php');
require_once('setupmysql.php');*/
require_once('userworkerphps.php');
//mysql_set_charset("utf8");

$bLevelNames=array();
foreach($config['buildings'] as $key=>$value)
{
	$bLevelNames[]=$value['buildingLevelDbName'];
}

$scoreQuery=$config['villageScoreFunction']($bLevelNames);


$q=sqlPrintf(
	"
		SELECT wtfb2_villages.*,UNIX_TIMESTAMP(wtfb2_villages.lastUpdate) AS updateTimestamp,userName,guildName,wtfb2_users.id AS userId,wtfb2_guilds.id AS guildId, $scoreQuery AS score
		FROM wtfb2_villages LEFT JOIN wtfb2_users ON (wtfb2_villages.ownerId=wtfb2_users.id) LEFT JOIN wtfb2_guilds ON (wtfb2_users.guildId=wtfb2_guilds.id)
		WHERE (x>='{1}') AND (x<='{2}') AND (y>='{3}') AND (y<='{4}')
	"
	,array($_GET['left'],$_GET['right'],$_GET['top'],$_GET['bottom'])
);
$r=runEscapedQuery($q);



header('Content-type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<villages>';
$placeholdersOnly=isset($_GET['placeholdersonly']);
foreach ($r[0] as $row)
{
	
	echo '<village>';
	if ($placeholdersOnly)
	{
		echo '<id>'.htmlspecialchars($row['id'],ENT_COMPAT,"utf-8").'</id>';
		echo '<x>'.htmlspecialchars($row['x'],ENT_COMPAT,"utf-8").'</x>';
		echo '<y>'.htmlspecialchars($row['y'],ENT_COMPAT,"utf-8").'</y>';		
		echo '<guildId>'.htmlspecialchars($row['guildId'],ENT_COMPAT,"utf-8").'</guildId>';
		echo '<userId>'.htmlspecialchars($row['ownerId'],ENT_COMPAT,"utf-8").'</userId>';
		echo '<placeholder/>';
	}
	else
	{
		if ($row['ownerId']==$_SESSION['userId'])
		{
			foreach($row as $key=>$value)
			{
				if ((is_numeric($value)) && (abs($value)<1e-6)) $value=0;
				echo "<$key>".htmlspecialchars($value,ENT_COMPAT,"utf-8")."</$key>";
			}
		}
		else
		{
			echo '<id>'.htmlspecialchars($row['id'],ENT_COMPAT,"utf-8").'</id>';
			echo '<villageName>'.htmlspecialchars($row['villageName'],ENT_COMPAT,"utf-8").'</villageName>';
			echo '<userId>'.htmlspecialchars($row['ownerId'],ENT_COMPAT,"utf-8").'</userId>';
			echo '<userName>'.htmlspecialchars($row['userName'],ENT_COMPAT,"utf-8").'</userName>';
			echo '<guildId>'.htmlspecialchars($row['guildId'],ENT_COMPAT,"utf-8").'</guildId>';
			echo '<guildName>'.htmlspecialchars($row['guildName'],ENT_COMPAT,"utf-8").'</guildName>';
			echo '<x>'.htmlspecialchars($row['x'],ENT_COMPAT,"utf-8").'</x>';
			echo '<y>'.htmlspecialchars($row['y'],ENT_COMPAT,"utf-8").'</y>';
			echo '<score>'.htmlspecialchars($row['score'],ENT_COMPAT,"utf-8").'</score>';
		}
	}
	echo '</village>';
}
echo '</villages>';



?>
