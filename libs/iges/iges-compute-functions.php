<?php

class Computation {

  private $loop;
  // private $edgeObj;

  function __construct() {
    ;
  }

  public function computeNormal($edgelist)
  {
    $this->loop = $edgelist;
    reset ( $this->loop );
    $edge1 = $this->loop [key ( $this->loop )];
    $edge2 = null;

    foreach ( $this->loop as $edge )
    {
      if ($edge1 == $edge)
      continue;

      if ($edge1->Start_Vertex == $edge->Start_Vertex ||
      $edge1->Terminate_Vertex == $edge->Terminate_Vertex ||
      $edge1->Start_Vertex == $edge->Terminate_Vertex ||
      $edge1->Terminate_Vertex == $edge->Start_Vertex)
      {

        $edge2 = $edge;
        break;
      }
    }

    $a = $this->computeLineVector ( $edge1 );
    $b = $this->computeLineVector ( $edge2 );

    return $this->computeCrossProduct ( $a, $b );
  }

  private function computeLineVector($line) {
    $a = new Vertex ();
    // echo ($line->Start_Vertex->x)."\n";
    $a->x = $line->Start_Vertex->x - $line->Terminate_Vertex->x;
    $a->y = $line->Start_Vertex->y - $line->Terminate_Vertex->y;
    $a->z = $line->Start_Vertex->z - $line->Terminate_Vertex->z;

    return $a;
  }

  private function computeCrossProduct($a, $b) {
    $normal = new Vertex ();

    // var_dump($a->z);
    // echo "<br/>";
    // print_r($b->z);
    // echo "<br/>";

    $normal->x = ($a->y * $b->z) - ($a->z * $b->y);
    $normal->y = - (($a->x * $b->z) - ($b->x * $a->z));
    $normal->z = ($a->x * $b->y) - ($b->x * $a->y); /* */

    return $normal;
  }

  public function computeBendLength($line) {
    $a = new Vertex ();

    $a->x = $line->Start_Vertex->x - $line->Terminate_Vertex->x;
    $a->y = $line->Start_Vertex->y - $line->Terminate_Vertex->y;
    $a->z = $line->Start_Vertex->z - $line->Terminate_Vertex->z;

    return $this->computeEuclideanNorm ( $a );
  }

  public function computeLength($line) {
    $a = new Vertex ();

    $a->x = $line->Start_Vertex->x - $line->Terminate_Vertex->x;
    $a->y = $line->Start_Vertex->y - $line->Terminate_Vertex->y;
    $a->z = $line->Start_Vertex->z - $line->Terminate_Vertex->z;

    return $this->computeEuclideanNorm ( $a );
  }

  private function computeEuclideanNorm($vector) {
    // print_r($vector);
    // echo "<br/>";
    $A = $vector->x * $vector->x;
    $B = $vector->y * $vector->y;
    $C = $vector->z * $vector->z;

    // echo ($A + $B + $C);
    return sqrt ( $A + $B + $C );
  }

  private function computeDotProduct($v1, $v2) {
    $A = $v1->x * $v2->x;
    $B = $v1->y * $v2->y;
    $C = $v1->z * $v2->z;

    return ($A + $B + $C);
  }

  public function distanceBTWPlanes($n1, $n2){
    $num = ($n1->x)*($n2->x) + ($n1->y)*($n2->y) + ($n1->z)*($n2->z);
    $den = $this->computeEuclideanNorm($n1);

    // echo $num;
    $distance = $num / $den;

    return $distance;
  }

  public function computeAngle($normal1, $normal2) {

    $dotp = $this->computeDotProduct ( $normal1, $normal2 );

    $En1 = $this->computeEuclideanNorm ( $normal1 );
    $En2 = $this->computeEuclideanNorm ( $normal2 );

    if ($dotp == 0)
    $cosine = 0;
    else

    $cosine = $dotp / ($En1 * $En2);
    // echo "Cossine = ".$cosine."<br/>";
    $radian = acos ( $cosine );

    $angle = rad2deg ( $radian );

    return round($angle);
  }

  public function isSidePlane($edgelist, $thickness) {
    $count = 0;

    foreach ($edgelist as $edge) {
      $length = $this->computeLength($edge);

      if (abs(($length-$thickness)) < 0.00001){
        ++$count;
        // echo $length."  <br/>";
        }
      else {
        // echo $length."  <br/>";
      }
    }
    // echo "<br/>";
    if ($count === 2) {
      return TRUE;
    }


    return FALSE;
  }

