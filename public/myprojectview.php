<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('file_uploads','on');
require_once('../includes/initialize.php');

// ini_set('display_errors','on');
//ini_set('file_uploads','on');
//ini_set('upload_temp_dir','/home/digitalincentives/www/loginregister/uploads/');

//if not logged in redirect to login page
if (!$user->is_logged_in()) {
    header('Location: login.php');
}

$_POST['cool'] = 1;
$pid = trim($_GET['pid']);

$_SESSION['projectid'] = $pid;

//define page title
$title = 'My Projects';

// print_r(Tool::findToolByAngle(90));
//include header template
require('templates/header.php');

?>
<?php
//ob_end_flush();
//xsession_start();
// include ('includes/config.php');
// ODO: Fix
// include('classes/extract.php');
//require('classes/parser.php');

//ini_set('display_errors','on');

//if logged in redirect to members page
if(!$user->is_logged_in()){
  unset($_POST['cool']);
  unset($_POST['process']);
  header('Location: login.php');
}
/*
$_POST['pro'] = 1;*/
$_POST['cool'] = 1;
$_POST['process'] = 1;

// TOD: Fix lines below
$fileID = trim($_GET['fid']);
// echo $fileID;
$query = "select filename from files where fileid=? LIMIT 1";

$res = $database->getRow($query, [$fileID]);
$iges_file = array_shift($res);
$title = 'Draw';
//include header template
require('templates/header.php');




/*
if (isset($_SESSION['BENDS'])){
$obj = (($_SESSION['BENDS']));
//$objj = unserialize($obj);
$temp = (unserialize($obj));
print_r($temp);
}*/
$result = BendFeatures::find_feature_by_id($fileID);
// foreach ($result as $row)
//   break;

?>

<script src="assets/js/springy.js"></script>
<script src="assets/js/springyui.js"></script>
<link rel="stylesheet" href="style/draw.css">
<link rel="stylesheet" href="style/overlay.css">
<style>
/*html{
  background: linear-gradient(#08f, #fff);
  padding: 40px;
  width: 170px;
  height: 100%;
  margin: 0 auto;
}*/

