<?php

class Face {

  var $Face_ID;
  var $Bend_ID;
  var $Dimension;
  var $Surface_Type;
  var $Surface_Pointer;
  var $Thickness;
  var $External_Loop;
  // var $Internal_Loop_Count;
  var $Internal_Loop; // ARRAY
  var $Central_Loop;
  var $D_Element;

  private $face_list;
  static $shell;
  static $surface;

  function __construct() {
    $this->face_list = array();
    $this->Internal_Loop = array();
    self::$shell = new Shell();
    self::$surface = new RBSplineSurface();
  }

  public function facetract($dsection, $psection, $loops, $vertexlist, $edgeList, $dim) {
    global $xtract;
    $counter = 0;
    $bend = 0;
    $bendface = 0;
    $nfaces = 0;
    $face = null;
    static $p = 1;
    $f = 1;

    // var_dump($edgeList);
    $i = 1;
    foreach ( $dsection as $value )
    {

      if ($value->EntityType == 510)
      {
        // echo $i."<br>";
        // var_dump($value);
        $pentry = $psection [$value->PointerData];

        $arr = $xtract->multiexplode ( array (
          ",",
          ";"
        ), $pentry );

        $face = new Face ();
        $pSurf = trim ( $arr [1] );
        $Num_of_loops = $arr [2];
        $OuterLoopFlag = $arr [3];
        $pLoop = $arr [4];

        $index = $dsection [$pSurf]->PointerData;
        $ret = self::$surface->RBSplineSurfaceTract( $psection [$index] );
        // var_dump($ret);
        $ppentry_ploop = $dsection [$pLoop]->PointerData;

        static $countedge = 0;
        static $countplane = 0;
        static $countsideplane = 0;
        static $countcurved = 0;
        static $countedgeside = 0;

        $counter ++;
        ++$countplane;
        if (($ret->PROP3) == 1) {
          if ($loops [$ppentry_ploop]->Loop_Type != "BEND") {

            $face->Surface_Type = "Plane Surface";
            $face->Face_ID = $countplane;;
            $face->Bend_ID = - 1;
            $face->Dimension = $dim;
            $nfaces ++;

            // echo "Plane : ";
            // echo $face->Face_ID.", ";
            //echo $face->Surface_Type.' <br>';
          } else {
            $face->Surface_Type = "Bend Side";
            // $face->Surface_Type = "Plane Surface";
            $face->Face_ID = 0;
            $face->Bend_ID = - 1;
            $face->Dimension = $dim;
            unset($face);
            continue;
            $nfaces ++;
            // echo "Side : ";
            // echo $face->Face_ID.", ";//
            ++$countedge;
            ++$countedgeside;
            //echo $face->Surface_Type.' <br>';
          }
        } else {
          --$bendface;
          $bend ++;
          $face->Surface_Type = "Curved Surface";
          $face->Face_ID = $bendface;
          $face->Bend_ID = "0".$bend;
          $face->Dimension = $dim;
          ++$countcurved;
          // echo "curved : ";
          // echo $face->Face_ID.",<br/> ";
          // echo "Bend : ".$face->Bend_ID."<br/>";
        }

        $face->External_Loop = $loops [$ppentry_ploop];

        $thick = 0;
        static $thickray = array();
        if ($face->Surface_Type == "Plane Surface") {
            // echo "Face ID : ".$face->Face_ID."<br/> ";
            $fx = new Computation();
            $thick = $fx->computeThickness($face->External_Loop->Edge_List);

            $thickray[] = $thick;
        }

        // print_r($face);
        // echo "<br/><br/>";
        $this->face_list [$counter] = new Face ();
        $this->face_list [$counter] = $face;

        $i++;
      }

    }

    if (!empty($thickray)) {
      natsort($thickray);

      $th = array_shift($thickray);
    }

    foreach ($this->face_list as $value) {
        $value->Thickness = $th;
    }

    $fx = new Computation();
    $fc = 0;
    foreach ($this->face_list as $key => $face) {

      if ($face->Surface_Type == "Plane Surface" || $face->Surface_Type == "Curved Surface") {
          $issideplane = $fx->isSidePlane($face->External_Loop->Edge_List, $face->Thickness);

          if ($issideplane == TRUE) {
            // echo "Face ID : ".$face->Face_ID."<br/> ";
            $face->Surface_Type = "Thickness Defining Side";
            $face->Face_ID = 0;
            // ++$countsideplane;
            unset($this->face_list[$key]);
            // $this->face_list = array_values($this->face_list);
            continue;
            // echo $face->Surface_Type." <br/>";
          } else {
            // Reset the Face ID of the plane faces
            // echo "Face ID .......: ".$face->Face_ID."<br/> ";
            if ($face->Surface_Type == "Plane Surface") {
                ++$fc;
                $face->Face_ID = $fc;
              }

            $this->computePlaneEquation($face);
          }
      }


      // echo $face->Surface_Type." <br/>";
    }

    $FFC = 0;
    foreach ($this->face_list as $face) {

      if ($face->Surface_Type == "Plane Surface" || $face->Surface_Type == "Curved Surface") {
          $issideplane = $fx->isSidePlane($face->External_Loop->Edge_List, $face->Thickness);

          if ($issideplane == TRUE) {
            // echo "Face ID : ".$face->Face_ID."<br/> ";
            // $face->Surface_Type = "Thickness Defining Side";
            // $face->Face_ID = 0;
            ++$countsideplane;
            // echo $face->Surface_Type." <br/>";
          } else {
            // Reset the Face ID of the plane faces
            // echo "Face ID .......: ".$face->Face_ID."<br/> ";
            if ($face->Surface_Type == "Plane Surface") {
                ++$FFC;
                // $face->Face_ID = $fc;
              }

            // $this->computePlaneEquation($face);
          }
      }
}
    // foreach ($this->face_list as $face) {
            // echo $face->Face_ID." <br/>";
    // }

    //  echo "Bend Side face: ".$countedgeside." Plane face: ".($countplane - $countsideplane)." Side Plane: ".$countsideplane." Curved face: ".$countcurved."<br/>";
    //  echo "Bend Side face: ".$countedgeside." Plane face: ".($FFC)." Side Plane: ".$countsideplane." Curved face: ".$countcurved."<br/>";
     //
    //  echo count($this->face_list)."<br>";// [, $mode])
     //
    $this->facePairing($this->face_list);
    // foreach ($this->face_list as $face)
    // echo $face->Surface_Type."<br>";

    // echo count($this->face_list)."<br>";

    self::$shell->createShell($vertexlist, $edgeList, $loops, $this->face_list);
  }

