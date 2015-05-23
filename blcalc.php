<?php

$score=(int)$_GET['s'];
$limit=7;
if (isset($_GET['l']))
{
	$limit=(int)$_GET['l'];
}
$maxSum=(int)$_GET['m'];

$echoes=1000;

function printPossibleLevels($score,$prevLevels,$maxDepth,$maxSum)
{
	global $echoes;
	if (array_sum($prevLevels)>$maxSum) return;
	if ($maxDepth==0)
	{
		if ($score<=0)
		{
			echo implode(",",$prevLevels)."\n";
			$echoes--;
			if ($echoes==0) die();
		}
		return;
	}
	$maxLev=floor(sqrt($score));
	$last=end($prevLevels);
	if ($last!==FALSE)
	{
		if ($last<$maxLev) $maxLev=$last;
	}
	for($i=$maxLev;$i>=0;$i--)
	{
		$ns=$prevLevels;
		$ns[]=$i;
		printPossibleLevels($score-$i*$i,$ns,$maxDepth-1,$maxSum);
	}
}

?>

<!DOCTYPE>
<html>
	<head>
	</head>
	<form action="blcalc.php" method="GET">
		<fieldset>
			<legend>Calculation parameters</legend>
			Score: <input type="text" name="s" value="<?php echo (int)$_GET['s'];?>"><br>
			Maximum building count: <input type="text" name="l" value="<?php echo (int)$_GET['l'];?>"><br>
			Maximum build points produced in village: <input type="text" name="m" value="<?php echo (int)$_GET['m'];?>"><br>
			<input type="submit">
		</fieldset>
	</form>
	<body>
		<?php
			echo '<pre>';
			printPossibleLevels($score,array(),$limit,$maxSum);
			echo '</pre>';
		?>
	</body>
</html>


