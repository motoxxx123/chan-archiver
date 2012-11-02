<?php


session_start();
$time = microtime(true);
include "chanArchiver.php";
include "templating.php";
$t = new chanArchiver();
$t->connectDB();
include "frontend.php";

if ( !isset($archiver_config[ 'updater_enabled' ]) || $archiver_config[ 'updater_enabled' ] )
    $t->doUpdate();
$return = "";
if ( $delenabled && isset( $_REQUEST[ 'del' ] ) && isset( $_REQUEST[ 'tid' ] ) && isset( $_REQUEST[ 'files' ] ) )
    $return .= $t->RemoveThreadByID( $_REQUEST[ 'tid' ], $_REQUEST[ 'files' ] );

if ( $delenabled && isset( $_REQUEST[ 'clr' ] ) )
    $return .= $t->ClearThreads();

if ( $chkenabled && isset( $_REQUEST[ 'chk' ] ) && isset( $_REQUEST[ 'tid' ] ) )
    $return .= $t->UpdateThreadByID( $_REQUEST[ 'tid' ] );

if ( $chkenabled && isset( $_REQUEST[ 'chka' ] ) )
    $return .= $t->CheckThreads( false );

if ( $chkenabled && isset( $_REQUEST[ 'chkb' ] ) && is_array( $_REQUEST[ 'chkb' ] ) )
{
    $keys = array_keys( $_REQUEST[ 'chkb' ] );
    $return .= $t->CheckThreads( false, $keys[0] );
}

if ( $chkenabled && isset( $_REQUEST[ 'zip' ] ) && isset( $_REQUEST[ 'tid' ] ) )
    $return .= $t->CreateThreadZipByID( $_REQUEST[ 'tid' ] );

if ( $delenabled && isset( $_REQUEST[ 'upd' ] ) && isset( $_REQUEST[ 'tid' ] ) )
    $return .= $t->UpdateThreadDescriptionByID( $_REQUEST[ 'tid' ], $_REQUEST[ 'desc' ] );
    
if ( $addenabled && isset( $_REQUEST[ 'add' ] ) && isset( $_REQUEST[ 'url' ] ) )
{
    if ( substr( $_REQUEST[ 'url' ], 0, 7 ) != "http://" )
        $_REQUEST[ 'url' ] = "http://" . $_REQUEST[ 'url' ];
    if ( !isset( $_REQUEST[ 'desc' ] ) )
        $_REQUEST[ 'desc' ] = "";
        $return .= $t->AddThread( $_REQUEST[ 'url' ], $_REQUEST[ 'desc' ] );
}

if ( $return != "" )
{
    $_SESSION[ 'returntime' ] = microtime(true) - $time;
    $_SESSION[ 'returnvar' ] = $return;
    header( 'Location: index.php' );
    exit;
}

echo $template;
$t->closeDB();
?>
