<?php

/**
 * @author lolkittens
 * @copyright 2012
 */

class DbWrapper
{
    public $Type = "mysql";
    public $Username = "root";
    public $Password = "dsfargeg";
    public $Host = "localhost";
    public $Database = "chanarchiver";
    public $ErrNo;
    private $handle;
    private $connected = false;
    function __construct($type, $database, $username, $password, $host)
    {
        $this->Type = $type;
        $this->Database = $database;
        $this->Username = $username;
        $this->Password = $password;
        $this->Host = $host;
    }
    public function Connect()
    {
        switch(strtolower($this->Type))
        {
            case "mysql":
                $this->handle = new mysqli($this->Host, $this->Username, $this->Password, $this->Database);
                if($this->handle->connect_errno)
                {
                    $this->ErrNo = $this->handle->connect_errno;
                    return false;
                }
                break;
            case "sqlite":
                $this->handle = new SQLiteDatabase($this->Database);
                if(!$this->handle)
                    return false;
                break;
        }
        $this->connected = true;
        return true;
    }
    
    public function Query($query)
    {
        if(!$this->connected)
            return false;
        switch(strtolower($this->Type))
        {
            case "mysql":
                if(!($result = $this->handle->query($query)))
                {
                    $this->ErrNo = $this->handle->errno;
                    return false;
                }
                return $result;
                break;
            case "sqlite":
                $query = str_replace("PRIMARY KEY (ID)", "", $query);
                $query = str_replace("AUTO_INCREMENT", "PRIMARY KEY", $query);
                if(!($result = $this->handle->query($query)))
                    return false;
                return $result;
                break;
        }

        return false;
    }
    public function CloseResult($query)
    {
        if(!$this->connected)
            return false;
        switch(strtolower($this->Type))
        {
            case "mysql":
                $query->close();
                break;
            case "sqlite":
                break;
        }
        return true;
    }
    public function NumRows($query)
    {
        if(!$this->connected)
            return false;
        switch(strtolower($this->Type))
        {
            case "mysql":
                return $query->num_rows;
                break;
            case "sqlite":
                return $query->numRows();
                break;
        }
        return false;
    }
    public function LastInsertId()
    {
        if(!$this->connected)
            return false;
        switch(strtolower($this->Type))
        {
            case "mysql":
                return $this->handle->insert_id;
                break;
            case "sqlite":
                return $this->handle->lastInsertRowid();
                break;
        }
        return false;
    }
    public function FetchArray($query)
    {
        if(!$this->connected)
            return false;
        switch(strtolower($this->Type))
        {
            case "mysql":
                return $query->fetch_array();
                break;
            case "sqlite":
                $query->fetchAll();
                return $query->fetch();
                break;
        }
        return false;
    }
    
    public function FetchObject($query)
    {
        if(!$this->connected)
            return false;
        switch(strtolower($this->Type))
        {
            case "mysql":
                return $query->fetch_object();
                break;
            case "sqlite":
                $query->fetchAll();
                return $query->fetchObject();
                break;
        }
        return false;
    }
    public function Close()
    {
        if(!$this->connected)
            return false;
        switch(strtolower($this->Type))
        {
            case "mysql":
                $this->handle->close();
                return true;
                break;
            case "sqlite":
                return true;
                break;
        }
        return false;
    }
}

?>