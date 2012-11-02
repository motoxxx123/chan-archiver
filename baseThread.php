<?php

class baseThread
{
    public $chanName;
    public $chanFriendlyName;
    
    public static $regex;
    public static $regexBoardIdx;
    public static $regexThreadIdx;
    
    public $baseURL;
    public $threadURL;
    public $imageURL;
    public $thumbURL;
    public $thumbURLSecondary;
    
    public $threadStartSeperator;
    public $postStartSeperator;
    public $postEndSeperator;
    
    public $postIDStartSeperator;
    public $postIDEndSeperator;
    
    public $postTimeStartSeperator;
    public $postTimeEndSeperator;
    public $postTimeFormatted = true;
    public $postTimeYearSwapped = false;
    public $postTimeYearTwoDigit = false;
    
    public $postFileDetailsStartSeperator;
    public $postFileDetailsEndSeperator;
    
    public $postFileLinkStartSeperator;
    public $postFileLinkEndSeperator;
    
    public $postThumbLinkStartSeperator;
    public $postThumbLinkEndSeperator;
    
    public $ID;
    public $ThreadID = 0;
    public $Board = "";
    public $Chan = "";
    public $Description = "";
    public $Status = 1;
    public $LastChecked = 0;
    public $TimeAdded = 0;
    public $CheckDelay = 30;
    public $FirstImageFilename = "";
    public $Posts = array();
    
    public $arch;
    
    private function loadFromQuery($query)
    {
        $obj = $this->arch->mysql->FetchObject($query);
        $this->ID = $obj->ID;
        $this->ThreadID = $obj->ThreadID;
        $this->Board = $obj->Board;
        $this->Chan = $obj->Chan;
        $this->Description = $obj->Description;
        $this->Status = $obj->Status;
        $this->LastChecked = $obj->LastChecked;
        $this->TimeAdded = $obj->TimeAdded;
        $this->CheckDelay = $obj->CheckDelay;
        $this->FirstImageFilename = $obj->FirstImageFilename;
        $this->arch->mysql->CloseResult($query);
        $this->loadSubClasses();
        return true;
    }

    private function loadSubClasses()
    {
        $this->Posts = array();
        $query = $this->arch->mysql->Query( "SELECT * FROM `Posts` WHERE `ThreadID` = '{$this->ID}' ORDER BY `PostTime` DESC" );
        if(!$query || $this->arch->mysql->NumRows($query) <= 0)
            return false;
            
        while($row = $this->arch->mysql->FetchObject($query))
        {
            $post = new basePost();
            $post->ID = $row->ID;
            $post->PostID = $row->PostID;
            $post->ThreadID = $row->ThreadID;
            $post->PostTime = $row->PostTime;
            array_push($this->Posts, $post);
        }
        $this->arch->mysql->CloseResult($query);
    }
    
    private function saveSubClasses()
    {
        $query = null;
        foreach($this->Posts as $post)
        {
            $post->ThreadID = $this->ID;
            if(!$post->ID)
            { // new post to add
                $query = $this->arch->mysql->Query( "INSERT INTO `Posts` ( `ID`, `ThreadID`, `PostID`, `PostTime` ) VALUES ( NULL, '{$post->ThreadID}', '{$post->PostID}', '{$post->PostTime}' )");
                if($query)
                    $post->ID = $this->arch->mysql->LastInsertId();
                else
                    return false;
            }
            else
            {
                $query = $this->arch->mysql->Query( "UPDATE `Posts` SET `ThreadID` = '{$post->ThreadID}', `PostID` = '{$post->PostID}', `PostTime` = '{$post->PostTime}' WHERE `ID` = '{$post->ID}'");
                if(!$query)
                    return false;
            }
        }
        return true;
    }

    public function Load($threadid, $board, $chan)
    {
        $query = $this->arch->mysql->Query( "SELECT * FROM `Threads` WHERE `ThreadID` = '{$threadid}' AND `Board` = '{$board}' AND `Chan` = '{$chan}'" );
        if(!$query || $this->arch->mysql->NumRows($query) <= 0)
            return false;
            
        return $this->loadFromQuery($query);
    }
    
