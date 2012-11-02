<?php

error_reporting( E_ALL );
include "dbWrapper.php";
include "config.php";
include "baseThread.php";

class chanArchiver
{
    public $mysql;
    public $updaterurl = "https://github.com/emoose/4chan-archiver/tarball/master";
    public $compareurl = "https://github.com/emoose/4chan-archiver/compare/";
    public $currentVersion;
    public $latestVersion;
    public $updateAvailable;
    
    public function chanArchiver()
    {
        global $archiver_config;
        foreach($archiver_config['enabled_plugins'] as $key => $value)
        {
            if(!$value)
                continue;
                
            include("plugins/" . $key . ".php");
        }
    }
    
    public function doUpdate()
    {
        $size   = 0;
        $handle = 0;
        if ( file_exists( "version.txt" ) )
        {
            $size   = filesize( "version.txt" );
            $handle = fopen( "version.txt", "r+" );
        }
        if ( !$handle || $size <= 0 )
        {
            $this->currentVersion = $this->getCurrentLatest();
            $this->saveCurrentVersion();
        }
        else
        {
            $this->currentVersion = fread( $handle, $size );
            fclose( $handle );
        }
        $this->latestVersion   = $this->getCurrentLatest();
        $this->updateAvailable = $this->latestVersion != $this->currentVersion;
    }
    
    protected function saveCurrentVersion()
    {
        $handle = fopen( "version.txt", "w+" );
        if ( !$handle )
            die( 'Unable to open version.txt' );
        fwrite( $handle, $this->currentVersion );
        fclose( $handle );
    }
    
    protected function getCurrentLatest()
    {
        $headers = get_headers( $this->updaterurl, 1 );
        $latest  = explode( "filename=", $headers[ 'Content-Disposition' ] );
        $latest  = str_replace( ".tar.gz", "", str_replace( "emoose-4chan-archiver-", "", $latest[ 1 ] ) );
        return $latest;
    }
    
    public function connectDB()
    {
        global $archiver_config;
        if ( !$this->mysql )
        {
            $this->mysql = new DbWrapper( $archiver_config[ 'database_type' ], $archiver_config[ 'database_db' ], $archiver_config[ 'database_user' ], $archiver_config[ 'database_pass' ], $archiver_config[ 'database_host' ] );
            if(!$this->mysql->Connect())
                die( 'Couldn\'t connect: ' . $this->mysql->ErrNo );            
        }
    }
    
    public function closeDB()
    {
        if ( $this->mysql )
        {
            $this->mysql->Close();
        }
    }
    
    public static function getSource( $url )
    {
        if ( ( $source = @file_get_contents( $url ) ) == false )
            return false;
        return $source;
    }
    
    public static function downloadFile( $url, $location, $referer = "" )
    {
        $file = "";
        if ( ( $handle = @fopen( $url, "r" ) ) )
        {
            if(!empty( $referer ))
                fwrite( $handle, "Referer: {$referer}\r\n" );
            while ( $line = fread( $handle, 8192 ) )
                $file .= $line;
            fclose( $handle );
            self::writeFile( $file, $location );
        }
    }
    
    public static function writeFile( $data, $location )
    {
        if ( ( $handle = fopen( $location, "w+" ) ) )
        {
            fwrite( $handle, $data );
            fclose( $handle );
            return true;
        }
        return false;
    }
    
    public static function createZip($files = array(), $basepath = '', $destination = '', $overwrite = false)
    {
        //if the zip file already exists and overwrite is false, return false
        if( file_exists( $destination ) && !$overwrite )
            return false;
            
        $current = getcwd();
        if( !empty( $basepath ) )
            chdir( $basepath );
            
        $valid_files = array();
        if( is_array( $files ) )
            foreach( $files as $file )
                if( file_exists( $file ) )
                    $valid_files[] = $file;
            
        if(count($valid_files))
        {
            $zip = new ZipArchive();
            if( $zip->open( $destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE ) !== true )
                return false;
             
            // add the files
            foreach( $valid_files as $file )
                $zip->addFile( $file, $file );
                
            //close the zip -- done!
            $zip->close();
            
            //check to make sure the file exists
            chdir($current);
            return file_exists( $destination );
        }
        else
        {
            chdir($current);
            return false;
        }
    }
    
