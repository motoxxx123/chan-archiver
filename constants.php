<?php
function IsCli()
{
    return php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']);
}
// -----------------------------------------------------------
// INTERNAL STUFF
// you should DEFINATELY leave this alone
// -----------------------------------------------------------
if( !($archiver_config[ 'login_view' ] || $archiver_config[ 'login_chk' ] || $archiver_config[ 'login_chk' ] || $archiver_config[ 'login_chk' ]) )
    $archiver_config[ 'login_enabled' ] = false;


// -----------------------------------------------------------
// Index page for each thread (redirects to chan index)
// -----------------------------------------------------------
$archiver_config[ 'php_threads' ] = '<?php
header("Location: ../index.php#%s");
?>';

// -----------------------------------------------------------
// Index page for each board (e.g. /b/, /g/, /hc/, etc)
// -----------------------------------------------------------
$archiver_config[ 'php_boards' ] = '
<h1>Board Index</h1>
<?php
function readBoard($name)
{
    if(!$handle = opendir("./" . $name))
        return;
    echo "<a name=\"{$name}\"><h2>/{$name}/</h2></a>";
    while(false !== ($entry = readdir($handle)))
    {
        if($entry == "." || $entry == "..")
            continue;

        if(!is_dir("./" . $name . "/" . $entry))
            continue;
        $desc = "";
        if(file_exists("./" . $name . "/" . $entry . "/description.txt"))
            $desc = " - " . file_get_contents("./" . $name . "/" . $entry . "/description.txt");
        echo "<a href=\"{$name}/{$entry}.html\">{$entry}{$desc}</a><br />";
    }
    closedir($handle);
}
if(!$handle = opendir("."))
    return;
while (false !== ($entry = readdir($handle)))
{
    if($entry == "." || $entry == "..")
        continue;
    if(!is_dir($entry))
        continue;
    readBoard($entry);
}
closedir($handle);
?>';

// -----------------------------------------------------------
// Index page for each chan (e.g. 4chan, 7chan, anonib, etc)
// -----------------------------------------------------------
$archiver_config[ 'php_chans' ] = '
<h1>Archiver Index</h1>
<?php

if(!$handle = opendir("."))
    return;
while (false !== ($entry = readdir($handle)))
{
    if($entry == "." || $entry == "..")
        continue;
    if(!is_dir($entry))
        continue;
    echo "<a href=\"{$entry}/\">{$entry}</a><br />";
}
?>';

?>
