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
	$zip->open($Fn,ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
	zipEnumerator($StartDir,$zip,$Fn);
	$zip->close();
}

if (!isset($_SESSION['makingbackup']))
	$_SESSION['makingbackup']=array('step'=>1);

$myId=$_SESSION['userId'];

$r=doMySqlQuery("SELECT NOW() AS now");
$a=mysql_fetch_assoc($r);
$f=fopen("nowstamp.ts",'w+t');
fwrite($f,$a['now']);
fclose($f);


// create sql dumps
if ($_SESSION['makingbackup']['step']<=1)
{
	$r=doMySqlQuery("SHOW TABLES LIKE '%wtfb2_%'");
	$tnames=array();
	while($row=mysql_fetch_array($r))
	{
		// first the table creation statement
		$tableName=$row[0];
		$tnames[]=$tableName;
	}
	$_SESSION['makingbackup']['tnames']=$tnames;
	foreach($tnames as $key=>$tableName)
	{
		// first the table creation statement
		$f=fopen($tableName.'.sql','w+t');
		$s=doMySqlQuery(sqlPrintf("SHOW CREATE TABLE {1}",array($tableName)));
		$crTable=mysql_fetch_array($s);
		fwrite($f,$crTable[1]);
		// the select everything
		$s=doMySqlQuery(sqlPrintf("SELECT * FROM {1}",array($tableName)));
		if (mysql_num_rows($s)==0) continue; // no rows then continue
		// fetch the first row
		$row2=mysql_fetch_assoc($s);
		$columns=array_keys($row2);
		foreach($columns as $key=>$value)
		{
			$columns[$key]="`$value`";
		}
	
		fwrite($f,";\n\n\n");
		fwrite($f,"INSERT INTO `$tableName` (".implode(',',$columns).") VALUES\n");
		// then select and process all rows
		$first=true;
		do
		{
			if (!$first) fwrite($f,",\n");
			$first=false;
			foreach($row2 as $key=>$value)
			{
				if ($value===null) $row2[$key]='NULL';
				else $row2[$key]="'".mysql_real_escape_string($value)."'";
			}
			fwrite($f,"(".implode(',',$row2).")");
		}
		while($row2=mysql_fetch_assoc($s));
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