    public static function rrmdir( $dir )
    {
        foreach ( glob( $dir . '/*' ) as $file )
        {
            if ( is_dir( $file ) )
                self::rrmdir( $file );
            else
                unlink( $file );
        }
        rmdir( $dir );
    }
    
    private function CreateThreadInstanceFromID( $threadid )
    {
        global $archiver_config;
        $query = $this->mysql->Query( "SELECT * FROM Threads WHERE ID = '{$threadid}'" );
        if ( !$query || $this->mysql->NumRows($query) <= 0 )
            return false;
            
        $obj = $this->mysql->FetchObject($query);
        
        if(!$archiver_config['enabled_plugins'][$obj->Chan])
            return false;
                
        $thread = null;
        eval('$thread = new ' . $obj->Chan . 'Thread();');            
        if($thread == null)
            return false;
        $thread->arch = $this;
        $this->mysql->CloseResult($query);
        $thread->LoadFromID( $threadid );
        return $thread;
    }

    public function CreateThreadZipByID( $threadid )
    {
        $thread = $this->CreateThreadInstanceFromID( $threadid );
        if(!$thread)
            return "unable to locate thread {$threadid} in database<br />\r\n";
        return $this->CreateThreadZip( $thread );
    }
    
    public function CreateThreadZip( $thread )
    {
        $result = $thread->CreateZip();
        if( !$result ) // 
            return "Unable to zip thread {$thread->ThreadID} ({$thread->chanFriendlyName} - /{$thread->Board}/) {$thread->MaybeGetQuotedDesc()}<br />\r\n";
        return "Created zip for thread {$thread->ThreadID} ({$thread->chanFriendlyName} - /{$thread->Board}/) {$thread->MaybeGetQuotedDesc()}<br />\r\n";
    }
    
    public function IsThreadZipped( $threadid )
    {
        $thread = $this->CreateThreadInstanceFromID( $threadid );
        if(!$thread)
            return "unable to locate thread {$threadid} in database<br />\r\n";
        
        return $thread->IsZipped();
    }
    
    public function UpdateThreadDescriptionByID( $threadid, $description = "" )
    {
        $thread = $this->CreateThreadInstanceFromID( $threadid );
        if(!$thread)
            return "unable to locate thread {$threadid} in database<br />\r\n";
        return $this->UpdateThreadDescription( $thread, $description );
    }

    public function UpdateThreadDescription( $thread, $description = "" )
    {
        $thread->Description = $description;
        $thread->CreateFolders();
        if( !empty( $description ) )
            self::writeFile($description, $thread->GetThreadLocalPath() . "/description.txt");
        else if( file_exists( $thread->GetThreadLocalPath() . "/description.txt" ) )
            unlink( $thread->GetThreadLocalPath() . "/description.txt" );
            
        if(!$thread->Save())
            return "Unable to update thread {$thread->ThreadID} ({$thread->chanFriendlyName} - /{$thread->Board}/) {$thread->MaybeGetQuotedDesc()}<br />\r\n";
        return "Updated thread {$thread->ThreadID} ({$thread->chanFriendlyName} - /{$thread->Board}/) {$thread->MaybeGetQuotedDesc()}<br />\r\n";
    }
    
    public function UpdateThreadByID( $threadid )
    {
        $thread = $this->CreateThreadInstanceFromID( $threadid );
        if(!$thread)
            return false;
        if(time() - $thread->LastChecked < $thread->CheckDelay)
            return "";
        return $thread->CheckThread();
    }
    
    public function CheckThreads( $checktime, $board = "" )
    {
        $threads = $this->GetThreads("Status", "1");
        $return = "";
        foreach($threads as $thread)
        {
            if ( $checktime && time() - $thread->LastChecked < $thread->CheckDelay )
                continue;            
            if ( !empty($board) && $thread->Chan != $board )
                continue;

            $return .= $thread->CheckThread();
        }
        return $return;
    }
    
    public function ClearThreads()
    {
        $threads = $this->GetThreads("Status", "0");
        $return = "";
        foreach($threads as $thread)
        {
            if($thread->Status != 0)
                continue;
            $return .= $this->RemoveThread($thread);
        }
        return $return;
    }

