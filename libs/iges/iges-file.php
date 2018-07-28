<?php
// If it's going to need the database, then it's
// probably smart to require it before we start.
//require_once(LIB_PATH.DS.'database.php');

class IgesFile
{
  protected static $table_name='files';
  protected static $db_fields=array(
    'fileuserid',
    'fileprojectid',
    'filename',
    'filetype',
    'filesize',
    'filecaption',
    'filemodelunits',
    'filemodelmaterialid'
    // 'fileuploaddate'
  );

  public $fileid;
  public $fileuserid;
  public $fileprojectid;
  public $filename;
  public $filetype;
  public $filesize;
  public $filecaption;
  public $fileuploaddate;
  public $filemodifieddate;
  public $filemodelunits;
  public $filemodelmaterialid;

  private $temp_path;
  protected $upload_dir= NULL;
  public $errors=array();

  protected static $upload_errors = array(
    // http://www.php.net/manual/en/features.file-upload.errors.php
    UPLOAD_ERR_OK                => "No errors.",
    UPLOAD_ERR_INI_SIZE    => "Larger than upload_max_filesize.",
    UPLOAD_ERR_FORM_SIZE    => "Larger than form MAX_FILE_SIZE.",
    UPLOAD_ERR_PARTIAL        => "Partial upload.",
    UPLOAD_ERR_NO_FILE        => "No file.",
    UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
    UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
    UPLOAD_ERR_EXTENSION    => "File upload stopped by extension."
  );

  function __construct() {}

  private static function instantiate($record)
  {
      // Could check that $record exists and is an array
    $object = new self;

    // More dynamic, short-form approach:
    foreach ($record as $attribute=>$value) {
        if ($object->has_attribute($attribute)) {
            $object->$attribute = $value;
        }
    }
    return $object;
  }

  private function has_attribute($attribute)
  {
      // We don't care about the value, we just want to know if the key exists
    // Will return true or false
    return array_key_exists($attribute, $this->attributes());
  }

