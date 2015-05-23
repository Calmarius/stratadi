<?php

require_once('userworkerphps.php');

if (isset($_SESSION['asdeputy']) && $_SESSION['asdeputy']) jumpErrorPage($language['accessdenied']);

doMySqlQuery(sqlPrintf("UPDATE wtfb2_users SET willDeleteAt=NULL WHERE (id='{1}')",array($_SESSION['userId'])));

jumpTo('game.php');


?>
