<?php

require_once ('../includes/initialize.php');

$iges_file = 'test.igs';
$_SESSION ['fileid'] = 7;
$parser = new Parser(FILE_REPOSITORY.$iges_file);
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
// Extraction of vertextes to create the vertex list
$vt = new Vertex();
$vt->vertract($dsection, $psection);
$vtlist = $vt->getVertexList();

// var_dump($vtlist);
$edgetype = array();
$rbspline = new RBSplineCurve();
$edgetype = $rbspline->rbsplineCurveTract($dsection, $psection);//, $edgetype);

// var_dump($edgetype);
$edge = new Edge();
$edgelist = $edge->edgetract($dsection, $psection, $edgetype, $vt);

// print_r($edgelist);
//($edge->getEdgeList());
$loops = new Loop();
$loops->looptract($dsection, $psection, $edge, $vtlist);

// print_r($loops);
$bends = new Bend();
$bendz = $bends->bendTract($loops->getLoops());

$x = new Extract();
$dim = $x->getDimensions($gsection);

$bends->insertBendFeatures($bendz, $dim);

// print_r($bendz );
// var_dump($bendz);
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
$file->fileModelUnits = $file->getModelUnits($gsection);
$file->fileModelMaterialsID = 2;

// print_r($gsection);
// echo $file->fileModelUnits;
$file->fileID = 1;
// $file->save();
// $file->delete();
// $file->updateModelUnits();
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
// print_r (IgesFile::find_all());
// foreach ($edgelist as $value) {
// var_dump($value->Edge_List);
// }

//var_dump($_SESSION ['edgelist'][1]->Edge_List);

//var_dump($rbspline);
//var_dump($edgetype);
//var_dump($vtlist);
//echo "$dline1\n";

?>