  public function getFaceList() {
    // foreach ($this->face_list as $face) {
    //         echo $face->Cent." <br/>";
    // }
    return $this->face_list;//$_SESSION['facelist'];
  }

  public function facePairing(&$facelist){
    global $compute;
    $planefaces = array();
    $c = 0;
    $faceArr = array();

    foreach ($facelist as $face) {
      if ($face->Surface_Type == "Plane Surface" || $face->Surface_Type == "Curved Surface") {
        $planefaces[] = $face;

        // echo $face->D_Element." ";
        // print_r($face);
        ++$c;
        // echo $c."<br/><br/>";
        // echo $face->Face_ID.", ";
        // echo $face->Face_ID." ";
      }
    }

    // foreach ($planefaces as $face)

    // print_r($planefaces);

    // reset($planefaces);
    $secondfaces = $planefaces;
    // echo count($secondfaces);
    $con = 0;
    foreach ($planefaces as $key1 => $firstface) {
      foreach ($secondfaces as $key2 => $face) {
        if ($firstface->Face_ID === $face->Face_ID) {
          // echo "Dupicate : ".$firstface->Face_ID.", ".$face->Face_ID." ";
          // echo "<br/>";
          // echo $firstface->Face_ID." ";
          // unset($secondfaces[$key2]);
          unset($planefaces[$key1]);
          continue;
        }
        // echo "..... ".$firstface->Face_ID.", ".$face->Face_ID." <br/>";
        // echo "<br/>";
        $isparallel = $compute->computeParallel($firstface->External_Loop->Normal, $face->External_Loop->Normal);

//         echo count($secondfaces).", ";
//         // echo $isparallel." ";
// echo count($planefaces)."p, ";
        if ($isparallel === TRUE) {
          // echo $firstface->Face_ID.", ".$face->Face_ID." <br/>";
          if ($firstface->Face_ID < 0) {

            $isparallelTOO = $compute->computeDistanceBTWPlaneVertices($firstface, $face);

            if ($isparallelTOO === TRUE) {
              $faceArr[] = $this->joinFaces($firstface, $face );
              // unset($secondfaces[$key2]);
              // unset($planefaces[$key1]);
              // echo $firstface->Face_ID." and ".$face->Face_ID." are parallel <br/> ";
              break;
            }
          } else {
          $isparallelTOO = $compute->computeDistanceBTWPlanes($firstface, $face);
          if ($isparallelTOO === TRUE) {
            ++$con;
            // echo $firstface->Face_ID." and ".$face->Face_ID." are parallel <br/> ";
            $isparallelTOO = $compute->computeDistanceBTWPlaneVertices($firstface, $face);

            if ($isparallelTOO === TRUE) {
              $faceArr[] = $this->joinFaces($firstface, $face);
              // unset($secondfaces[$key2]);
              // unset($planefaces[$key1]);
              // echo $firstface->Face_ID." and ".$face->Face_ID." are parallel <br/> ";
              break;
            }

          }
          }
          // echo $firstface->Face_ID." and ".$face->Face_ID." are parallel <br/> ";
          // echo $firstArr->Face_ID." are parallel <br/> ";

          // echo $firstface->Face_ID." and ".$face->Face_ID." are parallel <br/> ";

          // $val = $compute->distanceBTWPlanes($n1, $n2);
          // var_dump($val);
        } else {
          // echo " NOT ";
          // echo "<br/>";
        }
      }
    }

    // foreach ($this->face_list as $key => $value) {
    //   if ($value->Face_ID == 0) {
    //     $faceArr[] = $value;
    //   }
    // }
    $cn = 0;
    foreach ($faceArr as $key => $value) {
      ++$cn;
      if (($cn % 2) == 0)
      unset($faceArr[$key]);
    }

    foreach ($faceArr as $key => $value) {
      $value->Central_Loop = $compute->centreSurfaceGeneration($value);

      // echo $value->Surface_Type;
      // echo "<br/>";
      // print_r($value->External_Loop);
      // echo "<br/><br/>";
      // print_r($value->Central_Loop);
      // echo "<br/><br/>";
      // print_r($value->Internal_Loop);
      // echo "<br/><br/>";
      // if (empty($faceArr[$key]->Internal_Loop))
      //   unset($faceArr[$key]);
      // echo $value->Face_ID;
    }

    // echo " ".$con;
    $sh = new Shell();
    $sh->reformPartID($faceArr);
    foreach ($faceArr as $key => $value) {
      // $value->Central_Loop = $compute->centreSurfaceGeneration($value);

      // echo $value->Face_ID;
      // echo "<br/>";
      // print_r($value->External_Loop);
      // echo "<br/><br/>";
      // print_r($value->Central_Loop);
      // echo "<br/><br/>";
      // print_r($value->Internal_Loop);
      // echo "<br/><br/>";
      // if (empty($faceArr[$key]->Internal_Loop))
      //   unset($faceArr[$key]);
      // echo $value->Face_ID;
    }
    $this->face_list = $faceArr;

    return;
  }

  private function computePlaneEquation($face) {
    foreach ($face->External_Loop->Edge_List as $key => $edge) {
      // print_r($face->External_Loop->Normal);
      $normal = $face->External_Loop->Normal;

      $d1 = ($normal->x * $edge->Start_Vertex->x) + ($normal->y * $edge->Start_Vertex->y) + ($normal->z * $edge->Start_Vertex->z);
      $d2 = ($normal->x * $edge->Terminate_Vertex->x) + ($normal->y * $edge->Terminate_Vertex->y) + ($normal->z * $edge->Terminate_Vertex->z);
      // print_r($d1);
      // echo "<br/>";
      // print_r($d2);
      // echo "<br/>";

      if (abs(($d1 - $d2)) < 0.00001){
        $face->D_Element  = - $d1;
        break;
      }
    }
  }

  public function joinFaces($face1, $face2){
    // $idArr[] = $face1->Face_ID;
    // $idArr[] = $face2->Face_ID;
    //
    // $key = array_keys($idArr, $face2->Face_ID);
    // // print_r( isset($key));
    //
    // if (!isset($key))
      $face1->Internal_Loop = $face2->External_Loop;

    // $faceArray[] = $face1;

    unset($face2);
    return $face1;
  }
}

?>