    public function LoadFromID($id)
    {
        $query = $this->arch->mysql->Query( "SELECT * FROM `Threads` WHERE `ID` = '{$id}'" );
        if(!$query || $this->arch->mysql->NumRows($query) <= 0)
            return false;
            
        return $this->loadFromQuery($query);
    }
    
    public function AddPost($postid, $posttime)
    {
        $post = new basePost();
        $post->ThreadID = $this->ID;
        $post->PostID = $postid;
        $post->PostTime = $posttime;
        array_push($this->Posts, $post);
    }
    
    public function MaybeGetQuotedDesc()
    {
        return !empty($this->Description) ? "\"{$this->Description}\"" : "";
    }
    
    public function Delete( $deletefiles = 0 )
    {
        if(!$this->ID)
            return false;
        
        $this->ClearPosts();
        $this->arch->mysql->Query( "DELETE FROM `Threads` WHERE `ID` = '{$this->ID}'" );
        
        if ( $deletefiles )
        {
            if(is_dir( $this->GetThreadLocalPath() . "/" ))
                chanArchiver::rrmdir( $this->GetThreadLocalPath() . "/" );
            if(file_exists( $this->GetThreadLocalPath() . ".html" ))
                unlink( $this->GetThreadLocalPath() . ".html" );
        }
        return true;
    }
    public function ClearPosts()
    {
        $this->Posts = array();
        $this->arch->mysql->Query( "DELETE FROM `Posts` WHERE `ThreadID` = '{$this->ID}'" );
    }
    
    public function Save()
    {
        $query = null;
        if(!$this->ID)
        { // new thread to add
            $this->TimeAdded = time();
            $query = $this->arch->mysql->Query( "INSERT INTO `Threads` ( `ID`, `ThreadID`, `Board`, `Chan`, `Description`, `Status`, `LastChecked`, `TimeAdded`, `CheckDelay`, `FirstImageFilename` ) VALUES ( NULL, '{$this->ThreadID}', '{$this->Board}', '{$this->Chan}', '{$this->Description}', '{$this->Status}', '{$this->LastChecked}', '{$this->TimeAdded}', '{$this->CheckDelay}', '{$this->FirstImageFilename}' )");
            if($query)
                $this->ID = $this->arch->mysql->LastInsertId();
            else
                return false;
        }
        else
        {
            $query = $this->arch->mysql->Query( "UPDATE `Threads` SET `ThreadID` = '{$this->ThreadID}', `Board` = '{$this->Board}', `Chan` = '{$this->Chan}', `Description` = '{$this->Description}', `Status` = '{$this->Status}', `LastChecked` = '{$this->LastChecked}', `TimeAdded` = '{$this->TimeAdded}', `CheckDelay` = '{$this->CheckDelay}', `FirstImageFilename` = '{$this->FirstImageFilename}' WHERE `ID` = '{$this->ID}'");
            if(!$query)
                return false;
        }
        return $this->saveSubClasses();
    }
    
    public static function CheckLink($link, $class = __CLASS__)
    {
        $reg = new ReflectionClass($class);
        return $c = preg_match_all( $reg->getStaticPropertyValue( 'regex' ), $link, $matches );
    }
    
    public function ParseLink($link)
    {
        $reg = new ReflectionClass( get_class( $this ) );
        if( $c = preg_match_all( $reg->getStaticPropertyValue( 'regex' ), $link, $matches ) )
        {
            $this->ThreadID = $matches[ $reg->getStaticPropertyValue( 'regexThreadIdx' ) ][ 0 ];
            $this->Board = $matches[ $reg->getStaticPropertyValue( 'regexBoardIdx' ) ][ 0 ];
            $this->Chan = $this->chanName;
            return true;
        }
        return false;
    }
    
    public function GetThreadURL()
    {
        return sprintf( $this->threadURL, $this->Board, $this->ThreadID );
    }
    
    public function GetThreadLocalURL()
    {
        global $archiver_config;
        return "{$archiver_config['pubstorage']}{$this->chanFriendlyName}/{$this->Board}/{$this->ThreadID}";
    }
    
