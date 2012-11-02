<?php

class anonIBThread extends baseThread
{
    public $chanName = "anonIB";
    public $chanFriendlyName = "AnonIB";
    
    public static $regex = "/(http:\/\/anonib\.com).*?((?:[a-z][a-z0-9_]*)).*?(res).*?(\d+)(\.html)/is";
    public static $regexBoardIdx = 2;
    public static $regexThreadIdx = 4;
    
    public $baseURL = "http://anonib.com/";
    public $threadURL = "http://anonib.com/%s/res/%s.html";
    public $imageURL = "http://anonib.com/%s/src/";
    public $thumbURL = "http://anonib.com/%s/thumb/";
    public $thumbURLSecondary = "";
    
    public $threadStartSeperator = "<a name=\"s\">";
    public $postStartSeperator = "<td class=\"reply\" id=\"reply";
    public $postEndSeperator = "</blockquote>";
    
    public $postIDStartSeperator = "name=\"post[]\" value=\"";
    public $postIDEndSeperator = "\"";
    
    public $postTimeStartSeperator = "</span>";
    public $postTimeEndSeperator = "</label>";
    public $postTimeFormatted = true;
    public $postTimeYearSwapped = true;
    public $postTimeYearTwoDigit = true;
    
    public $postFileDetailsStartSeperator = "<a target=\"_blank\"";
    public $postFileDetailsEndSeperator = "</span>";
    
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
