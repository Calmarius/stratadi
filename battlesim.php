<?php

require_once("presenterphps.php");
require_once("utils/gameutils.php");
require_once("battlecalculation.php");

/*print_r($_POST);
echo '<br><br><br><br>';*/

$result=calculateBattleCasualties($_POST);

$bsParams=array();
$bsParams=$_POST;
$bsParams['attackhero']=$_POST['attackhero'];
$bsParams['walllevel']=$_POST['walllevel'];
$bsParams['targetlevel']=$_POST['targetlevel'];
$bsParams['targetlevel']=$_POST['targetlevel'];
$bsParams['targetdemolished']=$result['defender']['targetdemolished'];
$bsParams['defender']['casualties']=$result['defender']['casualties'];
$bsParams['attacker']['casualties']=$result['attacker']['casualties'];
$bsParams['wouldConquer']=$result['wouldConquer'];

showInBox('templates/battlesimtemplate.php',$bsParams);

?>
