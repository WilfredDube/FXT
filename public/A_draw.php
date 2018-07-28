<?php
//ob_end_flush();
require_once('../includes/initialize.php');

ini_set('display_errors','off');

//if logged in redirect to members page
if(!$user->is_logged_in()){
  unset($_POST['cool']);
  unset($_POST['process']);
  header('Location: login.php');
}

$_POST['pro'] = 1;
$_POST['cool'] = 1;
$_POST['process'] = 1;

$fileID = trim($_GET['id']);

// echo $_SESSION['projectid'];
$query = "select filename from files where fileid=? LIMIT 1";
$rows = $database->getRow($query, [$fileID]);
// print_r(array_shift($rows));
// foreach ($rows as $row){
$iges_file = array_shift($rows);

// echo "File : ".$iges_file;

$_SESSION['fileid'] = $fileID;
//echo $iges_file;
// }

//$title = 'Draw';
//include header template
//require('layout/header.php');
?>
<?php
ob_end_flush();

$FILE_REPOSITORY = User::getUserFolder();
$parser = new Parser($FILE_REPOSITORY.'/'.$iges_file);
// print_r($parser);
$total = $parser->count_dline();

$parser->get_back();
$dline1 = $parser->get_line();
$dline1 = $parser->jump_to_dsection($dline1);
$dline2 = $parser->get_line();

$parser->parse_d_entry($dline1, $dline2);

for ($i = 1; strpos($dline1, 'D') == true; $i++)
{
  $dline1 = $parser->get_line();
  $dline2 = $parser->get_line();

  if ($dline2 == null)
    break;

  $parser->parse_d_entry($dline1, $dline2);
}

$psection = $parser->param_section();
$gsection = $parser->global_section();
$dsection = $parser->getDsection();

// var_dump($gsection);
// var_dump($vtlist);
$edgetype = array();
$rbspline = new RBSplineCurve();
$edgetype = $rbspline->rbsplineCurveTract($dsection, $psection);//, $edgetype);

// var_dump($edgetype);

// Extraction of vertextes to create the vertex list
$vt = new Vertex();
$vtlist = $vt->vertract($dsection, $psection);
// $vtlist = $vt->getVertexList();

// if (!empty($vlist))
// var_dump($vtlist);

// var_dump($edgetype);
$edge = new Edge();
$edgelist = $edge->edgetract($dsection, $psection, $edgetype, $vt);

// var_dump($edgelist);
//($edge->getEdgeList());
$x = new Extract();
$dim = $x->getDimensions($gsection);
$loops = new Loop();
$loops->looptract($dsection, $psection, $edge, $vtlist, $dim);

// var_dump($loops);
// var_dump($loops->getLoops());
// print_r($loops);
$bends = new Bend();
$bendz = $bends->bendTract($loops->getLoops());
//
//
$bends->insertBendFeatures($bendz, $dim);

// var_dump($bendz);
// print_r($edgetype[90]->Control_Points);
//$xt->RBSplineSurface();
$array = $edgetype;

// echo $fileID;
// $row = BendFeatures::find_feature_by_id($fileID);
// print_r($row);
// var_dump($edgetype);
// echo $array;
// $loops = ($xt->getLoops ());
//$shell = $xt->getShell();

// $edget = $xt->getEdge504 ();

/*$i = 1;
foreach ($edget as $edgt)
        foreach ($edgt as $elist){
        echo $i."=> ";
        print_r($elist->Edge_List->Edge_Type);//." ";
        echo "<br/>";
        $i++;
        }
*/
// $xt->display();
//$_SESSION['BENDS'] = arra();
// $_SESSION['BENDS'] = serialize($xt->getBends());

//print_r (unserialize($_SESSION['BENDS']));
//$xt->facetract();

