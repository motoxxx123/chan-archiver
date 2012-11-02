<?php

class fourChanThread extends baseThread
{
    public $chanName = "fourChan";
    public $chanFriendlyName = "4chan";
    
    public static $regex = "/(http:\/\/boards\.4chan\.org).*?((?:[a-z][a-z0-9_]*)).*?(res).*?(\d+)/is";
    public static $regexBoardIdx = 2;
    public static $regexThreadIdx = 4;
    
    public $baseURL = "http://4chan.org/";
    public $threadURL = "http://boards.4chan.org/%s/res/%s";
    public $imageURL = "http://images.4chan.org/%s/src/";
    public $thumbURL = "http://1.thumbs.4chan.org/%s/thumb/";
    public $thumbURLSecondary = "http://0.thumbs.4chan.org/%s/thumb/";
    
    public $threadStartSeperator = "";
    public $postStartSeperator = "class=\"postContainer";
    public $postEndSeperator = "</blockquote> </div>";
    
    public $postIDStartSeperator = "title=\"Quote this post\">";
    public $postIDEndSeperator = "</a>";
    
    public $postTimeStartSeperator = "data-utc=\"";
    public $postTimeEndSeperator = "\"";
    public $postTimeFormatted = false;
    public $postTimeYearSwapped = false;
    public $postTimeYearTwoDigit = false;
    
    public $postFileDetailsStartSeperator = "\">File:";
    public $postFileDetailsEndSeperator = "</a></div>";
    
    public $postFileLinkStartSeperator = "<a href=\"";
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
