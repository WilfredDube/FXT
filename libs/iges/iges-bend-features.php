<?php

class BendFeatures
{
  protected static $table_name='features';
  protected static $db_fields = array(
    // "featureid",
    "projectid",
    "fileid",
		"bend_id",
		"face1_id",
		"face2_id",
		"angle",
		"bend_loop_id",
    "bend_unit",
		"bend_length",
		"bend_thickness",
		"bend_radius",
		"bend_height",
		"bending_force"
 	);

  public $featureid;
  public $projectid;
  public $fileid;
  public $bend_id;
  public $face1_id;
  public $face2_id;
  public $angle;
  public $bend_loop_id;
  public $bend_unit;
  public $bend_length;
  public $bend_thickness;
  public $bend_radius;
  public $bend_height;
  public $bending_force;

// $record = argument is a bend feature
  public function __construct($record, $projectid, $fileid)
  {
    // Could check that $record exists and is an array
	  // $object = new self;

    // $this->featureid = $featureid;
    $this->projectid = $projectid;
    $this->fileid = $fileid;
    $this->bend_id = $record->Bend_ID;
    $this->face1_id = $record->Face1;
    $this->face2_id = $record->Face2;
    $this->angle = $record->Angle;
    $this->bend_loop_id  = $record->Bend_Loop;
    $this->bend_unit = $record->Bend_Unit;
    $this->bend_length = $record->Bend_Length;
    $this->bend_thickness  = $record->Bend_Thickness;
    $this->bend_radius = $record->Bend_Radius;
    $this->bend_height = $record->Bend_height;
    $this->bending_force = $record->Bend_force;

		// return $object;
  }

  private function has_attribute($attribute)
  {
    // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $this->attributes());
  }

  protected function attributes()
  {
      // return an array of attribute names and their values
  	$attributes = array();
    foreach (self::$db_fields as $field) {
          // echo property_exists($this, $field);
    if (property_exists($this, $field)) {
        // echo $field.": ";
      // echo $this->$field." ";
      $attributes[$field] = $this->$field;
    }
      }
  // print_r($attributes);
  	return $attributes;
  }

  protected function sanitized_attributes()
  {
    global $database;
    $clean_attributes = array();
  	// sanitize the values before submitting
  	// Note: does not alter the actual value of each attribute
  	foreach ($this->attributes() as $key => $value) {
      $clean_attributes[$key] = ($value);
  	}

    return $clean_attributes;
  }
  // Common Database Methods
  public static function find_all()
  {
      global $database;
      return $database->getAllRows("SELECT * FROM ".self::$table_name, []);
  }

  public static function find_feature_by_id($id=0)
  {
      global $database;
      $result_array = self::find_feature_by_sql("SELECT * FROM ".self::$table_name." WHERE fileid=?", [$id]);
      // echo "::::::::::::::::KMCKDNDFJNJDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD";
      // print_r($result_array);
      return !empty($result_array) ? ($result_array) : false;
  }

  public static function find_feature_by_sql($sql="", $params = [])
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

  public function save()
  {
    // print_r($this);
    // A new record won't have an id yet.
    return isset($this->featureid) ? $this->update() : $this->create();
  }

  public function create()
  {
    global $database;
    // echo "string";
    // Don't forget your SQL syntax and good habits:
    // - INSERT INTO table (key, key) VALUES ('value', 'value')
    // - single-quotes around all values
    // - escape all values to prevent SQL injection
    $attributes = $this->sanitized_attributes();

    $sql = "INSERT IGNORE INTO ".self::$table_name." (";
    $sql .= join(", ", array_keys($attributes));
    $sql .= ") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $params = array_values($attributes);

    if ($database->insertRow($sql, $params)) {
      // $query = "SELECT projectid from projects where projectname = ?";
      // $this->projectid = $database->insert_id($query, "projectid", [$this->projectname]);
      return true;
    } else {
        return false;
    }
  }

  public function update()
  {
    global $database;

    // echo "string";
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
    $sql .= " WHERE featureid=?";

    // return ($database->updateRow($sql, [$this->projectid]) == 1) ? true : false;
    $affected_rows = $database->updateRow($sql, [$this->featureid]);
    return ($affected_rows == 1) ? true : false;
  }

  public function destroy($pid)
  {
      // First remove the database entry
    if ($this->delete($pid)) {
        // then remove the file
      // Note that even though the database entry is gone, this object
      // is still around (which lets us use $this->file_path()).
      // $target_path = SITE_ROOT.DS.$this->file_path();
      return true;
    } else {
        // database delete failed
      return false;
    }
  }

  public static function delete($pid)
  {
    global $database;
  // Don't forget your SQL syntax and good habits:
  // - DELETE FROM table WHERE condition LIMIT 1
  // - escape all values to prevent SQL injection
  // - use LIMIT 1
    $sql = "DELETE FROM ".self::$table_name;
    $sql .= " WHERE projectid= ?";
    $sql .= " LIMIT 1";

    $affected_rows  = $database->deleteRow($sql, [$pid]);

    return ($affected_rows == 1 && $deleted_rows == 1) ? true : false;

  // NB: After deleting, the instance of User still
  // exists, even though the database entry does not.
  // This can be useful, as in:
  //   echo $user->first_name . " was deleted";
  // but, for example, we can't call $user->update()
  // after calling $user->delete().
  }
}
