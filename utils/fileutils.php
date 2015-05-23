<?php

function compress($fnfrom,$fnto)
{
	$f=fopen($fnfrom,"rb");
	$fo=gzopen($fnto,'wb9');
	while(!feof($f)) gzwrite($fo,fread($f,1024*512));
	fclose($f);
	gzclose($fo);
}

function downloadFile($fn)
{
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($fn));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fn));
    readfile($fn);
}

?>