  public function computeThickness($edgelist)
  {
    $this->loop = $edgelist;
    reset ( $this->loop );
    $arr = array();
    // $edge1 = $this->loop [key ( $this->loop )];
    // $edge2 = null;

    foreach ( $this->loop as $edge )
    {
      $arr[]= $this->computeBendLength($edge);
    }

    natsort($arr);

    // print_r($arr);
    // var_dump($arr);
    // echo "<br><br>";
    $thickness = array_shift($arr);
    // return smallest value
    return $thickness;
  }

  public function computeParallel($n1, $n2){
    $q  = $this->computeCrossProduct($n1, $n2);
    $A  =  $this->computeAngle($n1, $n2);

    $B = $this->computeEuclideanNorm ( $q );
    // $A = round($A);
    // if ($A == 0)
    //   $A = 180;


    // if ((abs((180 - $A)) < 0.00001) || $A == 180 || (abs((0 - $A)) < 0.00001) || $A == 0)
    if (round($B) == 0){
        // echo "Angle = ".$A." ";
      return TRUE;
    } else {
      // echo "Angle = ".$A." ";
      // echo $A." ";
      return FALSE;
    }
  }

  public function computeDistanceBTWPlanes($Plane1, $Plane2) {
    $P1_vertex = $P2_vertex = array();
    $distance1 = $distance2 = array();
    // ->External_Loop->Normal

    // echo "<br/><br/>";
    // echo $Plane1->Face_ID." and ".$Plane2->Face_ID." <br/> ";

    $normal1 = $Plane1->External_Loop->Normal;
    $D1 = $Plane1->D_Element;

    $normal2 = $Plane2->External_Loop->Normal;
    $D2 = $Plane2->D_Element;
    // print_r($normal1);
    // echo "  D = ".$D1."<br/>";
    // print_r($normal1);
    // echo "  D = ".$D2."<br/>";
    foreach ($Plane2->External_Loop->Edge_List as $value) {
      $P1_vertex[] = $value->Start_Vertex;
      $P1_vertex[] = $value->Terminate_Vertex;
      // print_r($value->Start_Vertex);
      // echo "<br/>";
      // print_r($value->Terminate_Vertex);
      // echo "<br/>";
    }
    // echo "<br/><BR/>";
    // print_r($P1_vertex);

    // Plane1 and vertex from Plane2
    foreach ($P1_vertex as $edge) {
      // var_dump($edge);
      $num = (($normal1->x * $edge->x) + ($normal1->y * $edge->y) + ($normal1->z * $edge->z) + $D1);

      // print_r($edge);
      // echo "*****<br/>";
      if ($num < 0)
        $num = -$num;

      $den =  sqrt(($normal1->x * $normal1->x) + ($normal1->y * $normal1->y) + ($normal1->z * $normal1->z));

      $distance1 = abs($num / $den);

      // echo "Num = ".$num. " and Den = ".$den."<br/>";
      // print_r($distance1);
    }
    // echo "<br/><br/>";

    foreach ($Plane1->External_Loop->Edge_List as $value) {
      $P2_vertex[] = $value->Start_Vertex;
      $P2_vertex[] = $value->Terminate_Vertex;
      // print_r($value);
      // echo "<br/>";
    }


    // Plane2 and vertex from Plane1
    foreach ($P2_vertex as $edge) {
      $num = -(($normal2->x * $edge->x) + ($normal2->y * $edge->y) + ($normal2->z * $edge->z) + $D2);

            // print_r($edge);
            // echo "=====<br/>";
      if ($num < 0)
        $num = -$num;

      $den =  sqrt(($normal2->x * $normal2->x) + ($normal2->y * $normal2->y) + ($normal2->z * $normal2->z));

      $distance2 = abs($num / $den);

      // print_r($distance2);
      // echo " ";
    }
    // print_r($distance2);
    // if (($distance1 == $distance2))
    //   echo "AAAAAAAAAAAAAAA";
    // echo $Plane1->Face_ID." and ".$Plane2->Face_ID."  ";
    // echo "Distance between planes = ".$distance1."  <br/>";
    // echo "Distance between planes = ".$distance2."  <br/>";
    // if (($distance1 == $distance2) && (abs(($distance1-$Plane1->Thickness)) < 0.00001))
    if (abs(($distance2-$Plane1->Thickness)) < 0.00001) {

      return TRUE;
    }
    return FALSE;
  }