  public function attributes()
  {
      // return an array of attribute names and their values
    $attributes = array();
    foreach (self::$db_fields as $field) {
        if (property_exists($this, $field)) {
            $attributes[$field] = $this->$field;
        }
    }
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

  public static function getProjectFile($id) {
    global $database;

    $query = 'SELECT * FROM files WHERE fileprojectid=?';
    $res = $database->getAllRows($query, [$id]);

    return (isset($res)) ? ($res) : false;
  }

  // Pass in $_FILE(['uploaded_file']) as an argument
  public function attach_file($file)
  {
      // Perform error checking on the form parameters
    if (!$file || empty($file) || !is_array($file)) {
        // error: nothing uploaded or wrong argument usage
      $this->errors[] = "No file was uploaded.";
        return false;
    } elseif ($file['error'] != 0) {
        // error: report what PHP says went wrong
      $this->errors[] = self::upload_errors[$file['error']];
        return false;
    } else {
        if (IgesFile::isIGESfile($file['type'])) {
            // Set object attributes to the form parameters.
            $this->temp_path  = $file['tmp_name'];
            $this->fileuserid = $_SESSION['user']['memberID'];
            $this->fileprojectid = $file['fileprojectid'];
            $this->filename   = $file['fileprojectid']."_".basename($file['name']);
            $this->filetype   = $file['type'];
            $this->filesize   = $file['size'];
            $this->filecaption = $file['filecaption'];
            $this->filemodelmaterialid = $file['filematerialid'];
            // $this->fileuploaddate = date("Y-m-d H:i:s");
        // Don't worry about saving anything to the database yet.
        // print_r($this);
        return true;
        }
    }
  }

  private static function isIGESfile($file_type)
  {
      $perfect = false;
      if ($file_type == "model/iges" || $file_type == "application/octet-stream") {
          $perfect = true;
      }
      return $perfect;
  }

  public function save()
  {
      // A new record won't have an id yet.
    if (isset($this->fileid)) {
        // Really just to update the caption
      $this->update();
    } else {
      // Make sure there are no errors

    // Can't save if there are pre-existing errors
    if (!empty($this->errors)) {
        return false;
    }

    // Make sure the caption is not too long for the DB
    if (strlen($this->filecaption) > 255) {
        $this->errors[] = "The caption can only be 255 characters long.";
        return false;
    }

    // Can't save without filename and temp location
    if (empty($this->filename) || empty($this->temp_path)) {
        $this->errors[] = "The file location was not available.";
        return false;
    }

    $this->upload_dir = User::getUserFolder();

    // Determine the target_path
    $target_path = $this->upload_dir .DS. $this->filename;

    // Make sure a file doesn't already exist in the target location
    if (file_exists($target_path)) {
        $this->errors[] = "The file {$this->filename} already exists.";
        return false;
    }

    // Attempt to move the file
    if (move_uploaded_file($this->temp_path, $target_path)) {
      chmod($target_path, 0777);
        // Success
      // Save a corresponding entry to the database
      if ($this->create()) {
          // We are done with temp_path, the file isn't there anymore

        unset($this->temp_path);
          return true;
      }
    } else {
        // File was not moved.
      $this->errors[] = "The file upload failed, possibly due to incorrect permissions on the upload folder.";
        return false;
    }
  }
  }

  public static function file_path()
  {
      return User::getUserFolder();
  }

  public function size_as_text()
  {
      if ($this->size < 1024) {
          return "{$this->size} bytes";
      } elseif ($this->size < 1048576) {
          $size_kb = round($this->size/1024);
          return "{$size_kb} KB";
      } else {
          $size_mb = round($this->size/1048576, 1);
          return "{$size_mb} MB";
      }
  }

  private function getUnits($gsection)
  {
      $unit = trim($gsection [13]);

      switch ($unit) {
    case 1:
    $dim = "inches";
    break;
    case 2:
    $dim = "mm";
    break;
    case 3:
    $dim = "special";
    break;
    case 4:
    $dim = "ft";
    break;
    case 5:
    $dim = "miles";
    break;
    case 6:
    $dim = "metres";
    break;
    case 7:
    $dim = "Km";
    break;
    case 8:
    $dim = "mils";
    break;
    case 9:
    $dim = "microns";
    break;
    case 10:
    $dim = "cm";
    break;
    case 11:
    $dim = "minchs";
    break;
    default:
    break;
  }
      return $dim;
  }

  public static function getFileID() {
    return $this->fileid;
  }

  public function getModelUnits($gsection = [])
  {
      return $this->getUnits($gsection);
  }
  // Common Database Methods
  public static function find_all()
  {
      global $database;
      return $database->getAllRows("SELECT * FROM ".self::$table_name, []);
  }

  public static function find_file_by_id($id=0)
  {
      global $database;
      $result_array = self::find_file_by_sql("SELECT * FROM ".self::$table_name." WHERE file_id= ? LIMIT 1", [$id]);
      return !empty($result_array) ? ($result_array) : false;
  }

  public static function find_file_by_sql($sql="", $params = [])
  {
      global $database;
      $result_set = $database->findRow($sql, $params);
      return $result_set;
  }

  public static function count_all()
  {
      global $database;
      $sql = "SELECT * FROM ".self::$table_name;
      $res = $database->count($sql);

      return ($res);
  }

  public function create()
  {
    global $database;
    // Don't forget your SQL syntax and good habits:
    // - INSERT INTO table (key, key) VALUES ('value', 'value')
    // - single-quotes around all values
    // - escape all values to prevent SQL injection
    $attributes = $this->sanitized_attributes();

    $sql = "INSERT INTO ".self::$table_name." (";
    $sql .= join(", ", array_keys($attributes));
    $sql .= ") VALUES (?,?,?,?,?,?,?,?)";

    $params = array_values($attributes);

    if ($database->insertRow($sql, $params)) {
      $query = "SELECT fileid from files where filename = ?";
      $this->fileid = $database->insert_id($query, "fileid", [$this->filename]);

      if(($this->fileid)) {
        $_SESSION['projects'][] = $this->filename;
      }

      return true;
    } else {
        return false;
    }
  }

  public function update()
  {
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
    $sql .= " WHERE id=?";

    $affected_rows = $database->updateRow($sql, [$this->fileid]);
    return ($affected_rows == 1) ? true : false;
  }

  public static function destroy($id)
  {
    global $database;

    $find = "SELECT filename FROM ".self::$table_name." WHERE fileid= ?";
    $filename = $database->findRow($find, [$id]);

    $_SESSION['file'] = $filename->filename;
      // First remove the database entry
    if (IgesFile::delete($id)) {
      // then remove the file
      // Note that even though the database entry is gone, this object
      // is still around (which lets us use $this->file_path()).
      $target_path = IgesFile::file_path()."/".$filename->filename;
      if (file_exists($target_path)) {
        chmod($target_path, 0777);
        return unlink($target_path) ? true : false;
      }
    } else {
      // database delete failed
      return false;
    }
  }

  public static function delete($id)
  {
    global $database;
    // Don't forget your SQL syntax and good habits:
    // - DELETE FROM table WHERE condition LIMIT 1
    // - escape all values to prevent SQL injection
    // - use LIMIT 1

    $sql = "DELETE FROM ".self::$table_name;
    $sql .= " WHERE fileid= ?";
    $sql .= " LIMIT 1";
    $affected_rows = $database->deleteRow($sql, [$id]);

    return ($affected_rows == 1) ? $filename : false;

  // NB: After deleting, the instance of User still
  // exists, even though the database entry does not.
  // This can be useful, as in:
  //   echo $user->first_name . " was deleted";
  // but, for example, we can't call $user->update()
  // after calling $user->delete().
  }
}

$igesfile = new IgesFile();

// echo IgesFile::file_path()."/".$_SESSION['file'];
//echo IgesFile::file_path();
//IgesFile::delete(2);
?>
