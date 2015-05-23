<?php

require_once('userworkerphps.php');
bounceSessionOver();

$myId=$_SESSION['userId'];
doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET notes='{1}' WHERE (id='{2}')",array($_POST['notes'],$myId)));

jumpTo('notes.php?msg='.urlencode($language['notessaved']));


?>