    public function GetThreadLocalPath()
    {
        global $archiver_config;
        return "{$archiver_config['storage']}{$this->chanFriendlyName}/{$this->Board}/{$this->ThreadID}";
    }
    
    public function GetZipPath($local = false)
    {
        return $local ? $this->GetThreadLocalPath() . ".zip" : $this->GetThreadLocalURL() . ".zip";
    }
    
    public function GetLatestPostTime()
    {
        if(count($this->Posts) <= 0)
            return 0;
        $latestpost = $this->Posts[0];
        return $latestpost->PostTime;
    }
    public function ParsePost($post, $postarr, $pagedata)
    {
        $id   = explode( $this->postIDStartSeperator, $post );
        $id   = explode( $this->postIDEndSeperator, $id[ 1 ] );
        $id   = $id[ 0 ];
        if ( in_array( $id, $postarr ) )
            return array(0);

        $posttime = explode( $this->postTimeStartSeperator, $post );
        $posttime = explode( $this->postTimeEndSeperator, $posttime[ 1 ] );
        $posttime = $posttime[ 0 ];

        // fix up post times
        $posttime = str_replace('/', '-', $posttime);

        // parse the post time if we have to
        if($this->postTimeFormatted)
        {
            if($this->postTimeYearTwoDigit) // shouldn't have to change the 20 for another hundred years or so
                if($this->postTimeYearSwapped)
                    $posttime = "20" . $posttime;
                else
                    $posttime = substr($posttime, 0, 6) . "20" . substr($posttime, 6, strlen($posttime) - 6);

            $posttime = strtotime($posttime);
        }

        $files = explode( $this->postFileDetailsStartSeperator, $post );
        
        $newimages = 0;
        for ( $y = 1; $y < count( $files ); $y++ )
        {
            $file     = explode( $this->postFileDetailsEndSeperator, $files[ $y ] );
            $file     = $file[ 0 ];
            
            $fileurl = explode( $this->postFileLinkStartSeperator, $file );
            $fileurl = explode( $this->postFileLinkEndSeperator, $fileurl[ 1 ] );
            $fileurl = $fileurl[ 0 ];
            
            if(substr($fileurl, 0, 1) == "/" && substr($fileurl, 1, 1) != "/")
                $fileurl  = sprintf($this->imageURL, $this->Board) . str_replace($this->Board . "/", "", $fileurl);
            if(substr($fileurl, 0, 4) != "http")
                $fileurl  = "http:" . $fileurl;
            
            if ( count( $postarr ) <= 0 && $y == 1 )
                $this->FirstImageFilename = basename( $fileurl );

            $thumurl = explode( $this->postThumbLinkStartSeperator, $file );
            $thumurl = explode( $this->postThumbLinkEndSeperator, $thumurl[ 1 ] );
            $thumurl = $thumurl[ 0 ];
            
            if(substr($thumurl, 0, 1) == "/" && substr($thumurl, 1, 1) != "/")
                $thumurl  = sprintf($this->thumbURL, $this->Board) . str_replace($this->Board . "/", "", $thumurl);
            if(substr($thumurl, 0, 4) != "http")
                $thumurl  = "http:" . $thumurl;
            
            $filestor    = $this->GetThreadLocalPath() . "/" . basename( $fileurl );
            $thumstor    = $this->GetThreadLocalPath() . "/thumbs/" . basename( $thumurl );
            $pubfilestor = $this->GetThreadLocalURL() . "/" . basename( $fileurl );
            $pubthumstor = $this->GetThreadLocalURL() . "/thumbs/" . basename( $thumurl );
            
            chanArchiver::writeFile( "in progress", $filestor . ".tmp" );            
            chanArchiver::downloadFile( $fileurl, $filestor, $this->baseURL );
            unlink( $filestor . ".tmp" );
            
            chanArchiver::writeFile( "in progress", $thumstor . ".tmp" );
            chanArchiver::downloadFile( $thumurl, $thumstor, $this->baseURL );
            unlink( $thumstor . ".tmp" );
            
            $pagedata = str_replace( $fileurl, $this->ThreadID . "/" . basename( $fileurl ), $pagedata );
            $pagedata = str_replace( $thumurl, $this->ThreadID . "/thumbs/" . basename( $thumurl ), $pagedata );
            $newimages++;
        }
        
        return array($id, $posttime, $newimages, $pagedata);
    }
    
