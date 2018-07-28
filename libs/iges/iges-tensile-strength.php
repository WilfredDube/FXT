<?php
// include('password.php');
class TStrength {
	protected static $table_name = "tstrength";
  public $material;
  public $tstrength;
	// private $_db;

	function __construct() {
		// parent::__construct();
		// $this->_db = $db;
	}

	public function getMaterialByID($id) {
		global $database;
		$query = "SELECT * FROM ".self::$table_name." where m_id= ?";
		$res = $database->getRow($query, [$id]);
		return (is_array($res) && !empty($res)) ? array_shift($res) : $res;
	}

	public function getMaterialID($material) {
    global $database;
		$query = "SELECT m_id FROM ".self::$table_name." where material= ?";
    $id = $database->getRow($query, [$material]);
		return $id['m_id'];
	}

	public function getMaterialName($id) {
    global $database;

		$query = "SELECT material FROM ".self::$table_name." where m_id=?";
    $material = $database->getRow($query, [$id]);

		return $material['material'];
	}

	public function getMaterialStrength($id) {
    global $database;
		$TS = null;

		$query = "SELECT tensile_strength FROM ".self::$table_name." where m_id=?";
    $TS = $database->getRow($query, [$id]);

		return $TS['tensile_strength'];
	}

  public function insertMAterial() {
    global $database;

    $query = "INSERT INTO ".self::$table_name." (material, tensile_strength) VALUES (?,?)";
    if ($database->insertRow($query, [$this->material, $this->tstrength]))
      echo "Successful added";
  }

  public function deleteMAterial() {
    global $database;

    $query = "DELETE FROM ".self::$table_name." WHERE m_id=?";
    if ($database->deleteRow($query, [$this->getMaterialID($this->material)]))
      echo "Successfully deleted";
  }
}

?>
