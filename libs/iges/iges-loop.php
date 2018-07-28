<?php

class Loop {

  public $Loop_ID;
  public $Normal;
  public $Edge_Count;
  // public $Loop_Concavity;
  public $Loop_Type;
  public $Edge_List; // ARRAY
  // public $Face_Pointers;
  // public $loops;

  static $face;

  function __construct() {
    $this->Edge_List = array();
    // $this->loops = array();
  }

  // Extract loops
  public function looptract($dsection, $psection, $edge, $vertexlist, $dim) {
    global $xtract;
    static $p = 1;

    foreach ( $dsection as $value ) {
      if ($value->EntityType == 508) // && $set == false)
      {
        $pentry = $psection [$value->PointerData];

        $arr = $xtract->multiexplode ( array (
          ",",
          ";"
        ), $pentry );

        $edge504 = ($edge->getEdge504 ());

        // foreach ($edge504 as $key) {
        //   print_r($key);
        //   echo "<br/><br/>";
        // }

        $Edge_List = array ();

        $edgetuple = $arr [1];

        for($j = 2, $id = 1; $j < count ( $arr ); $j ++, $id ++)
        {
          if (($j + 1) >= count ( $arr ) && ($j + 2) >= count ( $arr )) {
            break;
          }

          $type = $arr [$j];
          $pointer = trim ( $arr [$j + 1] );
          $index = array ();

          $in = trim ( $arr [$j + 2] );

          $Edge_List [$id] = new EdgeList ();
          $Edge_List [$id] = $edge504 [$pointer] [$in]->Edge_List;

          if ($id == $edgetuple)
          $id = 1;

          $j += 4;
        }

        if ($Edge_List == null) {
          echo "string";
          continue;
        }

        $this->loops [$value->PointerData] = new Loop ();
        $this->loops [$value->PointerData]->Edge_List = array ();
        $this->loops [$value->PointerData]->Edge_List = $Edge_List;
        $this->loops [$value->PointerData]->Edge_Count = count ( $Edge_List );
        $this->loops [$value->PointerData]->Loop_ID = $p;
        $this->loops [$value->PointerData]->Loop_Type = null;

        foreach ( $Edge_List as $edl ) {
          $i = 1;
          if ($edl->Edge_Type == "Arc") {
            $this->loops [$value->PointerData]->Loop_Type = "BEND";
            break;
          }

          ++ $i;
        }

        $fx = new Computation ( );
        if ($this->loops [$value->PointerData]->Loop_Type != "BEND" || $this->loops [$value->PointerData]->Loop_Type == null) {
          $this->loops [$value->PointerData]->Loop_Type = "FACE";
          $this->loops [$value->PointerData]->Normal = $fx->computeNormal ($this->loops [$value->PointerData]->Edge_List );
        } else
        $this->loops [$value->PointerData]->Normal = $fx->computeNormal ($this->loops [$value->PointerData]->Edge_List );//"bend";

        $p ++;
      }
    }



    self::$face = new Face();
    // var_dump($edge->getEdgeList());
    $rbsurface = self::$face->facetract($dsection, $psection, $this->loops, $vertexlist, $edge->getEdgeList(), $dim);

    // $this->displaystuff($this->loops);
    // var_dump($this->loops);
  }

  public function getLoops() {
    // var_dump(self::$face->getFaceList());
    return self::$face->getFaceList();
  }


  public function displaystuff($Arrr) {
    $loopcnt = 0;
    foreach ($Arrr as $key) {
      // print_r($key);
      // echo "<br/><br/>";
      ++$loopcnt;
    }

    // echo "<br/><br/>";
    // echo "<br/><br/>";
    // echo $loopcnt;
  }

  public function makeLoop($EdgeList, $normal, $ltype) {
    static $i = 1;
    $fx = new Computation ( );
    $loop = new Loop();

    $loop->Loop_ID = $i;
    $loop->Normal = $fx->computeNormal ($EdgeList);
    $loop->Edge_Count = 4;
    $loop->Loop_Type = $ltype;
    $loop->Edge_List = $EdgeList;

    ++$i;
    return $loop;
  }
}

?>
