<?php

require_once("setupmysqli.php");
require_once("utils/gameutils.php");

$r=runEscapedQuery("SELECT accountId FROM wtfb2_accesses WHERE (permission='admin')");
$a=$r[0][0];

jumpTo('viewplayer.php?id='.$a['accountId']);

?>
