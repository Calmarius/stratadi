<?php

session_name('wtfbattles2');
session_start();
if (!isset($_SESSION['lang'])) $_SESSION['lang']='hu';
$sn=$_SESSION['lang'];
require_once('configuration.php');
require_once('languages/hu/hu.php');
$serverLanguage=$language;
require_once('languages/'.$sn.'/'.$sn.'.php');

if (isset($_GET['parentdivid'])) $_SESSION['parentdivid']=$_GET['parentdivid'];

?>
