<?php

session_start();
$time = time();
set_time_limit(9001);
include "chanArchiver.php";
$t = new chanArchiver();
$t->connectDB();
include "templating.php";
include "frontend.php";
if( key_in_array($_REQUEST, "chk") )
{
    echo $t->UpdateThreadByID( $_REQUEST[ 'chk' ] );
}
if( key_in_array($_REQUEST, "add") )
{
    echo $t->AddThread($_REQUEST[ 'add' ], $_REQUEST[ 'desc' ]);
}
if( key_in_array($_REQUEST, "del") )
{
    echo $t->RemoveThreadByID( $_REQUEST[ 'del' ], $_REQUEST[ 'files' ] );
}
if( key_in_array($_REQUEST, "tbl") )
{
    echo '<script type="text/javascript">threads = new Array();';
    echo $jsthreads;
    echo '</script>' . "\r\n";
    echo $actions . "\r\n\r\n" . $thrinfo;
}
$t->closeDB();

?>