  public function computeDistanceBTWPlaneVertices($Plane1, $Plane2) {
    $distance1 = $distance2 = $count = 0;
    $thickness = $Plane1->Thickness;

    // echo $Plane1->Face_ID." and ".$Plane2->Face_ID." <br/> ";

    $trimmedPlane1 = $this->computeTrimFace($Plane1->External_Loop->Edge_List);
    $trimmedPlane2 = $this->computeTrimFace($Plane2->External_Loop->Edge_List);

    foreach ($trimmedPlane1 as $key => $point1) {
      foreach ($trimmedPlane2 as $key => $point2) {
        $d = $this->distanceBTW2points($point1, $point2);
        // echo $d.", ";
        if (abs($d - $thickness) < 0.00001) {
          ++$count;
        }
      }
    }

    // echo "<br/>";
    if ($count == 4)
      return true;

    return false;
  }

  public function centreSurfaceGeneration(&$face) {
      $distance1 = $distance2 = $count = 0;
      $thickness = $face->Thickness;
      $A = array();

      // echo $Plane1->Face_ID." and ".$Plane2->Face_ID." <br/> ";
      // echo "<br/>";
      // echo "<br/>";
      $trimmedPlane1 = $this->computeTrimFace($face->External_Loop->Edge_List);
      $trimmedPlane2 = $this->computeTrimFace($face->Internal_Loop->Edge_List);

      foreach ($trimmedPlane1 as $key => $point1) {
        foreach ($trimmedPlane2 as $key1 => $point2) {
          $d = $this->distanceBTW2points($point1, $point2);
          // echo $d.", ";
          if (abs($d - $thickness) < 0.00001) {
            ++$count;
            // $A[$key] = new Vertex();
            $A[$key] = $this->midPoint($point1, $point2);
            break;
          }
        }
      }

      // $face

      // echo "<br/>";
      if ($count == 4){
        // foreach ($A as $key => $value) {
        //   print_r(($value));
        //   echo "<br/><br/>";
        // }
        $edge = new Edge();
        $lp = new Loop();
        $edgelist = $edge->makeEdgeList($A);
        $loop = $lp->makeLoop($edgelist, $face->External_Loop->Normal, $face->External_Loop->Loop_Type);
        // print_r($loop);
        // echo "******************************************<br/>";
        return $loop;
      }

      // $face->Central_Loop = $loop;
      // echo $face->Surface_Type;
      // echo "<br/>";
      // print_r($face->External_Loop->Normal);
      // echo "<br/><br/>";
      // print_r($face->Internal_Loop->Normal);
      // echo "<br/><br/>";
      // print_r($face->Central_Loop->Normal);
      // echo "<br/><br/>";

      return $loop;
  }

  public function midPoint($point1, $point2) {
    $mid = new Vertex();

    $mid->x = ($point1->x + $point2->x) / 2;
    $mid->y = ($point1->y + $point2->y) / 2;
    $mid->z = ($point1->z + $point2->z) / 2;

    return $mid;
  }

  public function distanceBTW2points($point1, $point2) {
    $sqr = $d = 0;
    // if (($point1->x == $point2->x && $point1->y == $point2->y) ||
    //     ($point1->z == $point2->z && $point1->y == $point2->y) ||
    //     ($point1->x == $point2->x && $point1->z == $point2->z))

    $a = ($point1->x - $point2->x);
    $b = ($point1->y - $point2->y);
    $c = ($point1->z - $point2->z);

    $sqr = ($a * $a) + ($b * $b) + ($c * $c);
    $d = sqrt($sqr);

    return $d;
  }

  public function computerGradient($edge) {
    $X = $edge->Start_Vertex->x - $edge->Terminate_Vertex->x;
    $Y = $edge->Start_Vertex->y - $edge->Terminate_Vertex->y;
    $Z = $edge->Start_Vertex->z - $edge->Terminate_Vertex->z;

    $run = sqrt(($X * $X) + ($Y * $Y));
    $rise = $Z;

    // echo ", ";
    $slope = $rise / $run;

    return $slope;
  }

