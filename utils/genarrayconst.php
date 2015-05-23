<?php

die();

require_once('configuration.php');

function genArrayConst($array,$pre)
{
	$result="";
	$result.="array\n";
	$result.="$pre(\n";
	$first=true;
	foreach($array as $key=>$value)
	{
		if (!$first)
		{
			$result.=",\n";
		}
		$first=false;
		$v;
		if (is_string($value)) $v="'$value'";
		else if (is_array($value)) $v=genArrayConst($value,$pre."\t");
		else $v=$value;
		$tabs="";
		$result.="$pre\t'$key' => $v";
	}
	$result.="\n$pre)";
	return $result;
}

echo '<pre>';
echo genArrayConst($config,"");
echo '</pre>';

?>
