<?php

require_once('setupmysqli.php');

runEscapedQuery(
    "ALTER TABLE wtfb2_events 
        MODIFY COLUMN spearmen BIGINT,
        MODIFY COLUMN archers BIGINT,
        MODIFY COLUMN knights BIGINT,
        MODIFY COLUMN catapults BIGINT,
        MODIFY COLUMN diplomats BIGINT,
        MODIFY COLUMN gold BIGINT
    "
);

?>
