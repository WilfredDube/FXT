<?php

class Bend {

  protected static $table_name = 'bends';
  public $Bend_ID;
  public $Face1;
  public $Face2;
  public $Angle;
  public $Bend_Loop;
  public $Bend_Unit;
  public $Bend_Length;
  public $Bend_Thickness;
  public $Bend_Radius;
  public $Bend_height;
  public $Bend_force;

  static $bends;

  function __construct(){
    self::$bends = array();
  }

  public function bendTract($face_list) {
    $x = new Extract ();
    $fx = new Computation ();
    $flag = false;
    $bends = array();

    $loops = $face_list;
    $faces = $face_list;

    // var_dump($face_list);
    $p = 0;

    if ($face_list != null)
    foreach ( $loops as $loop )
    {
      $face1 = null;
      $face2 = null;
      $bendE = null;

      $i = 0;
      // var_dump($loop->External_Loop->Edge_List);
      if (isset($loop->External_Loop->Edge_List) && is_array($loop->External_Loop->Edge_List))
      foreach ( $loop->External_Loop->Edge_List as $bedl ) {
        if ($bedl->Edge_Type == "Line" && $loop->Bend_ID != - 1) {
          // echo $i."<br>";
          // var_dump($bedl);
          $i ++;
          // echo $loop->Bend_ID." ";
          $bendE = $bedl;

          foreach ( $faces as $face ) {
            if ($face->Bend_ID == -1) {
              foreach ( $face->External_Loop->Edge_List as $fedl )
              if ($bendE == $fedl) {
                $flag = true;

                if ($face1 == null)
                  $face1 = $face;
                else
                  $face2 = $face;

                // print_r($face1->Face_ID);
                // echo " ";
                // if (isset($face2))
                //   print_r($face2->Face_ID);
                // echo "<br>";
                // echo "<br>";
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

          $bendLength = $fx->computeBendLength ( $bendE );
          $angle = $fx->computeAngle ( $face1->External_Loop->Normal, $face2->External_Loop->Normal );

          $i = 0;

          // print_r($face1->Face_ID);
          // echo " ";
          // print_r($face2->Face_ID);
          // echo "<br>";
          // echo "<br>";

          self::$bends [$p] = new Bend ();
          self::$bends [$p]->Bend_ID = $loop->Bend_ID;
          self::$bends [$p]->Face1 = $face1->Face_ID;
          self::$bends [$p]->Face2 = $face2->Face_ID;
          self::$bends [$p]->Angle = $angle;
          self::$bends [$p]->Bend_Loop = $loop->External_Loop->Loop_ID;
          self::$bends [$p]->Bend_Length = $bendLength;
          $bends[$p] = self::$bends [$p];

          ++ $p;
        }
      }
    }

    // var_dump($bends);

    $_SESSION['bends'] = $bends;
    return $_SESSION['bends'];
  }

  public function insertBendFeatures($Bends, $unit) {
    global $database;
    $fx = new Computation ();
    $tstrength = new TStrength();

    if (isset ( $Bends )) {
      foreach ( $Bends as $bend ) {
        $shell = Shell::getShell();

        foreach ($shell->Face_List as $value) {
          if ($value->Surface_Type == "Plane Surface") {
            $thick = $value->Thickness;
            $dim = $value->Dimension;
            break;
          }
        }

// echo "$dim";
        $rads = array (
          2,
          3,
          5,
          8
        );
        $key = array_rand ( $rads, 1 );

        // echo $thick;
        $height = 2.5 * $thick * $rads [$key];

        $query = "SELECT filemodelmaterialid from files where fileid=?";
        $materialID = $database->findRow($query, [$_SESSION ['fileid']]);

        $TS = $tstrength->getMaterialStrength($materialID->filemodelmaterialid  );

        $force = $fx->computeBendingForce ( $bend->Bend_Length, $thick, $unit, $TS );

        // echo $bend->Bend_Length." ,Thickness : ".$thick." ,Units : ".$unit." ,Tensile Strength :".$TS." ,Bend Force :".$force."\n";

        // TODO: Make feature unique
        // $featureid = $_SESSION['projectid']."".$_SESSION ['fileid'] . "" . $bend->Bend_ID . "" . $bend->Bend_Loop."";
        // $bend->Bend_ID .= trim ( $featureid );
        $bend->Bend_ID = "b".$bend->Bend_ID;
        $bend->Bend_height = $height;
        $bend->Bend_force = $force;
        $bend->Bend_Unit = $dim;
        $bend->Bend_Thickness = $thick;
        $bend->Bend_Radius = $rads [$key];

        // echo "\n".$bend->Bend_ID."  \n";

        // print_r($bend);

        $bendfeature = new BendFeatures($bend, $_SESSION['projectid'], $_SESSION ['fileid']);

        // var_dump($bendfeature);
        // print_r($bendfeature);

        $bendfeature->save();

      } /* */
      $done = true;
    }
  }

  public function displaybends($bends) {
    echo count ( self::$bends );
    if (isset ( self::$bends )) {
      foreach ( self::$bends as $bend ) {
        print_r ( $bend->Angle );
        echo "<br/>";
      }
    }
  }

  public function getbends() {
    return $this;
  }

}
?>
