<?php

require_once('userworkerphps.php');
bounceNoAdmin();

//header('Content-Type: text/plain');

$dir=opendir('avatars');
$files=array();
while(false !== ($file=readdir($dir)))
{
	if (($file=='.') || ($file=='..')) continue;
	$files["avatars/$file"]=true;
}

//echo "<h1>Count of files: ".count($files)."</h1>\n";

$r=doMySqlQuery(
	"
		(SELECT avatarLink FROM wtfb2_users WHERE (avatarLink IS NOT NULL) AND (avatarLink<>''))
		UNION
		(SELECT avatarLink FROM wtfb2_heroes WHERE (avatarLink IS NOT NULL) AND (avatarLink<>''))	
	"
);

while($row=mysql_fetch_assoc($r))
{
	unset($files[$row['avatarLink']]);
}

foreach($files as $key=>$value)
{
//	echo '<img src="'.$key.'">';
	unlink($key); //risky
}

jumpSuccessPage(count($files).' files deleted!','');

//echo "<h1>Count of files that should be deleted: ".count($files)."</h1>";



?>
