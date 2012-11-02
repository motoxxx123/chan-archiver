<?php
include "chanArchiver.php";
$t = new chanArchiver();
$t->connectDB();
echo $t->CheckThreads( true );
$t->closeDB();
?>