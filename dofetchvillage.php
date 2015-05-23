<?php

require_once('userworkerphps.php');
require_once("villageupdater.php");

$villageId=(int)$_POST['id'];
$x=(int)$_POST['x'];
$y=(int)$_POST['y'];
updateVillage($villageId);
$_GET['left']=$_GET['right']=$x;
$_GET['top']=$_GET['bottom']=$y;
include('areainfo.php');	// ugly but saves a jump
//jumpTo("areainfo.php?left=$x&top=$y&right=$x&bottom=$y");

?>
