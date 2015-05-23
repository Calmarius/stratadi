<?php

require_once("presenterphps.php");

$latitude=$config['latitude'];
$longitude=$config['longitude'];
//$timezone=$config['timezone'];
$timezone=((int)date('Z'))/3600;

for($i=-18;$i<=5;$i++)
{
	echo "Sun rises above ${i}°: ".date_sunrise(time(),SUNFUNCS_RET_STRING,$latitude,$longitude,90-$i,$timezone)."<br>";
}
echo "<br>";
for($i=5;$i>=-18;$i--)
{
	echo "Sun set below ${i}°: ".date_sunset(time(),SUNFUNCS_RET_STRING,$latitude,$longitude,90-$i,$timezone)."<br>";
}

?>
