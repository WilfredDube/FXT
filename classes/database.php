<?php
// require_once('../includes/config.php');

class Database extends PDO {
	public $isConnected;
	protected $db;

	function __construct() {
		//parent::__construct();
		$this->open_connection();
	}

	public function open_connection() {
		$this->isConnected = true;

		try {
			// create PDO connection
			$this->db = new PDO ( "mysql:host=".DBHOST.";dbname=".DBNAME.";charset=utf8", DBUSER, DBPASS );
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		} catch ( PDOException $e ) {
			$this->isConnected = false;
			// show error
			echo '<p class="bg-danger">' . $e->getMessage () . '</p>';
			exit ();
		}

		// echo get_class($this->db)."\n";
	}

	public function close_connection() {
		$this->database = NULL;
		$this->isConnected = FALSE;
	}

	public function getRow($query, $params = []) {
		try {
			$statement = $this->db->prepare($query);

			$statement->execute($params);

			return $statement->fetch();
		} catch (PDOException $e) {
			echo '<p class="bg-danger">' . $e->getMessage () . '</p>';
			exit ();
		}

	}

	public function getAllRows($query, $params = []) {
		try {
			$statement = $this->db->prepare($query);

			$statement->execute($params);

			return $statement->fetchAll();

		} catch (PDOException $e) {
			echo '<p class="bg-danger">' . $e->getMessage () . '</p>';
			exit ();
		}

	}

	public function count ($query) {
		try {
			$statement = $this->db->prepare($query);

			$statement->execute();

			return $statement->rowCount();
		} catch (PDOException $e) {
			echo '<p class="bg-danger">' . $e->getMessage () . '</p>';
			exit ();
		}

	}

	private function exeCRUD($query, $params = []) {
		try {
			$statement = $this->db->prepare($query);

			$statement->execute($params);
			// return affected rows
			return $statement->rowCount();
			// $statement->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
			// $result = $statement->fetch();
			// return $result;

		} catch (PDOException $e) {
				echo '<p class="bg-danger">' . $e->getMessage () . '</p>';
				exit ();
		}
	}

	public function insertRow($query, $params = []) {
		return self::exeCRUD($query, $params);
	}

	public function updateRow($query, $params = []) {
		return self::exeCRUD($query, $params);
	}

	public function deleteRow($query, $params = []) {
		return self::exeCRUD($query, $params);
	}

	public function findRow($query, $params = []) {
		try {
			$statement = $this->db->prepare($query);

			$statement->execute($params);
			// return affected rows
			$statement->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
			$result = $statement->fetch();
			return $result;

		} catch (PDOException $e) {
				echo '<p class="bg-danger">' . $e->getMessage () . '</p>';
				exit ();
		}

	}

	public function insert_id($query, $id, $field = []){
		try {
			$statement = $this->db->prepare($query);
			$statement->execute($field);
			//$statement->setFetchMode(PDO::FETCH_CLASS, __CLASS__);
			$result = $statement->fetch();

			// var_dump($id);//."wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww";
			// echo $result;
			return $result[$id];
		} catch (PDOException $e) {

		}

		return $this->db->lastInsertId();
	}
}

$database = new Database();
//print_r($database->getRow("SELECT * FROM files"));
// $database->close_connection();
//$db =& $database;

?>
