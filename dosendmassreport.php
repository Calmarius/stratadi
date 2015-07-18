<?php

require_once('userworkerphps.php');
bounceNoAdmin();

$r = runEscapedQuery("SELECT userName FROM wtfb2_users WHERE (id = {0})", $_SESSION['userId']);
$me = $r[0][0];

$text = '<div class="left">'.$_POST['text'].'</div><p class="right"><a href="viewplayer.php?id='.$_SESSION['userId'].'">- '.$me['userName'].'</a></p>';

$r=runEscapedQuery("SELECT * FROM wtfb2_users");
foreach ($r[0] as $user)
{
    runEscapedQuery("
        INSERT INTO wtfb2_reports
        (recipientId,title,text,reportTime,reportType,token)
        VALUES
        ({0},{1},{2},NOW(),{3},MD5(RAND()))",
        $user['id'],$_POST['subject'],$text,'adminmessage'
    );
}

jumpTo('massreport.php');

?>