.trafficlight{
  background: #222;
  background-image: linear-gradient(transparent 2%, #111 2%, transparent 3%, #111 30%);
  width: 20px;
  height: 20px;
  border-radius: 20px;
  /*position: relative;*/
  /*border: solid 5px #333;*/
}

.trafficlight:before{
  background: #222;
  background-image: radial-gradient(#444, #000);
  content: "";
  /*width: 170px;
  height: 150px;*/
  margin: 0 auto;
  /*position: absolute;*/
  top: -20px;
  margin-left: 0px;
  border-radius: 50%;
  z-index: -1;
}

.trafficlight:after{
  background: #222;
  background-image: linear-gradient(-90deg, #222 0%, #444 30%, #000);
  content: "";
  /*width: 50px;
  height: 500px;*/
  margin-left: 60px;
  /*position: absolute;*/
  top: 150px;
  z-index: -1;
}

.protector{
  background: transparent;
  width: 180px;
  height: 0;
  /*position: absolute;*/
  top: 20px;
  left: -35px;
  border-right: solid 30px transparent;
  border-left: solid 30px transparent;
  border-top: solid 90px #111;
  border-radius: 10px;
  z-index: -1;
}

.protector:nth-child(2){
  top: 140px;
}

.protector:nth-child(3){
  top: 260px;
}

.red{
  background: red;
  background-image: radial-gradient(brown, transparent);
  background-size: 5px 5px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  /*position: absolute;*/
  top: 20px;
  left: 35px;
  animation: 1s red infinite;
  border: dotted 2px red;
  box-shadow:
    0 0 10px #111 inset,
    0 0 10px red;
}

.yellow{
  background: yellow;
  background-image: radial-gradient(orange, transparent);
  background-size: 5px 5px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: dotted 2px yellow;
  /*position: absolute;*/
  /*top: 145px;
  left: 35px;*/
  animation: 1s yellow infinite;
  box-shadow:
    0 0 20px #111 inset,
    0 0 10px yellow;
}

.green{
  background: green;
  background-image: radial-gradient(lime, transparent);
  background-size: 5px 5px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: dotted 2px lime;
  /*position: absolute;*/
  /*top: 270px;*/
  /*left: 35px;*/
  box-shadow:
    0 0 20px #111 inset,
    0 0 10px lime;
  animation: 1s green infinite;
}

@keyframes red{
  /*0%{opacity: 1}*/
  /*25%{opacity: 1}
  50%{opacity: 1}
  75%{opacity: .1}*/
  /*100%{opacity: .1}*/
}

@keyframes yellow{
  /*0%{opacity: .1}
  20%{opacity: .1}
  40%{opacity: 1}
  50%{opacity: .1}
  60%{opacity: .1}
  80%{opacity: .1}
  100%{opacity: .1}*/
}

@keyframes green{
  /*0%{opacity: .1}*/
  /*20%{opacity: .1}
  40%{opacity: 1}
  50%{opacity: .1}
  60%{opacity: .1}
  80%{opacity: .1}*/
  /*100%{opacity: .1}*/
}
////////////////////////////////////////
body {
  /* Margin bottom by footer height */
  /*margin-bottom: 60px;*/
}
.footer {
  position: absolute;
  float: right;
  bottom: 0;
  width: 70%;
  /* Set the fixed height of the footer here */
  height: 40px;
  background-color: #f5f5f5;
}


/* Custom page CSS
-------------------------------------------------- */
/* Not required for template or sticky footer method. */

/*.container {
  width: auto;
  max-width: 680px;
  padding: 0 15px;
}
.container .text-muted {
  margin: 20px 0;
}*/

</style>

<div id="panel" class="collapsed">

<div class = "panel panel-default">
<div class = "panel-heading">
  <h3 class = "panel-title">
     <b>Model Features</b>
  </h3>
</div>
<div class = "panel-body panel-body1">

  <!--<a id="expandButton" href="#">
    <span></span>
    <span></span>
    <span></span>
  </a>
  div class="filterBlock" >
    <input type="text" id="filterInput" placeholder="Type to filter"/>
    <a href="#" id="clearFilterButton" >x</a><div id="content">   </div>

  </div-->

<div class = "panel panel-default">
<div class = "panel-body">
  <div>
    <!-- <div class="col-xscol-sm-3 sidebar-offcanvas" id="sidebar"> -->
      <div class="list-group">
        <!-- <a href="#" class="list-group-item">Model Files</a> -->
        <?php foreach (IgesFile::getProjectFile($pid) as $row) { ?>
        <a id="file" href="<?php echo "myprojectview.php?fid=".$row['fileid']."&pid=".$pid; ?>" class="list-group-item"><?php echo $row['filename']; ?></a>
        <?php }  ?>
      </div>
  </div>
</div>
</div>

<div class = "panel panel-default">
<div class = "panel-heading"><b>Bend Features</b></div>
<table class = "table table-hover">
 <thead>
<tr>
  <th>Bend ID</th>
  <th>Radius</th>
  <th>Thickness</th>
  <th>Length</th>
  <th>Angle</th>
</tr>
</thead>
<?php
// $query = "SELECT * FROM bends  where file_id = '$fileID'";
// $i = 1;
// var_dump($result);
if (isset($result) && is_array($result))
foreach ($result as $row){
// $iges_file = $row['file_id'];
// if (($i % 2) == 0)
{
?>

  <tr>
     <td><?php echo $row['bend_id']; //2;?></td>
     <td><?php echo $row['bend_radius'];?></td>
     <td><?php echo $row['bend_thickness'];?></td>
     <td><?php echo $row['bend_length'];?></td>
     <td><?php echo $row['angle']."&deg";?></td>
  </tr>
<?php
}
// ++$i;
  }
?>
</table>

</div>
<div class = "panel panel-default" style="margin-bottom:30px">
<div class = "panel-heading"><b>Face Relationships</b></div>

<table class = "table table-hover">
 <thead>
<tr>
  <th>Face 1</th>
  <th>Bend</th>
  <th>Face 2</th>
</tr>
</thead>
<?php
$i = 1;
if (isset($result) && is_array($result))
foreach ($result as $row){
// if (($i % 2) == 0)
{
?>
  <tr>
     <td><?php echo $row['face1_id'];?></td>
     <td><?php echo $row['bend_id'];//2;?></td>
     <td><?php echo $row['face2_id'];?></td>
  </tr>
<?php
}
++$i;
}
?>
<span></span>
</table>

</div>


</div>
</div>
</div>


<iframe id="viewer" name="viewer" allowfullscreen onmousewheel="" src=<?php echo "A_draw1.php?id=".$fileID;?>></iframe>
<div class="container">

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
 <div class="modal-dialog modal-lg">
   <div class="modal-content">
     <div class="modal-header">
       <button type="button" class="close"
data-dismiss="modal">&times;</button>
       <h3 class="modal-title">Bend Features</h3>
     </div>
     <div class="modal-body">
       <p>
          <table class = "table table-hover">
  <thead>
 <tr>
   <th>Bend ID</th>
   <th>Radius<?php echo "(".$row['bend_unit'].")"; ?></th>
   <th>Thickness<?php echo "(".$row['bend_unit'].")"; ?></th>
   <th>Length<?php //echo "(".$unit.")"; ?></th>
   <th>Angle(Degrees)</th>
   <th>Height<?php echo "(".$row['bend_unit'].")"; ?></th>
   <th>Bending Force(N)</th>
 </tr>
</thead>
<?php
if (isset($result) && is_array($result))
foreach ($result as $row){
?>

   <tr>
      <td><?php echo $row['bend_id'];///2;?></td>
      <td><?php echo $row['bend_radius'];?></td>
      <td><?php echo $row['bend_thickness'];?></td>
      <td><?php echo $row['bend_length'];?></td>
      <td><?php echo $row['angle']."&deg";?></td>
      <td><?php echo $row['bend_height'];?></td>
      <td><?php echo $row['bending_force'];?></td>
   </tr>
<?php
}
// ++$i;
// }
?>
</table></p>
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-default btn-lg"
data-dismiss="modal">Close</button>
     </div>
   </div>
 </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myMachines" role="dialog">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header">
       <button type="button" class="close"
data-dismiss="modal">&times;</button>
       <h3 class="modal-title">Machine Status</h3>
     </div>
     <div class="modal-body">
       <p>
          <table class = "table table-hover" style="width=20%">
  <thead>
 <tr>
   <th>Name</th>
   <!-- <th>Details</th> -->
   <th>Status</th>
 </tr>
</thead>
<?php
if (isset($result) && is_array($result))
foreach ($result as $row){
  $rads = array (0, 1, 2);
  $key = array_rand ( $rads, 1 );
?>

   <tr>
      <td><?php echo $row['bend_id'];///2;?></td>
      <!-- <td><?php echo $row['bend_radius'];?></td> -->
      <td><div class="trafficlight">
        <!-- <div class="protector"></div> -->
        <!-- <div class="protector"></div> -->
        <!-- <div class="protector"></div> -->
        <?php if ($key == 1) {
        echo "<div class=\"green\"></div>";
      } else if ($key == 2){
        echo "<div class=\"red\"></div>";
      }else {
        echo "<div class=\"yellow\"></div>";
      }?>
        <!-- <div class="yellow"></div> -->
        <!-- <div class="green"> -->

        </div>
      <!-- </div><div id="light">
          <?php
            // $active = "active";
            // $colour = $key ? "green" : "red";
            // echo "<span class=$active id=$colour></span>";  ?>
        </div> -->

<!-- <button>Switch Light</button></td> -->
<?php
}
// ++$i;
// }
?>
</table></p>
     </div>
     <!-- <div class="modal-footer">
       <button type="button" class="btn btn-default btn-lg"
data-dismiss="modal">Close</button>
     </div> -->
   </div>
 </div>
</div>

<!-- <div class="trafficlight"> -->
  <!-- <div class="protector"></div> -->
  <!-- <div class="protector"></div> -->
  <!-- <div class="protector"></div> -->
  <!-- <div class="red"></div> -->
  <!-- <div class="yellow"></div> -->
  <!-- <div class="green"></div> -->
<!-- </div> -->
<?php
require('templates/footer.php');
?>
