<?php

exec('uptime');
die();

$zip=new ZipArchive();
$zip->open('test.zip',ZIPARCHIVE::CREATE);


function zipEnumerator($path,$file,$tarfilename)
{
	zipEnumeratorRec('',$path,$file,$tarfilename);
}

function zipEnumeratorRec($subpath,$root,$zip,$zipfilename)
{
	$dir=opendir($root.$subpath);
	while(false !== ($file=readdir($dir)))
	{
		if ($file==".") continue;
		if ($file=="..") continue;
		if ($file==$zipfilename) continue; // skip the file we created
		if (filetype($root.$subpath."/".$file)=="dir")
		{
			$zip->addEmptyDir(ltrim($subpath."/".$file."/","/"));
			zipEnumeratorRec($subpath."/".$file,$root,$zip,$tarfilename);
			continue;
		}
		$str=$subpath."/".$file;
		$zip->addFile($root.$str,ltrim($str,'/'));
	}
}

function createZip($StartDir,$Fn)
{
	$zip=new ZipArchive();
	$zip->open($Fn,ZIPARCHIVE::CREATE);
	zipEnumerator($StartDir,$zip,$Fn);
	$zip->close();
}

createZip('.','test.zip');
echo "IT'S OKAY!";

?>
