<?php

function key_in_array($array, $find){
 $exists = FALSE;
 if(!is_array($array)){
   return;
}
foreach ($array as $key => $value) {
  if($find == $key){
       $exists = TRUE;
  }
}
  return $exists;
}

// login stuff
if ( isset( $_REQUEST[ 'login' ] ) && isset( $_REQUEST[ 'user' ] ) && isset( $_REQUEST[ 'pass' ] ) )
{
    $_SESSION[ 'uname' ] = $_REQUEST[ 'user' ];
    $_SESSION[ 'pword' ] = $_REQUEST[ 'pass' ];
}
// commands
$isloggedin = ( isset( $_SESSION[ 'uname' ] ) && isset( $_SESSION[ 'pword' ] ) && $_SESSION[ 'uname' ] == $archiver_config[ 'login_user' ] && $_SESSION[ 'pword' ] == $archiver_config[ 'login_pass' ] ) || !$archiver_config[ 'login_enabled' ];
$delenabled = ( !$archiver_config[ 'login_del' ] || $isloggedin );
$chkenabled = ( !$archiver_config[ 'login_chk' ] || $isloggedin );
$addenabled = ( !$archiver_config[ 'login_add' ] || $isloggedin );
$viewenabled = ( !$archiver_config[ 'login_view' ] || $isloggedin );

$host = "http://" . ( $_SERVER[ 'HTTP_HOST' ] ? $_SERVER[ 'HTTP_HOST' ] : $_SERVER[ "SERVER_NAME" ] ) . $_SERVER[ "SCRIPT_NAME" ];
$infobox = "";
$actions = "";

$update = <<<ENDHTML
    <div class="alertbox">There is an <a href="{$t->updaterurl}" onclick="alert('make sure you delete version.txt after updating!');">update</a> available! <a href="{$t->compareurl}{$t->currentVersion}...{$t->latestVersion}">(diff)</a></div><br />
ENDHTML;
if ( !$t->updateAvailable )
    $update = "";

$chans = array();
$jsthreads = "";
if ( $viewenabled )
{
    $threads = $t->GetThreads();
    $i = 0;
    $deleted = 0;
    foreach ( $threads as $thr )
    {
        if( !key_in_array($chans, $thr->Chan) )
            $chans[$thr->Chan] = $thr->chanFriendlyName;
        if($thr->Status == 1)
            $jsthreads .= "threads[" . ($i - $deleted) . "] = [\"{$thr->ID}\", \"{$thr->Chan}\"];\r\n";
        else
            $deleted++;
        $local  = $thr->GetThreadLocalURL() . ".html";
        //$check  = ($thr->Status == 1 && $chkenabled ? "<input type=\"submit\" name=\"chk\" value=\"Check\"/>" : "") . ($delenabled ? "<input type=\"submit\" name=\"del\" onclick=\"if(!confirm('Keep files?')) document.getElementById('files{$i}').value='1';\" value=\"Remove\"/>" : "");
        $check = ($thr->Status == 1 && $chkenabled ? "<input type=\"button\" name=\"chk\" onclick=\"updateThread({$thr->ID});\" value=\"Check\"/>" : "") . ($delenabled ? "<input type=\"button\" name=\"del\" onclick=\"if(!confirm('Keep files too?')) removeThread({$thr->ID}, true); else removeThread({$thr->ID}, false);\" value=\"Remove\"/>" : "");
        $lastposttime = $thr->GetLatestPostTime();
        $lastposttime = '<div id="lst' . $thr->ID . '">' . ($lastposttime == "" || $lastposttime <= 0 ? "N/A" : date( "m/d/y, g:i a", $lastposttime )) . "</div>";
        $zip = $chkenabled ? ($thr->IsZipped() ? "<a href=\"{$thr->GetZipPath( false )}\"><img src=\"static/zip_icon.png\" border=\"0\"/></a>" : "<input type=\"image\" src=\"static/zip_icon.png\" class=\"faded\" onsubmit=\"submit-form();\" name=\"zip\" value=\"1\" />") : "";
        $link = ($thr->Status == 1 ? "<a href=\"{$thr->GetThreadURL()}\">{$thr->ThreadID}</a> <a href=\"$local\">(local)</a>" : "<a href=\"$local\">{$thr->ThreadID}</a>") . "&nbsp;" . $zip;
        $description = $delenabled ? "<input type=\"text\" name=\"desc\" value=\"{$thr->Description}\"/><input type=\"submit\" name=\"upd\" value=\"Update\"/>" : $thr->Description;
        $lastchecked = '<div id="chk' . $thr->ID . '">' . ($thr->Status == 1 ? ($thr->LastChecked == 0 ? "never" : time() - $thr->LastChecked . " secs ago") : "") . "</div>";
        $status = '<div id="ste' . $thr->ID . '">' . ($thr->Status == 1 ? "Ongoing" : "404'd") . "</div>";
        $row = array( $link , $thr->chanFriendlyName, "/{$thr->Board}/", $description, $status, $lastchecked, $lastposttime, $check);
        
        $thrinfo->AddRow($row, '<form action="?" method="post"><input type="hidden" name="tid" value="' . $thr->ID . '"/>', '</form>');
        $i++;
    }
    $thrinfo = $thrinfo->GetHTML();
}
else
    $thrinfo = "";

