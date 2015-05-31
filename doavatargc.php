<?php

require_once('userworkerphps.php');
bounceNoAdmin();

$dir=opendir('avatars');
$files=array();
while(false !== ($file=readdir($dir)))
{
	if (($file=='.') || ($file=='..')) continue;
	$files["avatars/$file"]=true;
}

$avatarLinks=runEscapedQuery(
	"
		(SELECT avatarLink FROM wtfb2_users WHERE (avatarLink IS NOT NULL) AND (avatarLink<>''))
		UNION
		(SELECT avatarLink FROM wtfb2_heroes WHERE (avatarLink IS NOT NULL) AND (avatarLink<>''))	
	"
);

foreach ($avatarLinks[0] as $avatarLink)
{
	unset($files[$avatarLink['avatarLink']]);
}

foreach($files as $key=>$value)
{
	unlink($key); //risky
}

jumpSuccessPage(count($files).' files deleted!','');

?>
