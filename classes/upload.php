<?php
// include('password.php');
class Upload {
  private static $userid;
  private $fileuserid;
  private $filename;
  private $filetype;
  private $filesize;
  private $filecaption;
  private $filemodelmaterial;
  private $filemodelunits;
  private $fileuploaddate;
  // private $_db;

  function __construct($userid) {
    self::$userid = $userid;
  }

  // public function
  public function getMaterialID($material) {
    $query = "SELECT m_id FROM tstrength where material='$material'";
    foreach ( $this->_db->query ( $query ) as $row ) {
      $id = $row ['m_id'];
    }

    return $id;
  }
  public function getMaterialName($id) {
    $material = null;

    // echo $id."KKKL";
    $query = "SELECT material FROM tstrength where m_id='$id'";
    foreach ( $this->_db->query ( $query ) as $row ) {
      $material = $row ['material'];
    }

    return $material;
  }
  public function getMaterialStrength($id) {
    $TS = null;

    // echo $id."KKKL";
    $query = "SELECT tensile_strength FROM tstrength where m_id='$id'";
    foreach ( $this->_db->query ( $query ) as $row ) {
      $TS = $row ['tensile_strength'];
    }

    return $TS;
  }
  
  private function file_exists($tmp_name, $file_name) {
    if (file_exists ( FILEREPOSITORY . $tmp_name . ".iges" )) {
      $updatedFileName = update_file_name ( FILEREPOSITORY . $file_name );
      $result = move_uploaded_file ( $tmp_name, FILEREPOSITORY . "/$file_name.igs" );
    } else {
      $result = move_uploaded_file ( $tmp_name, FILEREPOSITORY . "/$file_name.igs" );
    }

    return $result;
  }

  public function validate_file($file_name, $tmp_name, $file_type) {
    $perfect = false;
    if ($file_type == "model/iges") {
      if ($this->file_exists ( $tmp_name, $file_name )) {
        if ($this->upload_file ( $file_name, $file_caption, $material_ID ))
        $perfect = true;
      }
    }

    return $perfect;
  }

  private function upload_file($file_name, $file_caption, $material_ID) {
    $success = false;
    $file_name = $file_name . ".igs";
    $file_username = $_SESSION ['username'];
    $file_date = date ( 'Y-m-d' );

    try {
      $query = 'INSERT INTO files (filename,file_username,file_caption,file_date, file_material) VALUES (:filename, :file_username, :file_caption, :file_date, :file_material)';
      $stmt = $this->_db->prepare ( 'INSERT INTO files (filename,file_username,file_caption,file_date, file_material) VALUES (:filename, :file_username, :file_caption, :file_date, :file_material)' );
      $stmt->execute ( array (
        ':filename' => $file_name,
        ':file_username' => $file_username,
        ':file_caption' => $file_caption,
        ':file_date' => $file_date,
        ':file_material' => $material_ID
        ) );
        $success = true;
      } catch ( PDOException $e ) {
        $error [] = $e->getMessage ();
      }

      return $success;
    }
  }

$upload = new Upload($_SESSION['user']['memberID']);
?>
