<?php

require_once('userworkerphps.php');
bounceSessionOver();

$myId=$_SESSION['userId'];
$r=runEscapedQuery("SELECT notes FROM wtfb2_users WHERE (id={0})",$myId);
$notes=$r[0][0];


showInBox('templates/notestemplate.php',array('notes'=>$notes['notes'],'msg'=>@$_GET['msg']));


?>
