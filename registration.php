<?php

header('content-type:text/html; charset=utf-8');

require_once('userworkerphps.php');

if (!isset($_SESSION['registrationparms'])) $_SESSION['registrationparms']=array();

showInBox('templates/regform.php',$_SESSION['registrationparms']);

?>
