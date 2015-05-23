<?php

header("HTTP/1.0 500");

header('content-type: text/plain; charset=UTF-8');


require_once('userworkerphps.php');

require_once('villageupdater.php');

updateVillage($_POST['villageId']);


?>