  public function computeTrimFace($Edge_List) {
    $P2_vertex = array();

    foreach ($Edge_List as $value) {
      // echo $this->computerGradient($value)."<br/>";
      // print_r($value);
      // echo "<br/>";
      // echo "<br/>";
      $P1_vertex[] = $value->Start_Vertex;
      $P1_vertex[] = $value->Terminate_Vertex;
      // $P1_vertex['Edge_Type'] = $value->Edge_Type;
    }

    // echo "<br/><br/>";
    // echo $Plane2->Face_ID."<br/>";
    // echo "<br/>";
    // echo "<br/>";
    $copyto = $P1_vertex;

    $recopy = array();

    // foreach ($copyto as $key => $values) {
    //   print_r($values);echo "<br/>";
    // }

    // echo "<br/>";
    // echo "<br/>";
    $vl = array_shift($copyto);

    foreach ($copyto as $key => $values) {
      $keys = array_keys($copyto, $vl);

      // print_r($keys);echo "<br/>";

      $recopy[] = $vl;

      foreach($keys as $key)
        unset($copyto[$key]);

      // foreach ($copyto as $key => $values) {
      //   print_r($values);echo "<br/>";
      // }

      if (empty($copyto)){
        // print_r(count($recopy));
        break;
      }

      $vl = array_shift($copyto);
    }

    return $recopy;
  }

  public function computeConcavity($bend, $face1, $face2) {
    $concavity = null;
    $loop1 = new Loop ();
    $loop2 = new Loop ();

    $loop1 = $face1->External_Loop;
    $loop2 = $face2->External_Loop;

    $Na = $loop1->Normal;
    $Nb = $loop2->Normal;

    foreach ( $loop1->Edge_List as $edl ) {
      if ($edl->Start_Vertex != $bend->Start_Vertex && $edl->Start_Vertex != $bend->Terminate_Vertex) {
        $Pa = $edl->Start_Vertex;
        break;
      } else if ($edl->Terminate_Vertex != $bend->Terminate_Vertex && $edl->Terminate_Vertex != $bend->Start_Vertex) {
        $Pa = $edl->Terminate;
        break;
      } else
      continue;
    }

    foreach ( $loop2->Edge_List as $edl ) {
      if ($edl->Start_Vertex != $bend->Start_Vertex && $edl->Start_Vertex != $bend->Terminate_Vertex) {
        $Pb = $edl->Start_Vertex;
        break;
      } else if ($edl->Terminate_Vertex != $bend->Terminate_Vertex && $edl->Terminate_Vertex != $bend->Start_Vertex) {
        $Pb = $edl->Terminate_Vertex;
        break;
      } else
      continue;
    }

    $diff = new Vertex ();
    $diff1 = new Vertex ();

    $diff->x = $Pb->x - $Pa->x;
    $diff->y = $Pb->y - $Pa->y;
    $diff->z = $Pb->z - $Pa->z;

    $diff1->x = $Pa->x - $Pb->x;
    $diff1->y = $Pa->y - $Pb->y;
    $diff1->z = $Pa->z - $Pb->z;

    $con = $this->computeDotProduct ( $diff, $Na );
    $con1 = $this->computeDotProduct ( $diff1, $Nb );

    if (($con < 0 && $con1 < 0) || ($con > 0 && $con1 > 0))
    ;
    else
    ;

    if ($con == $con1) {
      if ($con <= 0)
      $concavity = "Convex";
      else
      $concavity = "Concave";
    }

    return $concavity;
  }

  private function toMM($value, $unit) {
    $out = 0;
    switch ($unit) {
      case 1 :
      $out = $value * 25.4;
      break;
      case 2 :
      $out = $value;
      break;
      case 4 :
      $out = $value * 304.8;
      break;
      case 5 :
      $out = $value * 1.609e6;
      break;
      case 6 :
      $out = $value * 1000;
      break;
      case 7 :
      $out = $value * 1000000;
      break;
      case 8 :
      $out = $value * 0.0254;
      break;
      case 9 :
      $out = $value * 0.001;
      break;
      case 10 :
      $out = $value * 10;
      break;
      default :
      break;
    }

    return $out;
  }

  public function computeBendingForce($bendlength, $thickness, $unit, $TS) {
    $KBS = 1.33;

    $D = 8 * $thickness;

    $bendl = $this->toMM ( $bendlength, $unit );

    $force = ($KBS * $TS * $bendlength * $thickness * $thickness) / $D;

    return $force;
  }
}

$compute = new Computation();
?>
