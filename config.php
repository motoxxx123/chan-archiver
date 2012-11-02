<?php
$archiver_config = array();
// -----------------------------------------------------------
// FOLDER CONFIG
// e.g. if your script is at /chandl/ these should be set to /chandl/arch/
// -----------------------------------------------------------

// where to store the archives data, this folder should probably get made by you with 777 perms
$archiver_config[ 'storage' ] = "/opt/share/www/archive/res/";

// the publicly accessible link to the data store
$archiver_config[ 'pubstorage' ] = "http://linkstation:8081/archive/res/";

// -----------------------------------------------------------
// MYSQL CONFIG
// self explanatory
// -----------------------------------------------------------

$archiver_config[ 'database_type' ] = "mysql";
$archiver_config[ 'database_db' ]   = "chanarchiver";
$archiver_config[ 'database_user' ] = "root";
$archiver_config[ 'database_pass' ] = "lolusingrootforthis";
$archiver_config[ 'database_host' ] = "localhost";

// -----------------------------------------------------------
// ACCESS CONTROL
// -----------------------------------------------------------

// enable/disable the login system
$archiver_config[ 'login_enabled' ] = false;

// username & password for login
$archiver_config[ 'login_user' ] = "eggman";
$archiver_config[ 'login_pass' ] = "implying";

// if this is true you need to login to view threads
// (only hides thread list, password protect the archive directory if your paranoid)
$archiver_config[ 'login_view' ] = true;

// if this is true you need to login to zip or manually check threads
$archiver_config[ 'login_chk' ] = false;

// if this is true you need to login to add threads
$archiver_config[ 'login_add' ] = false;

// if this is true you need to login to delete or change description of threads
$archiver_config[ 'login_del' ] = false;

// -----------------------------------------------------------
// PLUGINS
// very experimental
// -----------------------------------------------------------

$archiver_config[ 'enabled_plugins' ] = array();

$archiver_config[ 'enabled_plugins' ][ 'fourChan' ] = true;
$archiver_config[ 'enabled_plugins' ][ 'sevenChan' ] = true;
$archiver_config[ 'enabled_plugins' ][ 'fourTwentyChan' ] = true;
$archiver_config[ 'enabled_plugins' ][ 'anonIB' ] = true;
$archiver_config[ 'enabled_plugins' ][ 'yummyChan' ] = true;

// -----------------------------------------------------------
// ADVANCED STUFF
// you should probably leave this alone
// -----------------------------------------------------------

$archiver_config[ 'updater_enabled' ] = false;

include "constants.php";
?>