if ( $addenabled )
{
    $addthread = <<<ENDHTML
<form action="?" method="post">
{$addthread->GetHTML()}
</form>
ENDHTML;
}
else
    $addthread = "";


if ( $chkenabled && $viewenabled )
{
    $onclick = $t->GetOngoingThreadCount() >= 10 ? "alert('Since you have many ongoing threads it may seem like the page has hung, just be patient and they will all update');" : "";
    foreach ($chans as $key => $value) {
        $actions .= <<<ENDHTML
<input type="button" name="chkb[$key]" onclick="updateAllThreads('$key');" value="Recheck $value"/>
ENDHTML;
    }
    $actions .= <<<ENDHTML
<input type="button" name="chka" onclick="updateAllThreads(null);" value="Recheck All"/><input type="button" name="reft" onclick="updateTable();" value="Refresh"/>
<input type="checkbox" name="auto" onclick="autoenabled = document.getElementById('auto').checked; setTimeout(timeOut,autotimeout * 1000);" checked="true" />Auto Refresh
ENDHTML;
}

if ( $delenabled && $t->GetEndedThreadCount() > 0 )
{
    $actions .= <<<ENDHTML
<input type="submit" name="clr" value="Clear 404'd"/>
ENDHTML;
}

if( !$isloggedin )
{
    $login = <<<ENDHTML
<form action="?" method="post">
{$login}
</form>
ENDHTML;
}
else
{
    $login = "";
    if($archiver_config[ 'login_enabled' ])
    {
        $actions .= <<<ENDHTML
    <input type="hidden" name="user" value="" />
    <input type="hidden" name="pass" value="" />
    <input type="submit" name="login" value="Logout"/>
ENDHTML;
    }
}

$actions = "<form action=\"?\" method=\"post\">\r\n{$actions}\r\n</form>";

if ( isset( $_SESSION[ 'returnvar' ] ) && !empty( $_SESSION[ 'returnvar' ] ) )
{
    if( isset( $_SESSION[ 'returntime' ] ) && !empty( $_SESSION[ 'returntime' ] ) )
        $time -= $_SESSION[ 'returntime' ];
        
    $arr = explode( '<br />', $_SESSION[ 'returnvar' ] );
    foreach ( $arr as $str )
    {
        if ( empty( $str ) || strlen( $str ) <= 3 )
            continue;
        $infobox .= <<<ENDHTML
    <div class="infobox">$str</div><br />
ENDHTML;
    }
    $_SESSION[ 'returnvar' ] = "";
    $_SESSION[ 'returntime' ] = "";
    unset( $_SESSION[ 'returnvar' ] );
    unset( $_SESSION[ 'returntime' ] );
}

$time = microtime(true) - $time;
$template = <<<ENDHTML
<!DOCTYPE html PUBLIC
  "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>4chan archiver - by anon e moose</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<link rel="icon" type="image/vnd.microsoft.icon" href="favicon.ico" />
<style type="text/css">
.infobox{
width:900px;
border:solid 1px #DEDEDE;
background:#FFFFCC url(static/menu_tick.png) 8px 6px no-repeat;
color:#222222;
padding:4px;
text-align:center;
}

.alertbox{
width:900px;
border:solid 1px #DEDEDE;
background:#FF3330 url(static/menu_light.png) 8px 6px no-repeat;
color:#222222;
padding:4px;
text-align:center;
}

.faded{
opacity: 0.4;
filter: alpha(opacity=40);
zoom: 1;  /* needed to trigger "hasLayout" in IE if no width or height is set */ 
}
</style>
<script type="text/javascript">
var threads = new Array();
{$jsthreads}
</script>
<script type="text/javascript" src="static/scripts.js"></script>
</head>
<body style="font-family: verdana">
<h2><a href="http://github.com/emoose/chan-archiver/">chan archiver 1.0 - by anon e moose</a></h2>

{$update}
<div id="infoboxes">
{$infobox}
</div>
<font size="1" family="Verdana">infobox options: <a href="#" onclick="(document.getElementById('infoboxes').style['display'] == 'none' ? document.getElementById('infoboxes').style['display'] = '' : document.getElementById('infoboxes').style['display'] = 'none');">show/hide</a> <a href="#" onclick="document.getElementById('infoboxes').innerHTML = '';">clear</a></font>
{$login}

{$addthread}
<div id="thrtbl">
{$actions}

{$thrinfo}
</div>
<font size="1" family="Verdana">
downloaded from <a href="http://github.com/emoose/chan-archiver/">github.com/emoose/chan-archiver</a>. 
<abbr title="not fully accurate due to redirections and ajax and such">page took {$time} seconds to execute.</abbr>
<abbr title="bookmarklet: drag this into your bookmarks and use it to add threads"><a href="javascript:open('{$host}?add=Add&url=' + document.URL.replace('http://', 'http---').replace('https://', 'https---') + '&desc=' + encodeURIComponent(prompt('Please enter the threads description', '')));">add to archive</a></abbr></font>
</body>
</html>
ENDHTML;

?>