?>
<head>
<?php
//include header template
//require('layout/footer.php');
?>
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<style>
			body {
				width: 50%;
				/*background: #B3B3B3;*
				bottom: 10px;*/
				/*cursor: crosshair;*/
				/*//margin-top: 5px;
				//margin-left: 5px;*/
			}
			#info {
				position: absolute;
				top: 10px;
				width: 100%;
				padding: 5px;
				text-align:center;
			}
		</style>
	</head>
  <body>

    <canvas id="debug" style="position:absolute; left:100px"></canvas>

    <script src="assets/js/three.min.js"></script>

    <script src="assets/js/curves/NURBSCurve.js"></script>
    <script src="assets/js/curves/NURBSUtils.js"></script>

    <script src="assets/js/renderers/Projector.js"></script>
    <script src="assets/js/renderers/CanvasRenderer.js"></script>

    <script src="assets/js/libs/stats.min.js"></script>

    <script src="assets/js/controls/TrackballControls.js"></script>
    <script src="assets/js/controls/OrbitControls.js"></script>
    <script src="assets/js/Detector.js"></script>
    <script src="assets/js/libs/stats.min.js"></script>

    <script>
  window.onload = function() {
  var renderer,
    scene,
    camera,
    controls,
    meshMaterial;

  if ( ! Detector.webgl ) Detector.addGetWebGLMessage();

  renderer = new THREE.WebGLRenderer({ antialias: true });
  document.body.appendChild( renderer.domElement );
  renderer.setSize( 970, 570 );
  renderer.setClearColor( 0xB3B3B3);
  //renderer.setClearColorHex( 0xeeeeee, 1.0 );

  scene = new THREE.Scene();
          group = new THREE.Group();
        group.position.y = 0;
        scene.add( group );

        init();

  // Add axes
  //axes = buildAxes( 1000 );
  //scene.add( axes );

        var ax = new THREE.AxisHelper(400);
  scene.add(ax);

  var gridHelper = new THREE.GridHelper( 400, 10 );
  scene.add( gridHelper );

  var dir = new THREE.Vector3( 1, 0, 0 );
  var origin = new THREE.Vector3( 0, 0, 0 );
  var length = 50;
  var hex = 0xff0000;

  var arrowHelper = new THREE.ArrowHelper( dir, origin, length, hex );
  scene.add( arrowHelper );

  var dir = new THREE.Vector3( 0, 1, 0 );
  var origin = new THREE.Vector3( 0, 0, 0 );
  var length = 50;
  var hex = 0x00ff00;

  var arrowHelper = new THREE.ArrowHelper( dir, origin, length, hex );
  scene.add( arrowHelper );

  var dir = new THREE.Vector3( 0, 0, 1 );
  var origin = new THREE.Vector3( 0, 0, 0 );
  var length = 50;
  var hex = 0x0000ff;

  var arrowHelper = new THREE.ArrowHelper( dir, origin, length, hex );
  scene.add( arrowHelper );

  // We need a camera to look at the scene!
  camera = new THREE.PerspectiveCamera( 45, 1000 / 700, 1, 10000 );
  camera.position.set( 30, 100, 300 );
  camera.lookAt( new THREE.Vector3( 0, 0, 0 ) );


  // And some sort of controls to move around
  // We'll use one of THREE's provided control classes for simplicity
  controls = new THREE.TrackballControls( camera );
  controls.rotateSpeed = 10.0;
  controls.zoomSpeed = 0.2;
  controls.panSpeed = 4.8;

  controls.noZoom = false;
  controls.noPan = false;

  controls.staticMoving = true;
  controls.dynamicDampingFactor = 0.3;
        //controls.dollyOut = function();

  // and go!
  animate();

  function animate() {
    requestAnimationFrame( animate );
    controls.update();
    renderer.render( scene, camera );
  }
  /*
  function buildAxes( length ) {
    var axes = new THREE.Object3D();

    axes.add( buildAxis( new THREE.Vector3( 0, 0, 0 ), new THREE.Vector3( length, 0, 0 ), 0xFF0000, false ) ); // +X
    axes.add( buildAxis( new THREE.Vector3( 0, 0, 0 ), new THREE.Vector3( -length, 0, 0 ), 0xFF0000, true) ); // -X
    axes.add( buildAxis( new THREE.Vector3( 0, 0, 0 ), new THREE.Vector3( 0, length, 0 ), 0x00FF00, false ) ); // +Y
    axes.add( buildAxis( new THREE.Vector3( 0, 0, 0 ), new THREE.Vector3( 0, -length, 0 ), 0x00FF00, true ) ); // -Y
    axes.add( buildAxis( new THREE.Vector3( 0, 0, 0 ), new THREE.Vector3( 0, 0, length ), 0x0000FF, false ) ); // +Z
    axes.add( buildAxis( new THREE.Vector3( 0, 0, 0 ), new THREE.Vector3( 0, 0, -length ), 0x0000FF, true ) ); // -Z

    return axes;

  }

  function buildAxis( src, dst, colorHex, dashed ) {
    var geom = new THREE.Geometry(),
      mat;

    if(dashed) {
      mat = new THREE.LineDashedMaterial({ linewidth: 3, color: colorHex, dashSize: 3, gapSize: 3 });
    } else {
      mat = new THREE.LineBasicMaterial({ linewidth: 3, color: colorHex });
    }

    geom.vertices.push( src.clone() );
    geom.vertices.push( dst.clone() );
    geom.computeLineDistances(); // This one is SUPER important, otherwise dashed lines will appear as simple plain lines

    var axis = new THREE.Line( geom, mat, THREE.LinePieces );

    return axis;

  }*/

  function init()
  {

        // NURBS curve
          <?php
            $p = 0;
            foreach ($array as $value)
            {
                 $nurbsDegree = $value->Degree;
                 $nurbsKnots = $value->Knot_Sequence;
          ?>
          var nurbsControlPoints = [
          <?php	     for ($i = 0, $j = 0; $i < count($value->Control_Points); $i++, $j++){

              /* X */
              if ($value->Control_Points[$i] == 0.)
                $x = 0.0;
              else if ($value->Control_Points[$i] == 1.)
                $x = 1.0*10;
              else if ($value->Control_Points[$i] == 2.)
                $x = 2.0*10;
              else if ($value->Control_Points[$i] == 3.)
                $x = 3.0*10;
              else if ($value->Control_Points[$i] == 4.)
                $x = 5.0*10;
              else if ($value->Control_Points[$i] == 6.)
                $x = 6.0*10;
              else if ($value->Control_Points[$i] == 7.)
                $x = 7.0*10;
              else if ($value->Control_Points[$i] == 8.)
                $x = 8.0*10;
              else if ($value->Control_Points[$i] == 9.)
                $x = 9.0*10;
              else {
                $x = $value->Control_Points[$i]*10;
              }

              /* Y */
              if ($value->Control_Points[$i + 1] == 0.)
                $y = 0.0*10;
              else if ($value->Control_Points[$i + 1] == 1.)
                $y = 1.0*10;
              else if ($value->Control_Points[$i + 1] == 2.)
                $y = 2.0*10;
              else if ($value->Control_Points[$i + 1] == 3.)
                $y = 3.0*10;
              else if ($value->Control_Points[$i + 1] == 4.)
                $y = 5.0*10;
              else if ($value->Control_Points[$i + 1] == 6.)
                $y = 6.0*10;
              else if ($value->Control_Points[$i + 1] == 7.)
                $y = 7.0*10;
              else if ($value->Control_Points[$i + 1] == 8.)
                $y = 8.0*10;
              else if ($value->Control_Points[$i + 1] == 9.)
                $y = 9.0*10;
              else {
                $y = $value->Control_Points[$i + 1]*10;
              }

              /* Z */
              if ($value->Control_Points[$i + 2] == 0.)
                $z = 0.0*10;
              else if ($value->Control_Points[$i + 2] == 1.)
                $z = 1.0*10;
              else if ($value->Control_Points[$i + 2] == 2.)
                $z = 2.0*10;
              else if ($value->Control_Points[$i + 2] == 3.)
                $z = 3.0*10;
              else if ($value->Control_Points[$i + 2] == 4.)
                $z = 5.0*10;
              else if ($value->Control_Points[$i + 2] == 6.)
                $z = 6.0*10;
              else if ($value->Control_Points[$i + 2] == 7.)
                $z = 7.0*10;
              else if ($value->Control_Points[$i + 2] == 8.)
                $z = 8.0*10;
              else if ($value->Control_Points[$i + 2] == 9.)
                $z = 9.0*10;
              else {
                $z = $value->Control_Points[$i + 2]*10;
              }


              if ($value->Weights[$j] == 0.)
                $w = 0.0;
              else if ($value->Weights[$j] == 1.)
                $w = 1.0;
              else if ($value->Weights[$j] == 2.)
                $w = 2.0;
              else if ($value->Weights[$j] == 3.)
                $w = 3.0;
              else if ($value->Weights[$j] == 4.)
                $w = 5.0;
              else if ($value->Weights[$j] == 6.)
                $w = 6.0;
              else if ($value->Weights[$j] == 7.)
                $w = 7.0;
              else if ($value->Weights[$j] == 8.)
                $w = 8.0;
              else if ($value->Weights[$j] == 9.)
                $w = 9.0;
              else
                $w = $value->Weights[$j];

            if(($i + 3) == count($value->Control_Points) )
              $s = " ";
            else
              $s = ",";
        ?>
  new THREE.Vector4 (<?php echo ($x);?>, <?php echo ($y);?>, <?php echo ($z);?>,<?php echo ($w);?>)<?php echo $s;?>
        <?php
                  $i += 2;
                 }
                    ++$p;
          ?>
          ];

          var nurbsDegree = <?php echo $nurbsDegree; ?>;
          var nurbsKnots = [
          <?php
            for ($i = 0; $i < count($nurbsKnots); $i++){
              if(($i + 1) == count($nurbsKnots))
                $s = " ";
              else
                $s = ",";

              if ($nurbsKnots[$i] == 0.)
                echo "0.0".$s;
              else if ($nurbsKnots[$i] == 1.)
                echo "1.0".$s;
              else if ($nurbsKnots[$i] == 2.)
                echo "2.0".$s;
              else if ($nurbsKnots[$i] == 3.)
                echo "3.0".$s;
              else if ($nurbsKnots[$i] == 4.)
                echo "4.0".$s;
              else if ($nurbsKnots[$i] == 5.)
                echo "5.0".$s;
              else if ($nurbsKnots[$i] == 6.)
                echo "6.0".$s;
              else if ($nurbsKnots[$i] == 7.)
                echo "7.0".$s;
              else if ($nurbsKnots[$i] == 8.)
                echo "8.0".$s;
              else if ($nurbsKnots[$i] == 9.)
                echo "9.0".$s;
              else
              {
                echo ($nurbsKnots[$i])." ".$s;
              }
            }
          ?>
          ];
          drawNURBSCurves(nurbsControlPoints, nurbsKnots, nurbsDegree);
          <?php
            }

          ?>

  }

        function drawNURBSCurves(nurbsControlPoints, nurbsKnots, nurbsDegree)
  {
    var nurbsCurve = new THREE.NURBSCurve(nurbsDegree, nurbsKnots, nurbsControlPoints);

    var nurbsGeometry = new THREE.Geometry();
    nurbsGeometry.vertices = nurbsCurve.getPoints( 0 );
    var nurbsMaterial = new THREE.LineBasicMaterial( { linewidth: 3, color: 0x443334 } );//0x443344

    var nurbsLine = new THREE.Line( nurbsGeometry, nurbsMaterial );
    nurbsLine.position.set( 0, 0, 0 );
    group.add( nurbsLine );

    var nurbsControlPointsGeometry = new THREE.Geometry();
    nurbsControlPointsGeometry.vertices = nurbsCurve.controlPoints;
                var nurbsControlPointsMaterial = new THREE.LineBasicMaterial( { linewidth: 4, color: 0xDF0101,opacity: 0.25  } );

    var nurbsControlPointsLine = new THREE.Line( nurbsControlPointsGeometry, nurbsControlPointsMaterial );
        nurbsControlPointsLine.position.copy( nurbsLine.position );

    //group.add(nurbsControlPointsLine );
    // this also works:
    //group.add( nurbsLine ).add( nurbsControlPointsLine );
  }
  }

    </script>
  <?php //print_r ($_SESSION['ERROR']); ?>
  </body>
</html>
