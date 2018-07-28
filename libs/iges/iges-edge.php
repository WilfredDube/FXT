<?php

class Edge {

  public $Edge_ID;
  public $Edge_Type;
  public $Start_Vertex;
  public $Terminate_Vertex;
  //public $Concavity;
  //public $Face_Pointer; // ARRAY
  //public $Loop_Pointer; // ARRAY
  //public $Dimension;
  public static $edge504;
  private static $edgelist;

  function __construct () {
    self::$edgelist = array();
    //$this->Face_Pointer = array();
    //$this->Loop_Pointer = array();
  }

  public function edgetract($dsection=null, $psection = null, $edgetype = null, $vertex = null)
  {
    global $xtract;
    $counter = 0;
    $arccount = 0;

    $vertexlist = $vertex->getVertexList();

    if ($dsection != null)
    foreach ( $dsection as $value )
    {
      if ($value->EntityType == 504)
      {
        $pentry = $psection [$value->PointerData];

        $arr = $xtract->multiexplode ( array (
          ",",
          ";"
        ), $pentry );

        for($j = 2, $id = 1; $j < count ( $arr ); $j ++, $id ++) {
          $edg = new Edge();

          if (($j + 1) >= count ( $arr ) && ($j + 2) >= count ( $arr )) {
            break;
          }
          ++ $counter;

          $pointer = trim ( $arr [$j] );
          $sindex = trim ( $arr [$j + 2] );
          $tindex = trim ( $arr [$j + 4] );

          $edg->Edge_ID = $id;

          if ($edgetype [$dsection [$pointer]->PointerData]->PROP3 == 1)
          $edg->Edge_Type = "Line";
          else {
          $edg->Edge_Type = "Arc";
          ++$arccount;
          }



          $edg->Start_Vertex = new Vertex ();
          $edg->Start_Vertex = $vertexlist [$sindex]->Vertex;
          $edg->Terminate_Vertex = new Vertex ();
          $edg->Terminate_Vertex = $vertexlist [$tindex]->Vertex;

          self::$edgelist [$id] = new EdgeList ();
          self::$edgelist [$id]->Edge_Count = trim ( $arr [1] );
          self::$edgelist [$id]->Edge_List = array ();
          self::$edgelist [$id]->Edge_List = $edg;

          $_SESSION ['edgelist'][$id] = self::$edgelist[$id];

          $j = $j + 4;

          foreach ( $edgetype as $edt ) {
            $v = new Vertex ();
            $v1 = new Vertex ();

            $c = $edt->Control_Pend;

            $v->x = $edt->Control_Points [0];
            $v->y = $edt->Control_Points [1];
            $v->z = $edt->Control_Points [2];

            $v1->x = $edt->Control_Points [$c - 3];
            $v1->y = $edt->Control_Points [$c - 2];
            $v1->z = $edt->Control_Points [$c - 1];
          }
        }
      }

      if ($value->EntityType == 504) {
        self::$edge504 [$value->LineNumber] = new EdgeList ();
        self::$edge504 [$value->LineNumber] = $_SESSION ['edgelist'];
        $_SESSION ['edge504'] = self::$edge504;
      }
    }

    // echo $arccount;

    return $_SESSION ['edgelist'];
  }

  public function getEdge504() {
    return $_SESSION ['edge504'];
  }


  public function getEdgeList() {
    return $_SESSION ['edgelist'];
  }

  // REturns Edgelist array
  public function makeEdgeList($trimmedVertexArray) {
    $EdgeLIST = array();
    $edges = 4;
    $edgeID = 1;
    for ($i=0, $j = 1; $i < count($trimmedVertexArray); $i++, $j++, $edgeID++) {
      if ($i == 3){
        $j = 0;
      }

      $edge = new Edge();
      $edge->Edge_ID = $edgeID;
      $edge->Start_Vertex = $trimmedVertexArray[$i];
      $edge->Terminate_Vertex = $trimmedVertexArray[$j];

      $EdgeLIST[] = $edge;
    }

    return $EdgeLIST;
  }
}

?>
