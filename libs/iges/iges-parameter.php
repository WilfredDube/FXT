<?php

class Point {
	public $x;
	public $y;
	public $z;
}

class VertexList {
	public $Vertex_ID;
	public $Vertex_Count;
	public $Vertex; // ARRAY
}
$vertexlist = new VertexList();

/* ENTIRY 128 RBSpline Surface */
class SurfaceType {
	public $K1;
	public $K2;
	public $Degree1;
	public $Degree2;
	// public $Degree;
	public $PROP1;
	public $PROP2;
	public $PROP3;
	public $PROP4;
	public $PROP5;
	public $Knot_Sequence1; // ARRAY
	public $Knot_Sequence2; // ARRAY
	public $Weights; // ARRAY
	public $Control_Points; //
}
class EdgeList {
	public $Edge_Count;
	public $Edge_List; // ARRAY

	function getEdgeType() {
		return $this->edgetype;
	}

}
class Edges504 {
	public $Edgeentity; // ARRAY
}

class Surface {
	public $Surface_Type;
}

?>
