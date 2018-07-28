<?php
// If it's going to need the database, then it's
// probably smart to require it before we start.
// require_once('database.php');

class Project {
    protected static $table_name='projects';
    protected static $db_fields=array('projectname', 'projectdescription', 'projectmaterialid', 'projectownerid', 'projectfileid');
    public $projectid;
    public $projectname;
    public $projectdescription;
    public $projectownerid;
    public $projectmaterialid;
    public $projectfileid;

    function __construct() {
      $this->projectfileid = 0;
    }

  private static function instantiate($record) {
    // Could check that $record exists and is an array
    $object = new self;

    // More dynamic, short-form approach:
    foreach($record as $attribute=>$value){
      if($object->has_attribute($attribute)) {
        $object->$attribute = $value;
      }
    }
    return $object;
  }

  private function has_attribute($attribute) {
    // We don't care about the value, we just want to know if the key exists
    // Will return true or false
    return array_key_exists($attribute, $this->attributes());
  }

  protected function attributes() {
    // return an array of attribute names and their values
    $attributes = array();
    foreach(self::$db_fields as $field) {
      // echo property_exists($this, $field);
      if(property_exists($this, $field)) {
        // echo $field.": ";
        // echo $this->$field." ";
        $attributes[$field] = $this->$field;
      }
    }
    // print_r($attributes);
    return $attributes;
  }

  protected function sanitized_attributes() {
    global $database;
    $clean_attributes = array();
    // sanitize the values before submitting
    // Note: does not alter the actual value of each attribute
    foreach($this->attributes() as $key => $value){
      $clean_attributes[$key] = ($value);
    }

    return $clean_attributes;
  }

  public function save() {
    // A new record won't have an id yet.
    return isset($this->projectid) ? $this->update() : $this->create();
  }

  public function create() {
    global $database;
    // Don't forget your SQL syntax and good habits:
    // - INSERT INTO table (key, key) VALUES ('value', 'value')
    // - single-quotes around all values
    // - escape all values to prevent SQL injection
    $attributes = $this->sanitized_attributes();

    $sql = "INSERT INTO ".self::$table_name." (";
    $sql .= join(", ", array_keys($attributes));
    $sql .= ") VALUES (?,?,?,?,?)";

    $params = array_values($attributes);

    if($database->insertRow($sql, $params)) {
      $query = "SELECT projectid from projects where projectname = ?";
      $this->projectid = $database->insert_id($query, "projectid", [$this->projectname]);
      return true;
    } else {
      return false;
    }
  }

  public function update() {
    global $database;
    // Don't forget your SQL syntax and good habits:
    // - UPDATE table SET key='value', key='value' WHERE condition
    // - single-quotes around all values
    // - escape all values to prevent SQL injection
    $attributes = $this->sanitized_attributes();
    $attribute_pairs = array();
    foreach($attributes as $key => $value) {
      $attribute_pairs[] = "{$key}='{$value}'";
    }
    $sql = "UPDATE ".self::$table_name." SET ";
    $sql .= join(", ", $attribute_pairs);
    $sql .= " WHERE projectid=?";
    ;
    return ($database->updateRow($sql, [$this->projectid]) == 1) ? true : false;
  }

  public function setProjectFileID($fileid, $projectid) {
    global $database;
    $query = 'UPDATE '.self::$table_name.' SET projectfileid=? WHERE projectid=?';
    $params = array($fileid, $projectid);

    $res =$database->updateRow($query, $params);
// print_r($params);
    // echo $res;
    return ($res == 1) ? true : false;
  }

  public static function getProjectFileID($id) {
    $query = "SELECT projectfileid FROM ".self::$table_name." WHERE projectid=? LIMIT 1";
    $result_array = self::find_project_by_sql($query, [$id]);

    $ret = (array_shift($result_array)['projectfileid']);
    return $ret;
  }

  public static function getModelFileID($projectid) {
    global $database;
    $query = 'SELECT fileid FROM files WHERE fileprojectid=?';

    $fileid = ($database->getRow($query, [$projectid]));

    // print_r(array_shift($fileid));
    return (is_array($fileid) && !empty($fileid)) ? array_shift($fileid) : $fileid;
  }

  public function destroy()
  {
        // First remove the database entry
    if ($this->delete()) {
        // then remove the file
      // Note that even though the database entry is gone, this object
      // is still around (which lets us use $this->file_path()).
      // $target_path = SITE_ROOT.DS.$this->file_path();
      return unlink($target_path) ? true : false;
    } else {
        // database delete failed
      return false;
    }
  }

  public static function getMaterialID($materialname) {
    $query = 'SELECT m_id FROM tstrength WHERE material=?';
    $materialname = array($materialname);

    $ret = self::find_project_by_sql($query, $materialname);
    return array_shift($ret);
  }

  public static function getAllMaterials() {
    $query = 'SELECT material FROM tstrength';
    return self::find_project_by_sql($query,[]);
  }

  // Common Database Methods
  public static function find_all()
  {
      global $database;
      return $database->getAllRows("SELECT * FROM ".self::$table_name, []);
  }

    public static function find_project_by_id($id=0)
    {
        global $database;
        $result_array = self::find_project_by_sql("SELECT projectfileid FROM ".self::$table_name." WHERE projectid=? LIMIT 1", [$id]);
        print_r($result_array);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_project_by_sql($sql="", $params = [])
    {
        global $database;
        $result_set = $database->getAllRows($sql, $params);
        return !empty($result_set) ? ($result_set) : false;
    }

    public static function count_all()
    {
        global $database;
        $sql = "SELECT * FROM ".self::$table_name;
        $res = $database->count($sql);

        return ($res);
    }

    public function updateProject()
    {
      global $database;
    // Don't forget your SQL syntax and good habits:
    // - UPDATE table SET key='value', key='value' WHERE condition
    // - single-quotes around all values
    // - escape all values to prevent SQL injection

      $sql = "UPDATE ".self::$table_name." SET filemodelunits = ?  WHERE fileid = ?";
      $affected_rows = $database->updateRow($sql, [$this->fileModelUnits, $this->fileID]);
      return ($affected_rows == 1) ? true : false;
    }

    public static function delete($id)
    {
      global $database;
    // Don't forget your SQL syntax and good habits:
    // - DELETE FROM table WHERE condition LIMIT 1
    // - escape all values to prevent SQL injection
    // - use LIMIT 1
      $projectsql = "DELETE FROM ".self::$table_name;
      $projectsql .= " WHERE projectid= ?";
      $projectsql .= " LIMIT 1";

      $filesql = "DELETE FROM files";
      $filesql .= " WHERE fileprojectid= ?";
      $filesql .= " LIMIT 1";

      $fileid = self::getModelFileID($id);

      $deleted_rows   = IgesFile::destroy($fileid);
      $affected_rows  = $database->deleteRow($projectsql, [$id]);

      return ($affected_rows == 1 && $deleted_rows == 1) ? true : false;

    // NB: After deleting, the instance of User still
    // exists, even though the database entry does not.
    // This can be useful, as in:
    //   echo $user->first_name . " was deleted";
    // but, for example, we can't call $user->update()
    // after calling $user->delete().
    }
}

$project = new Project();

// print_r(array_shift(Project::getAllMaterials()));
//$igesfile = new IgesFile();
?>
