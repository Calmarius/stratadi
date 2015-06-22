<?php

require_once('userworkerphps.php');
bounceSessionOver();

$r = runEscapedQuery(
"
    SELECT e.eventType AS type, COUNT(e.id) AS count
    FROM wtfb2_events e
    INNER JOIN wtfb2_villages v ON e.destinationVillage = v.id
    WHERE v.ownerId = {0} AND e.eventType IN  ('attack', 'recon', 'raid')
    GROUP BY e.eventType
", $_SESSION['userId']
);

$me = runEscapedQuery("SELECT userName FROM wtfb2_users WHERE (id = {0})", $_SESSION['userId']);


?>

<!DOCTYPE html>
<html>
    <head>
        <title>
            <?php
                if (isEmptyResult($r))
                {
                    ?>Attack alerter<?php
                }
                else
                {
                    ?>YOU ARE UNDER ATTACK!<?php
                }
            ?>
        </title>
        <meta http-equiv="refresh" content="60">
    </head>
    <body>
        <script>
            function beep()
            {
                var snd = new Audio('alert.mp3');
                snd.play();
            }
        </script>
        <p>Alerter for <?php echo $me[0][0]['userName']; ?></p>
        <p>Last update: <?php echo date(DATE_RSS); ?> <a href="javascript:void(beep())">Test alert</a></p>
        <?php
            if (isEmptyResult($r))
            {
                ?>
                    <p>Clear!</p>
                <?php
            }
            else
            {
                ?>
                    <p>You are under attack!</p>
                    <script>beep();</script>
                <?php
            }
        ?>
    </body>
</html>
