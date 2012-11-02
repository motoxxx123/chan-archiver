<?php

// this is experimental
// you should run this in CLI / cron, not with apache
// make a chanrip.stop file to stop it
include "chanArchiver.php";

$t = new chanArchiver();

$t->connectDB();

// we need to open index page, split threads, attempt adding all the threads to DB, etc
// maybe scan other pages too?
while(true)
{
    echo "scanning page...\n";
    $pagedata = chanArchiver::getSource("http://boards.4chan.org/b/");
    $threads = explode( "div class=\"thread\" id=\"t", $pagedata );
    for($i = 0; $i < count( $threads ); $i++)
    {
        $thread = explode( "\">", $threads[$i] );
        $thread = $thread[ 0 ];
        $t->AddThread("http://boards.4chan.org/b/res/{$thread}", "chanrip");
    }
    $t->CheckThreads(true, "fourChan");
    if(file_exists("chanrip.stop"))
        break;
}
$t->closeDB();



?>