    public function CreateFolders()
    {
        global $archiver_config;
        if ( is_dir( $archiver_config['storage'] ) === FALSE )
        {
            mkdir( $archiver_config['storage'], 0777, true );
            chanArchiver::writeFile( $archiver_config['php_chans'], $archiver_config['storage'] . "index.php" );
        }
        if ( is_dir( "{$archiver_config['storage']}{$this->chanFriendlyName}/" ) === FALSE )
        {
            mkdir( "{$archiver_config['storage']}{$this->chanFriendlyName}/", 0777, true );
            chanArchiver::writeFile( $archiver_config['php_boards'], "{$archiver_config['storage']}{$this->chanFriendlyName}/index.php" );
        }
        if ( is_dir( "{$archiver_config['storage']}{$this->chanFriendlyName}/{$this->Board}/" ) === FALSE )
        {
            mkdir( "{$archiver_config['storage']}{$this->chanFriendlyName}/{$this->Board}/", 0777, true );
            chanArchiver::writeFile( sprintf($archiver_config['php_threads'], $this->Board), "{$archiver_config['storage']}{$this->chanFriendlyName}/{$this->Board}/index.php" );
        }
        if ( is_dir( $this->GetThreadLocalPath() . "/thumbs/" ) === FALSE )
            mkdir( $this->GetThreadLocalPath() . "/thumbs/", 0777, true );
    }
    
    public function IsZipped()
    {
        return file_exists( $this->GetZipPath( true ) );
    }
    
    public function CreateZip()
    {
        if( !$this->IsZipped() )
        {
            $files = array();
            if ( $handle = opendir( $this->GetThreadLocalPath() ) )
            {
                while ( false !== ( $entry = readdir( $handle ) ) )
                    if ($entry != "." && $entry != "..")
                        array_push( $files, $entry );
                
                closedir( $handle );
            }
            if(!chanArchiver::createZip( $files, $this->GetThreadLocalPath() . "/", $this->GetZipPath( true ), true ))
                return false;
        }
        return true;
    }
    
