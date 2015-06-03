<?php

require_once('userworkerphps.php');
bounceSessionOver();

$myId=$_SESSION['userId'];
runEscapedQuery("UPDATE wtfb2_users SET notes={0} WHERE (id={1})",$_POST['notes'],$myId);

jumpTo('notes.php?msg='.urlencode($language['notessaved']));


?>
