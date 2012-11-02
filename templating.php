<?php
// im sorry for this
// im so so sorry
class TplTable
{
    public $columns = array();
    public $rows = array();
    public $rowstarts = array();
    public $rowends = array();
    public $border = "1";
    public $bordercolor = "#FFCC00";
    public $style = "background-color:#FFFFCC";
    public $width = "1120";
    public $cellpadding = "3";
    public $cellspacing = "3";
    
    public static function CreateTable($columns = array())
    {
        $table = new TplTable();
        $table->columns = $columns;
        return $table;
    }
    
    public function AddRow($row = array(), $rowstart = "", $rowend = "")
    {
        array_push( $this->rows, $row );
        array_push( $this->rowstarts, $rowstart );
        array_push( $this->rowends, $rowend );
    }
    
    public function GetHTML()
    {
        $html = "<table border=\"{$this->border}\" bordercolor=\"{$this->bordercolor}\" style=\"{$this->style}\" width=\"{$this->width}\" cellpadding=\"{$this->cellpadding}\" cellspacing=\"{$this->cellspacing}\">";
        $html .= "\r\n\t<tr>";
        foreach($this->columns as $column)
            $html .= "\r\n\t\t<td>{$column}</td>";
        $html .= "\r\n\t</tr>";
        for($i = 0; $i < count($this->rows); $i++)
        {
            $html .= "\r\n\t<tr>";
            $html .= "\r\n" . $this->rowstarts[$i];
            foreach($this->rows[$i] as $column)
                $html .= "\r\n\t\t<td>{$column}</td>";
            $html .= "\r\n\t" . $this->rowends[$i];
            $html .= "\r\n\t</tr>";
        }
        $html .= "\r\n</table><br />";
        return $html;
    }
}

$addthread = TplTable::CreateTable(array("<b>Add Thread</b>"));
$addthread->width = "650";
$addthread->AddRow(array("Thread URL: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"text\" id=\"url\" name=\"url\" size=\"60\" />"));
$addthread->AddRow(array("Thread Description: <input type=\"text\" id=\"desc\" name=\"desc\" size=\"60\" />", "<input type=\"button\" id=\"addthread\" name=\"add\" value=\"Add\" onclick=\"addThread(document.getElementById('url').value, document.getElementById('desc').value);\"/>"));

$login = TplTable::CreateTable(array("<b>Admin Login</b>"));
$login->width = "450";
$login->AddRow(array("Username: <input type=\"text\" name=\"user\" size=\"20\" />"));
$login->AddRow(array("Password: <input type=\"password\" name=\"pass\" size=\"20\" />", "<input type=\"submit\" name=\"login\" value=\"Login\"/>"));
$login = $login->GetHTML();

$thrinfo = TplTable::CreateTable(array("Thread ID", "Chan", "Board", "Description", "Status", "Last Checked", "Last Post", "Actions"));


?>