    public function CheckThread($firstonly = false)
    {
        $time = microtime(true);
        $postarr  = array();
        foreach ($this->Posts as $post)
            array_push( $postarr, $post->PostID );

        $data = chanArchiver::getSource( $this->GetThreadURL() );//$this->getSource( $url );
        if ( !$data ) // must have 404'd
        {
            $this->Status = 0;
            $this->ClearPosts();
            $this->Save();
            return sprintf( "Checked %s (%s - /%s/) %s at %s, thread 404'd<br />\r\n", $this->ThreadID, $this->chanFriendlyName, $this->Board, $this->MaybeGetQuotedDesc(), time() );
        }

        // remove newlines as it makes everything much easier to work with
        $fixeddata = str_replace( "\r\n", "", $data );
        $fixeddata = str_replace( "\n", "", $fixeddata );

        // change https to http, might fuck up stuff but meh
        $fixeddata = str_replace( "https://", "http://", $fixeddata );

        // fix up urls
        $fixeddata = str_replace( "=\"//", "=\"http://", $fixeddata );
        $fixeddata = str_replace( "=\"/", "=\"" . $this->baseURL, $fixeddata );
        $fixeddata = str_replace( "\"" . $this->ThreadID . "#", "\"" . $this->ThreadID . ".html#", $fixeddata );

        // wtf is rocketscript, fix that shit
        $fixeddata = str_replace( "text/rocketscript", "text/javascript", $fixeddata );
        $fixeddata = str_replace( "data-rocketsrc", "src", $fixeddata );

        // remove day from post time
        $fixeddata = str_replace("(Mon)", " ", $fixeddata);
        $fixeddata = str_replace("(Tue)", " ", $fixeddata);
        $fixeddata = str_replace("(Wed)", " ", $fixeddata);
        $fixeddata = str_replace("(Thu)", " ", $fixeddata);
        $fixeddata = str_replace("(Fri)", " ", $fixeddata);
        $fixeddata = str_replace("(Sat)", " ", $fixeddata);
        $fixeddata = str_replace("(Sun)", " ", $fixeddata);
        $fixeddata = str_replace("Mon,", "", $fixeddata);
        $fixeddata = str_replace("Tue,", "", $fixeddata);
        $fixeddata = str_replace("Wed,", "", $fixeddata);
        $fixeddata = str_replace("Thu,", "", $fixeddata);
        $fixeddata = str_replace("Fri,", "", $fixeddata);
        $fixeddata = str_replace("Sat,", "", $fixeddata);
        $fixeddata = str_replace("Sun,", "", $fixeddata);

        // remove analytics bullcrap, no point in them counting our archived threads visits
        $fixeddata = str_replace("google-analytics", "cockblocked", $fixeddata);
        
        $this->CreateFolders();
            
        $newposts = 0;
        $newimages = 0;
        
        if( !empty( $this->threadStartSeperator ) )
        {   // if for some reason this boards formatting is fucked and OP has to be seperated seperately...
            $thread = explode( $this->threadStartSeperator, $fixeddata );
            $thread = explode( $this->postEndSeperator, $thread[ 1 ] );
            $thread = $thread[ 0 ];
            
            $thread = $this->ParsePost($thread, $postarr, $fixeddata);
            if($thread[ 0 ] != 0)
            {
                $this->AddPost($thread[ 0 ], $thread[ 1 ]);
                $newimages += $thread[ 2 ];
                $newposts++;
                $fixeddata = $thread[ 3 ];
            }
        }
        
        if( (!empty( $this->threadStartSeperator ) && !$firstonly) || empty( $this->threadStartSeperator ) )
        {
            $posts = explode( $this->postStartSeperator, $fixeddata );
            for ( $i = 1; $i < count( $posts ); $i++ )
            {
                $post = explode( $this->postEndSeperator, $posts[ $i ] );
                $post = $post[ 0 ];
                
                $post = $this->ParsePost($post, $postarr, $fixeddata);
                if($post[0] == 0)
                    continue;
                    
                $this->AddPost($post[ 0 ], $post[ 1 ]);
                $newimages += $post[ 2 ];
                $newposts++;
                $fixeddata = $post[ 3 ];
                if($firstonly)
                    break; // only download one
            }
        }
        // fix for posts we've already downloaded
        if(!empty($this->thumbURL))
            $fixeddata = str_replace( sprintf( $this->thumbURL, $this->Board ), $this->ThreadID . "/thumbs/", $fixeddata );
        
        if(!empty($this->thumbURLSecondary))
            $fixeddata = str_replace( sprintf( $this->thumbURLSecondary, $this->Board ), $this->ThreadID . "/thumbs/", $fixeddata );
        
        if(!empty($this->imageURL))
            $fixeddata = str_replace( sprintf( $this->imageURL, $this->Board ), $this->ThreadID . "/", $fixeddata );
        
        // if we have new images and a zip, delete the zip
        if($newimages > 0 && file_exists( $this->GetThreadLocalURL() . "/" . $this->ThreadID . ".zip" ) )
            unlink( $this->GetThreadLocalURL() . "/" . $this->ThreadID . ".zip" );
            
        // thread is done
        if($newposts == 0)
        {
            if($this->CheckDelay < 300) // 5 mins max
                $this->CheckDelay = $this->CheckDelay + 10; // add 10 seconds as per recommended 4chan spec
        }
        else
            $this->CheckDelay = 30; // default

        $this->LastChecked = time();
        chanArchiver::writeFile( $fixeddata, $this->GetThreadLocalPath() . ".html" );
        $this->Save();
        $time = microtime(true) - $time;

        return sprintf( "Checked %s (%s - /%s/) %s at %s, added %s images and %s posts<br />\r\n", $this->ThreadID, $this->chanFriendlyName, $this->Board, $this->MaybeGetQuotedDesc(), time(), $newimages, $newposts, $time );

    }
    
}

class basePost
{
    public $ID;
    public $ThreadID = 0;
    public $PostID = 0;
    public $PostTime = 0;
}

?>
