<?php

require_once('utils/tarballs.php');
require_once('utils/fileutils.php');
require_once('userworkerphps.php');
bounceNoAdmin();

define('CRITICAL_TIME',9);

$t=time();

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
			zipEnumeratorRec($subpath."/".$file,$root,$zip,$zipfilename);
			continue;
		}
		$str=$subpath."/".$file;
		$zip->addFile($root.$str,ltrim($str,'/'));
	}
}

function createZip($StartDir,$Fn)
{
	$zip=new ZipArchive();
	$zip->open($Fn,ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
	zipEnumerator($StartDir,$zip,$Fn);
	$zip->close();
}

if (!isset($_SESSION['makingbackup']))
	$_SESSION['makingbackup']=array('step'=>1);

$myId=$_SESSION['userId'];

$r=runEscapedQuery("SELECT NOW() AS now");
$a=$r[0][0];
$f=fopen("nowstamp.ts",'w+t');
fwrite($f,$a['now']);
fclose($f);


// create sql dumps
if ($_SESSION['makingbackup']['step']<=1)
{
	$r=runEscapedQuery("SHOW TABLES LIKE '%wtfb2_%'");
	$tnames=array();
	foreach ($r[0] as $row)
	{
		// first the table creation statement
		foreach ($row as $value)
		{
    		$tableName=$value;
		}
		$tnames[]=$tableName;
	}
	$_SESSION['makingbackup']['tnames']=$tnames;
	foreach($tnames as $key=>$tableName)
	{
		// first the table creation statement
		$f=fopen($tableName.'.sql','w+t');
		$s=runEscapedQuery("SHOW CREATE TABLE $tableName");
		$crTable=$s[0][0];
		fwrite($f,$crTable['Create Table']);
		// the select everything
		$s=runEscapedQuery("SELECT * FROM $tableName");
		if (isEmptyResult($s)) continue; // no rows then continue
		// fetch the first row
		$row2=$s[0][0];
		$columns=array_keys($row2);
		foreach($columns as $key=>$value)
		{
			$columns[$key]="`$value`";
		}
	
		fwrite($f,";\n\n\n");
		fwrite($f,"INSERT INTO `$tableName` (".implode(',',$columns).") VALUES\n");
		// then select and process all rows
		$first=true;
		foreach ($s[0] as $row2)
		{
			if (!$first) fwrite($f,",\n");
			$first=false;
			foreach($row2 as $key=>$value)
			{
			    $row2[$key] = sqlvprintf("{0}", array($value));
			}
			fwrite($f,"(".implode(',',$row2).")");
		}
		fwrite($f,";\n\n\n");
		fclose($f);
	}
	$_SESSION['makingbackup']['step']=2;
	echo 'First step complete: <a href="domakebackup.php?rnd='.mt_rand().'">Click here to continue</a>';
	die();
//	jumpTo('domakebackup.php?rnd='.mt_rand());
}
if ($_SESSION['makingbackup']['step']<=2)
{
	// the current directiory
	$fn=date('Y-m-d.His').'.zip';
	createZip('.',$fn);
	$_SESSION['makingbackup']['step']=3;
	$_SESSION['makingbackup']['filename']=$fn;
	echo 'Second step complete: <a href="domakebackup.php?rnd='.mt_rand().'">Click here to continue</a>';
	die();
	jumpTo('domakebackup.php?rnd='.mt_rand());
}
downloadFile($_SESSION['makingbackup']['filename']);
unlink($_SESSION['makingbackup']['filename']);
//unlink($gzFn);
foreach($_SESSION['makingbackup']['tnames'] as $key=>$value)
{
	unlink($value.'.sql');
}
unset($_SESSION['makingbackup']);

?>
