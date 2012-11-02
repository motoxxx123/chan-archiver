<?php

class yummyChanThread extends baseThread
{
    public $chanName = "yummyChan";
    public $chanFriendlyName = "YummyChan";
    
    public static $regex = "/(http:\/\/yummychan\.org).*?((?:[a-z][a-z0-9_]*)).*?(res).*?(\d+)(\.html)/is";
    public static $regexBoardIdx = 2;
    public static $regexThreadIdx = 4;
    
    public $baseURL = "http://www.yummychan.org/";
    public $threadURL = "http://www.yummychan.org/%s/res/%s.html";
    public $imageURL = "http://www.yummychan.org/%s/src/";
    public $thumbURL = "http://www.yummychan.org/%s/thumb/";
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
