/var_dump($bendz);
// $bends->displaybends($bendz);
//
// $x = new Extract();
// $dim = $x->getDimensions($gsection);
// echo "$dim\n\n";

$file = new IgesFile();
$file->fileUserID = 5;
$file->fileName = "file.igs";
$file->fileType = 'model/iges';
$file->fileSize = 1000;
$file->fileCaption = "Bend file";
$file->fileUploadDate = date("Y-m-d H:i:s");
$file->fileModelUnits = 'mm';
$file->fileModelMaterialsID = 2;

$file->fileID = 1;
$file->delete();
$file->updateModelUnits();
//echo  $file->fileID."\n";
//
// $ts = new TStrength();
// $ts->material = "Steel";
// $ts->tstrength = 1000;
//
// echo ("Material name : ".$ts->getMaterialName(2))."\n";
// echo "Material ID : ".$ts->getMaterialID("Steel, 0.2% Carbon, cold rolled")."\n";
// echo $ts->getMaterialStrength(2)."\n";
// // $ts->insertMAterial();
// $ts->deleteMAterial();

// echo
// //echo date("Y-m-d H:i:s")."\n";
// // echo $file->fileUploadDate."\n";
// // $file->fileID = 1;
//  if ($file->save())
//    echo "SAVED!!\n";

//$file->fileID = $database->insert_id($file->fileName);
//print_r($file->fileID)."fgf\n";
// echo $file->file_path()."ddsd";
// print_r (IgesFile::find_file_by_id(12));
// print_r (IgesFile::count_all());
print_r (IgesFile::find_all());
// foreach ($edgelist as $value) {
// var_dump($value->Edge_List);
// }

//var_dump($_SESSION ['edgelist'][1]->Edge_List);

//var_dump($rbspline);
//var_dump($edgetype);
//var_dump($vtlist);
//echo "$dline1\n";
<?php
// ini_set('display_errors','on');
//include ('includes/config.php');
//include ('classes/fextract.php');

class Extract {
  private $dsection;
  private $psection;
  private $gsection;
  private $vertexlist;
  private $edge504;
  private $edgelist;
  private $edgetype;
  private $surfacetype;
  private $loops;
  private $face_list;
  private $shell;
  private $fx;
  private $Bends;

  function __construct() {
    // $this->psection = $_SESSION ['psection'];
    // $this->dsection = $_SESSION ['dsection'];
    // $this->gsection = $_SESSION ['gsection'];
    //
    // $this->vertexlist = array (); // new VertexList();
    // $this->edgelist = array (); // new EdgeList();
    // $this->edge504 = array ();
    // $this->edgetype = array ();
    // $this->surfacetype = array ();
    // $this->loops = array ();
    // $this->face_list = array ();
    // $this->Bends = array ();
    // ;
    // $this->shell = null;
  }

  private function multiexplode($delimeters, $string) {
    $ready = str_replace ( $delimeters, $delimeters [0], $string );
    $launch = explode ( $delimeters [0], $ready );

    return $launch;
  }

  private function addFileInformation($gsection) {
    $unit = trim ( $gsection [13] );

    // echo $unit."dd";

    switch ($unit) {
      case 1 :
      $dim = "inches";
      break;
      case 2 :
      $dim = "mm";
      break;
      case 3 :
      $dim = "special";
      break;
      case 4 :
      $dim = "ft";
      break;
      case 5 :
      $dim = "miles";
      break;
      case 6 :
      $dim = "metres";
      break;
      case 7 :
      $dim = "Km";
      break;
      case 8 :
      $dim = "mils";
      break;
      case 9 :
      $dim = "microns";
      break;
      case 10 :
      $dim = "cm";
      break;
      case 11 :
      $dim = "minchs";
      break;
      default :
      break;
    }
    //
    // $fid = $_SESSION ['fileid'];
    // try { // echo "dsds"; update files set units='nn' where file_id=2;
    //   $query = "UPDATE files SET units = '$dim' where file_id=$fid";
    //   $stmt = $db->prepare ( $query );
    //   $stmt->execute ();
    // } catch ( PDOException $e ) {
    //   $error [] = $e->getMessage ();
    // }
  }

  private function removeD($arr) {
    $kn = trim ( $arr );
    // echo $kn."dasasasa";
    $pos = strpos ( $kn, "D" );

    if ($pos) {
      $kn [$pos] = "E";
    }

    return $kn;
  }

  public function display() {
    $x = new Extract ();
    $flag = false;

    $loops = $this->face_list;
    $faces = $this->face_list;

    $p = 0;

    foreach ( $loops as $loop ) {
      $face1 = null;
      $face2 = null;
      $bendE = null;

      $i = 0;
      foreach ( $loop->External_Loop->Edge_List as $bedl ) {
        if ($bedl->Edge_Type == "Line" && $loop->Bend_ID != - 1) {

          $i ++;
          $bendE = $bedl;

          foreach ( $faces as $face ) {
            if ($face->Bend_ID == - 1) {
              foreach ( $face->External_Loop->Edge_List as $fedl )
              if ($bendE == $fedl) {
                $flag = true;
                if ($face1 == null)
                $face1 = $face;
                else
                $face2 = $face;
              } else
              continue;
            } else
            continue;

            if ($flag == true) {
              $flag = false;
              break;
            }
          }
        } else {
        }

        if ($i == 2) {
          $bendLength = $this->fx->computeBendLength ( $bendE );
          $angle = $this->fx->computeAngle ( $face1->External_Loop->Normal, $face2->External_Loop->Normal );
          $i = 0;
          $this->Bends [$p] = new Bend ();
          $this->Bends [$p]->Bend_ID = $loop->Bend_ID;
          $this->Bends [$p]->Face1 = $face1->Face_ID;
          $this->Bends [$p]->Face2 = $face2->Face_ID;
          $this->Bends [$p]->Angle = $angle;
          $this->Bends [$p]->Bend_Loop = $loop->External_Loop->Loop_ID;
          $this->Bends [$p]->Bend_Length = $bendLength;

          ++ $p;
        }
      }
    }

    $this->insertBendFeatures ();
  }

}

?>
