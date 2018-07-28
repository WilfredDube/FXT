<?php

class Extract {
  private $dsection;
  private $psection;
  private $gsection;

  function __construct() {
    if (isset($_SESSION['psection']) && isset($_SESSION['dsection']) && isset($_SESSION['gsection'])) {
      $this->psection = $_SESSION ['psection'];
      $this->dsection = $_SESSION ['dsection'];
      $this->gsection = $_SESSION ['gsection'];
    }
  }

  public function multiexplode($delimeters, $string) {
    $ready = str_replace ( $delimeters, $delimeters [0], $string );
    $launch = explode ( $delimeters [0], $ready );

    return $launch;
  }

  public function getDimensions($gsection) {
    $unit = trim ( $gsection [13] );
    $dim = null;

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

    return $dim;
  }

  public function removeD($arr) {
    $kn = trim ( $arr );

    $pos = strpos ( $kn, "D" );

    if ($pos) {
      $kn [$pos] = "E";
    }

    return $kn;
  }
}
$xtract = new Extract();

//var_dump($xtract);

?>