    public function AddThread( $url, $description = "" )
    {
        global $archiver_config;
        $thread = null;
        // fix up for bookmarklet
        $url = str_replace( "http---", "http://", $url );
        $url = str_replace( "https---", "https://", $url );
        // fix up for regex's
        $url = str_replace( "http://www.", "http://", $url );
        $url = str_replace( "https://", "http://", $url ); // HACKY HACKY HACK HACK HACK

	
        foreach($archiver_config['enabled_plugins'] as $key => $value)
        {
            if(!$value)
                continue;
            $compat = false;
            eval('$compat = ' . $key . 'Thread::checkLink($url);');
            if(!$compat)
                continue;
            eval('$thread = new ' . $key . 'Thread();');            
            if($thread == null)
                continue;
                
            $thread->arch = $this;
            $thread->ParseLink($url);
            if($thread->Load($thread->ThreadID, $thread->Board, $thread->Chan))
                return "Thread {$thread->ThreadID} ({$thread->chanFriendlyName} - /{$thread->Board}/) {$thread->MaybeGetQuotedDesc()} already exists in the database!<br />\r\n";

            $thread->Description = $description;
            $thread->CreateFolders();
            if( !empty( $description ) )
                self::writeFile($description, $thread->GetThreadLocalPath() . "/description.txt");
            else if( file_exists( $thread->GetThreadLocalPath() . "/description.txt" ) )
                unlink( $thread->GetThreadLocalPath() . "/description.txt" );
            $thread->Save();
            return "Added thread {$thread->ThreadID} ({$thread->chanFriendlyName} - /{$thread->Board}/) {$thread->MaybeGetQuotedDesc()}<br />\r\n";
        }
        return "Invalid thread, perhaps a plugin isn't enabled?<br />\r\n";
    }
    
    public function RemoveThreadByID( $threadid, $deletefiles = 0 )
    {
        $thread = $this->CreateThreadInstanceFromID( $threadid );
        if(!$thread)
            return "Error locating thread {$threadid} in database<br />\r\n";
        return $this->RemoveThread( $thread, $deletefiles );
    }

    public function RemoveThread( $thread, $deletefiles = 0 )
    {
        if($thread->Delete($deletefiles))
            return "Removed thread {$thread->ThreadID} ({$thread->chanFriendlyName} - /{$thread->Board}/) {$thread->MaybeGetQuotedDesc()}<br />\r\n";
        else
            return "Error removing thread {$thread->ThreadID} ({$thread->chanFriendlyName} - /{$thread->Board}/) {$thread->MaybeGetQuotedDesc()}<br />\r\n";
    }
    
    public function GetOngoingThreadCount()
    {
        $query = $this->mysql->Query( "SELECT * FROM Threads WHERE Status = '1'" );
        if ( !$query )
            die( 'Could not query database: ' . $this->mysql->ErrNo );
        
        $num = $this->mysql->NumRows( $query );
        $this->mysql->CloseResult( $query );
        return $num;
    }

    public function GetEndedThreadCount()
    {
        $query = $this->mysql->Query( "SELECT * FROM Threads WHERE Status = '0'" );
        if ( !$query )
            die( 'Could not query database: ' . $this->mysql->ErrNo );
        
        $num = $this->mysql->NumRows( $query );
        $this->mysql->CloseResult( $query );
        return $num;
    }

    public function GetThreads($method = "", $query = "")
    {
        if(empty($query) || empty($method))
            $query = $this->mysql->Query( "SELECT * FROM Threads ORDER BY Chan, Board, TimeAdded, ID ASC" );
        else
            $query = $this->mysql->Query( "SELECT * FROM Threads WHERE {$method} = '{$query}' ORDER BY Chan, Board, TimeAdded, ID ASC" );
            
        if ( !$query )
            die( 'Could not query database: ' . $this->mysql->ErrNo );
            
        $threads = array();
        while ( $thr = $this->mysql->FetchObject( $query ) )
        {
            $thread = $this->CreateThreadInstanceFromID( $thr->ID );
            if(!$thread)
                continue;
            array_push($threads, $thread);
        }
        $this->mysql->CloseResult( $query );
        return $threads;
    }
}

?>
