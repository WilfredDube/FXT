<?php
// include('password.php');
class Tool {
	protected static $table_name = "tool_library";
  public $toolID;
  public $toolName;
  public $toolAngles;
  public $toolCaption;

  private static $attr;

	function __construct() {

	}

  public function getAllTool (){
    global $database;

    $query = "SELECT * FROM ".self::$table_name;
    $result_set = $database->getAllRows($query);

    return $result_set;
  }

	public static function findToolByAngle($angle) {
		global $database;
		// select concat('TL', toolid) as toolid, toolname from tool_library;
		$query = "SELECT concat('TL', toolid) as toolid FROM ".self::$table_name." WHERE toolangles like \"%$angle%\"";
		// echo "$query";
		// echo "$angle";
		$result_set = $database->getAllRows($query, []);
		return $result_set;
	}

  public function save() {
    if ($this->toolID)
    {
      self::$attr = array($this->toolName, $this->toolAngles, $this->toolCaption, $this->toolID);
      $this->updateTool();
    } else {
      self::$attr = array($this->toolName, $this->toolAngles, $toolCaption);
      $this->createTool();
    }
  }

  public function createTool() {
    global $database;

    $query = "INSERT INTO ".self::$table_name." (toolname, toolangles, toolcaption) VALUES (?,?,?)";
    if ($database->insertRow($query, self::$attr)) {
      $query = "SELECT toolid FROM ".self::$table_name." WHERE toolname = ?";
      $this->toolID = $database->insert_id($query, 'toolid', [$this->toolName]);
      echo "Successful added";
    }
  }

  public function updateTool() {
    global $database;

    $query = "UPDATE ".self::$table_name." SET toolname = ?, toolangles = ?, toolcaption = ? WHERE toolid = ?";
    if ($database->updateRow($query, self::$attr))
      echo "Successful added";
  }

  public function deleteTool() {
    global $database;

    $query = "DELETE FROM ".self::$table_name." WHERE toolid=?";
    if ($database->deleteRow($query, [$this->toolID]))
      echo "Successfully deleted";
  }
}

$tool = new Tool();


?>
