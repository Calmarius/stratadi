<?php

die('You are doing it wrong!');

require_once("setupmysql.php");
doMysqlQuery("TRUNCATE wtfb2_villages");
doMysqlQuery("TRUNCATE wtfb2_guilds");
doMysqlQuery("TRUNCATE wtfb2_guildinvitations");
doMysqlQuery("TRUNCATE wtfb2_guildpermissions");
doMysqlQuery("TRUNCATE wtfb2_worldevents");
doMysqlQuery("TRUNCATE wtfb2_threadentries");
doMysqlQuery("TRUNCATE wtfb2_threadlinks");
doMysqlQuery("TRUNCATE wtfb2_threads");
doMysqlQuery("UPDATE wtfb2_users SET guildId=NULL,regDate=NOW()");
doMysqlQuery("UPDATE wtfb2_heroes SET inVillage=0");
doMysqlQuery("DELETE FROM wtfb2_heroes WHERE (ownerId=0)");


die('Server reset happened.');


?>
