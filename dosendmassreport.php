<?php

require_once('userworkerphps.php');
bounceNoAdmin();

$r=runEscapedQuery("SELECT * FROM wtfb2_users");
foreach ($r[0] as $user)
{
    runEscapedQuery("
        INSERT INTO wtfb2_reports
        (recipientId,title,text,reportTime,reportType,token)
        VALUES
        ({0},{1},{2},NOW(),{3},MD5(RAND()))",
        $user['id'],$_POST['subject'],$_POST['text'],'adminmessage'
    ); // megcsinálni jobbra.
}

jumpTo('massreport.php');

?>
