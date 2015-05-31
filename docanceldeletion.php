<?php

require_once('userworkerphps.php');

if (isset($_SESSION['asdeputy']) && $_SESSION['asdeputy']) jumpErrorPage($language['accessdenied']);

runEscapedQuery("UPDATE wtfb2_users SET willDeleteAt=NULL WHERE (id={0})",$_SESSION['userId']);

jumpTo('game.php');


?>
