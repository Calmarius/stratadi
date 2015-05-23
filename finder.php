<?php

require_once('userworkerphps.php');
bounceNoAdmin();

function globRec($dir,$filter,&$array)
{
	$d=opendir($dir);
	while(($file=readdir($d))!==FALSE)
	{
		if (($file=='.') || ($file=='..')) continue;
		$relPath=$dir.'/'.$file;
		if (is_dir($file)) globRec($relPath,$filter,&$array);
		if (preg_match('/'.$filter.'/',$relPath)>0)
		{
			$array[]=$relPath;
		}
	}
}

function globRecursive($filter)
{
	$array=array();
	globRec('.',$filter,$array);
	asort($array);
	return $array;
}

//die(sqlPrintf("SELECT * FROM wtfb2_users WHERE (userName='{1}') AND (MD5(paswordHash)=MD5('{2}') )",array("HEHE","HAHA')) OR (1=1)")));

$youLookFor=$_GET['what'];
$filter=$_GET['where'];
if (($youLookFor!='') && ($filter!=''))
{
	$matches=0;
	$lines=0;

	$files=globRecursive($filter);
	foreach($files as $key=>$file)
	{
/*		?>
			<pre>
				<?php echo $file;   ?>
			</pre>
		<?php*/
		$cnt=file($file);
		$nline=1;
		foreach($cnt as $key2=>$line)
		{
			$lines++;
			$pos=strpos($line,$youLookFor);
			if ($pos!==FALSE)
			{
				$pos+=1;
				?>
					<pre>
						<?php echo "$file ($nline,$pos): ".htmlspecialchars($line);   ?>
					</pre>
				<?php
				$matches++;
			}
			$nline++;
		}
	}
}
?>
	<pre>
		Found <?php echo $matches?> matches!
		You have written <?php echo $lines; ?> lines already!
	</pre>
<?php
	

?>
<hr>
<form action="finder.php" method="GET">
	What: <input type="text" name="what" value="<?php echo $_GET['what']; ?>"><br>
	Where: <input type="text" name="where" value="<?php echo $_GET['where']; ?>"><br>
	<input type="submit">
</form>
