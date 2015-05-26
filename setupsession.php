<?php

require_once('configuration.php');

session_name('wtfbattles2');
session_start();
$sn=$config['serverLanguage'];
if (!isset($_SESSION['lang'])) $_SESSION['lang']=$sn;
require_once('languages/'.$sn.'/'.$sn.'.php');
$serverLanguage=$language;
if (isset($_GET['parentdivid'])) $_SESSION['parentdivid']=$_GET['parentdivid'];

?>
