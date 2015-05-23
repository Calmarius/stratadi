<?php

require_once("setupmysql.php");
require_once("utils/gameutils.php");

$r=doMySqlQuery("SELECT accountId FROM wtfb2_accesses WHERE (permission='admin')");
$a=mysql_fetch_assoc($r);

jumpTo('viewplayer.php?id='.$a['accountId']);

?>
