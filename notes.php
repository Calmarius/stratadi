<?php

require_once('userworkerphps.php');
bounceSessionOver();

$myId=$_SESSION['userId'];
$r=doMySqlQuery(sqlPrintf("SELECT notes FROM wtfb2_users WHERE (id='{1}')",array($myId)));
$notes=mysql_fetch_assoc($r);


showInBox('templates/notestemplate.php',array('notes'=>$notes['notes'],'msg'=>@$_GET['msg']));


?>
