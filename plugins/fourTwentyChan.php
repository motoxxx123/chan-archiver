<?php

class fourTwentyChanThread extends baseThread
{
    public $chanName = "fourTwentyChan";
    public $chanFriendlyName = "420chan";
    
    public static $regex = "/(http:\/\/boards\.420chan\.org).*?((?:[a-z][a-z0-9_]*)).*?(res).*?(\d+)(\.php)/is";
    public static $regexBoardIdx = 2;
    public static $regexThreadIdx = 4;
    
    public $baseURL = "http://boards.420chan.org/";
    public $threadURL = "http://boards.420chan.org/%s/res/%s.php";
    public $imageURL = "http://boards.420chan.org/%s/src/";
    public $thumbURL = "http://boards.420chan.org/%s/thumb/";
    public $thumbURLSecondary = "";
    
    public $threadStartSeperator = "";
    public $postStartSeperator = "a id=\"";
    public $postEndSeperator = "</blockquote>";
    
    public $postIDStartSeperator = "\">No.";
    public $postIDEndSeperator = "</a>";
    
    public $postTimeStartSeperator = " -  ";
    public $postTimeEndSeperator = " EST";
    public $postTimeFormatted = true;
    public $postTimeYearSwapped = false;
    public $postTimeYearTwoDigit = false;
    
    public $postFileDetailsStartSeperator = "<a onclick=\"return";
    public $postFileDetailsEndSeperator = "<blockquote";
    
    public $postFileLinkStartSeperator = "href=\"";
    public $postFileLinkEndSeperator = "\"";
    
    public $postThumbLinkStartSeperator = "<img src=\"";
    public $postThumbLinkEndSeperator = "\"";
    
    // needed because of stupid php limitations
    public static function CheckLink($link, $class = __CLASS__)
    {
        return parent::CheckLink($link, $class);
    }
}

?>
