<?php

require_once('userworkerphps.php');
require_once('inbuiltpie.php');
bounceNoAdmin();

$img=getInbuiltPie();
imagettftext($img,13,0,0,15,0x0000FF,realpath('Verdana.ttf'),'<ADMIN!>');
header('content-type: image/png');
imagepng($img);

?>
