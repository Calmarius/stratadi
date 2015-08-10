<?php

die('You are doing it wrong!');

require_once("setupmysqli.php");
runEscapedQuery("TRUNCATE wtfb2_villages");
runEscapedQuery("TRUNCATE wtfb2_guilds");
runEscapedQuery("TRUNCATE wtfb2_guildinvitations");
runEscapedQuery("TRUNCATE wtfb2_guildpermissions");
runEscapedQuery("TRUNCATE wtfb2_worldevents");
runEscapedQuery("TRUNCATE wtfb2_threadentries");
runEscapedQuery("TRUNCATE wtfb2_threadlinks");
runEscapedQuery("TRUNCATE wtfb2_threads");
runEscapedQuery("UPDATE wtfb2_users SET guildId=NULL,regDate=NOW()");
runEscapedQuery("UPDATE wtfb2_heroes SET inVillage=0");
runEscapedQuery("DELETE FROM wtfb2_heroes WHERE (ownerId=0)");


die('Server reset happened.');


?>
