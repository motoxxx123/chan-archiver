var ajaxinprocess = false;
var threadsToProcess = new Array();
var autoenabled = true;
var autotimeout = 60; // 60 secs
function timeOut()
{
    if(!autoenabled)
        return;
    updateAllThreads();
    setTimeout(timeOut,autotimeout * 1000);
}
function updateTable()
{
    if(ajaxinprocess)
    {
        alert("there is already an operation in progress.");
        return;
    }
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            var resp = xmlhttp.responseText;
            var split = resp.split("</script>");
            eval(split[0].replace('<script type="text/javascript">', ''));
            document.getElementById("thrtbl").innerHTML = xmlhttp.responseText;
            ajaxinprocess = false;
        }
    }
    xmlhttp.open("GET","ajax.php?tbl=true",true);
    xmlhttp.send();
    ajaxinprocess = true;
}

function addThread(url, description)
{
    if(ajaxinprocess)
    {
        alert("there is already an operation in progress.");
        return;
    }
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById("addthread").value = "Add";
            document.getElementById("addthread").enabled = true;
            document.getElementById("infoboxes").innerHTML = '<div class="infobox">' + xmlhttp.responseText + '</div><br />' + document.getElementById("infoboxes").innerHTML;
            ajaxinprocess = false;
            updateTable();
        }
    }
    document.getElementById("addthread").value = "...";
    document.getElementById("addthread").enabled = false;
    url = url.replace("http://", "http---");
    url = url.replace("https://", "https---");
    xmlhttp.open("GET","ajax.php?add=" + url + "&desc=" + description,true);
    xmlhttp.send();
    ajaxinprocess = true;
}
function updateAllThreads(board)
{
    if(ajaxinprocess)
    {
        alert("there is already an operation in progress.");
        return;
    }
    threadsToProcess = new Array();
    var i = 0;
    for(var index in threads)
    {
       var idx = index;
       var id = threads[idx][0];
       var brd = threads[idx][1];
       if(!board)
       {
           threadsToProcess[i] = id;
           i++;
       }
       else
           if(board == brd)
           {
                threadsToProcess[i] = id;
                i++;
           }
    }
    processThread(0);
}

function processThread(idx)
{
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    var id = threadsToProcess[idx];
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            if(xmlhttp.responseText && xmlhttp.responseText != "")
            {
                document.getElementById("chk" + id).innerHTML = "0 secs ago";
                if(xmlhttp.responseText.indexOf("404'd") != -1)
                {
                    document.getElementById("ste" + id).innerHTML = "404'd";
                    document.getElementById("lst" + id).innerHTML = "N/A";
                }
                document.getElementById("infoboxes").innerHTML = '<div class="infobox">' + xmlhttp.responseText + '</div><br />' + document.getElementById("infoboxes").innerHTML;
            }
            ajaxinprocess = false;
            if(threadsToProcess[idx+1] == null)
                updateTable();
            else
                processThread(idx + 1);
        }
    }
    document.getElementById("chk" + id).innerHTML = "checking...";
    xmlhttp.open("GET","ajax.php?chk=" + id,true);
    xmlhttp.send();
    ajaxinprocess = true;
}

function removeThread(id, removefiles)
{
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById("infoboxes").innerHTML = '<div class="infobox">' + xmlhttp.responseText + '</div><br />' + document.getElementById("infoboxes").innerHTML;
            ajaxinprocess = false;
            updateTable();
        }
    }
    document.getElementById("chk" + id).innerHTML = "removing...";
    xmlhttp.open("GET","ajax.php?del=" + id + "&files=" + (removefiles ? "1" : "0"),true);
    xmlhttp.send();
    ajaxinprocess = true;
}

function updateThread(id)
{
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            if(xmlhttp.responseText && xmlhttp.responseText != "")
            {
                document.getElementById("chk" + id).innerHTML = "0 secs ago";
                if(xmlhttp.responseText.indexOf("404'd") != -1)
                {
                    document.getElementById("ste" + id).innerHTML = "404'd";
                    document.getElementById("lst" + id).innerHTML = "N/A";
                }
                document.getElementById("infoboxes").innerHTML = '<div class="infobox">' + xmlhttp.responseText + '</div><br />' + document.getElementById("infoboxes").innerHTML;
            }
            ajaxinprocess = false;
            updateTable();
        }
    }
    document.getElementById("chk" + id).innerHTML = "checking...";
    xmlhttp.open("GET","ajax.php?chk=" + id,true);
    xmlhttp.send();
    ajaxinprocess = true;
}
setTimeout(timeOut,60000);