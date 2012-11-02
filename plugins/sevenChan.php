<?php

class sevenChanThread extends baseThread
{
    public $chanName = "sevenChan";
    public $chanFriendlyName = "7chan";
    
    public static $regex = "/(http:\/\/7chan\.org).*?((?:[a-z][a-z0-9_]*)).*?(res).*?(\d+)(\.html)/is";
    public static $regexBoardIdx = 2;
    public static $regexThreadIdx = 4;
    
    public $baseURL = "";
    public $threadURL = "http://7chan.org/%s/res/%s.html";
    public $imageURL = "http://7chan.org/%s/src/";
    public $thumbURL = "http://7chan.org/%s/thumb/";
    public $thumbURLSecondary = "";
    
    public $threadStartSeperator = "";
    public $postStartSeperator = "class=\"post\"";
    public $postEndSeperator = "<br class=\"clear-both\" />";
    
    public $postIDStartSeperator = "name=\"post[]\" value=\"";
    public $postIDEndSeperator = "\"";
    
    public $postTimeStartSeperator = "</span>";
    public $postTimeEndSeperator = "<span";
    public $postTimeFormatted = true;
    public $postTimeYearSwapped = true;
    public $postTimeYearTwoDigit = true;
    
    public $postFileDetailsStartSeperator = "<a target=\"_blank\"";
    public $postFileDetailsEndSeperator = "</a>";
    
    public $postFileLinkStartSeperator = "href=\"";
    public $postFileLinkEndSeperator = "\"";
    
    public $postThumbLinkStartSeperator = "src=\"";
    public $postThumbLinkEndSeperator = "\"";
    
    // needed because of stupid php limitations
    public static function CheckLink($link, $class = __CLASS__)
    {
        return parent::CheckLink($link, $class);
    }
}

?>
