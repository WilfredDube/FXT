<?php
require_once('../includes/initialize.php');

// ini_set('display_errors','on');
//ini_set('file_uploads','on');
//ini_set('upload_temp_dir','/home/digitalincentives/www/loginregister/uploads/');

//if not logged in redirect to login page
if (!$user->is_logged_in()) {
    header('Location: login.php');
}

$_POST['cool'] = 1;
$id = trim($_GET['pid']);
$mid = trim($_GET['mid']);

if (getProject($id) == false)
  header('Location: myprojects.php');

// echo $id;
if (Project::getModelFileID($id)) {
  $bl = $project->setProjectFileID(Project::getModelFileID($id), $id);
  // echo $bl;
  // $project->save();
}

// echo FILEREPOSITORY."/".$_SESSION['user']['username'];

if(isset($_POST['upload'])) {
  // print_r($_FILES);

  $_FILES['upload_file']['filecaption'] = $_POST['filecaption'];
  $_FILES['upload_file']['fileprojectid'] = $id;
  $_FILES['upload_file']['filematerialid'] = $mid;
  $_FILES['upload_file']['name'] = $_POST['filename'].".igs";

  if ($igesfile->attach_file($_FILES['upload_file'])) {
    $igesfile->save();
    header('Location: myprojectdetails.php?pid='.$id.'&mid='.$mid);
  } else {
	   $error[] = 'Select file to upload.';
  }
}

// $id = trim($_GET['id']);

function getProject($id)
{
    global $database;
    $query = "SELECT * FROM projects WHERE projectid=? LIMIT 1";
    $res = $database->getAllRows($query, [$id]);

    return !empty($res) ? $res : false;
}

$rett = getProject($id);
$row = is_array($rett) ? array_shift($rett) : $rett;
// print_r($row);

//define page title
$title = 'My Projects';

//include header template
require('templates/header.php');

?>
<style media="screen">
/* layout.css Style */
.upload-drop-zone {
  height: 100px;
  border-width: 2px;
  margin-bottom: 20px;
}

/* skin.css Style*/
.upload-drop-zone {
  color: #ccc;
  border-style: dashed;
  border-color: #ccc;
  line-height: 200px;
  text-align: center
}
.upload-drop-zone.drop {
  color: #222;
  border-color: #222;
}
.btn-circle {
  width: 30px;
  height: 30px;
  text-align: center;
  padding: 6px 0;
  font-size: 12px;
  line-height: 1.428571429;
  border-radius: 15px;
}
.btn-circle.btn-lg {
  width: 50px;
  height: 50px;
  padding: 10px 16px;
  font-size: 18px;
  line-height: 1.33;
  border-radius: 25px;
}
.btn-circle.btn-xl {
  width: 70px;
  height: 70px;
  padding: 10px 16px;
  font-size: 24px;
  line-height: 1.33;
  border-radius: 35px;
}

html, body {height:100%; margin:0; overflow:hidden;}
header, footer {display:block; background-color:black; height:15%;}
section {height:88%; ; display:block; overflow:auto;}
section .push {height:4000px;}
.modal-header, h4, .close {
      background-color: #202020;
      color:white !important;
      text-align: center;
      font-size: 30px;
  }
  .modal-footer {
      background-color: #f9f9f9;
  }
</style>
<section>

<div class="container">
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="padding:35px 50px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4><span></span>Upload Model File</h4>
        </div>

        <div class="modal-body" style="padding:40px 50px;">
          <form action="" method="post" enctype="multipart/form-data">
            <?php
            //check for any errors
            if(isset($error)){
              foreach($error as $error){
                echo '<p class="bg-danger">'.$error.'</p>';
              }
            }?>
            <div class="form-group">
              <input type="text" class="form-control input-lg" name="filename" placeholder="File Name" required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control input-lg" name="filecaption" placeholder="File Caption" required>
            </div>
            <!-- <div class="form-inline"> -->
              <div class="form-group">
                <!-- <div class="upload-drop-zone" id="drop-zone"> -->
                  <input type="file" class="btn btn-success btn-block btn-lg"  name="upload_file" id="upload_file" multiple required>
                <!-- </div> -->
              </div>
            <!-- </div> -->
            <!-- <div class="form-inline"> -->
              <div class="form-group">
              <!-- <button type="submit" class="btn btn-sm btn-primary" id="js-upload-submit">Upload files</button> -->
              <button type="submit" class="btn btn-danger btn-block btn-lg" name="upload">Upload</button>
            </div>
            <!-- </div> -->
          </form>
        </div>
        <!-- <div class="modal-footer">
          <button type="submit" class="btn btn-danger btn-default pull-left" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
          <p>Not a member? <a href="#">Sign Up</a></p>
          <p>Forgot <a href="#">Password?</a></p>
        </div> -->
      </div>
      </div>
      </div>
<div id="content-wrapper">

		<div class="row">
			<div class="col-sm-12">
<!--<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
 6. $BORDERED_TABLES ===========================================================================

				Bordered tables
-->

  <!-- <h2 class="sub-header">Users</h2> -->
  <div class="table-responsive">
    <div class="col-sm-3">
      <div>
        <h2>Project Details</h2>
      </div>
    </div>
    <div class="col-sm-3"></div>
    <div class="col-sm-3"></div>
    <div class="col-sm-3">
      <div class="col-sm-2"></div>
      <div class="col-sm-1">
        <?php //if (Project::getProjectFileID($id) == 0) { ?>
      <button type="button" class="btn btn-danger btn-circle btn-lg" id="myBtn"><i class="fa fa-plus"></i></button>
      <?php //} else { ?>
        <!-- <button type="button" class="btn btn-primary btn-circle btn-lg" id="myprocess"><i class="fa fa-play"></i></button> -->
      <?php //} ?>
      </div>
    </div>
   </div>
   <hr/>
      <!-- <h1 class="page-header"></h1> -->

      <!-- <h2 class="sub-header">Users</h2> -->
      <div class="table-responsive col-sm-9">

        <!-- <input type="text" id="myInput" onkeyup="my Function()" placeholder="Search for names.."> -->

        <table class="table table-striped" id="myTable">
          <tr class="header">
            <th style="width:30%;"></th>
            <th style="width:70%;"></th>
          </tr>
          <tr>
            <td>Project Name</td>
            <?php echo"<td>".($row['projectname'])."</td>";?>
          </tr>
          <tr>
            <td>Project Owner</td>
            <?php echo"<td><a href=\"#\">".(User::find_user_by_id($row['projectownerid'])->username)."</a></td>";?>
          </tr>
          <tr>
            <td>Complete</td>
            <?php echo"<td>".(($row['projectcomplete']) ? "Yes":"No")."</td>";?>
          </tr>
          <tr>
            <td>Description</td>
            <?php echo"<td>".($row['projectdescription'])."</td>";?>
          </tr>
          <tr>
            <td>Created</td>
            <?php echo"<td>".($row['projectcreationdate'])."</td>";?>
          </tr>
          <tr>
            <td>Tools Selected</td>
            <td>A,B,C,D</td>
          </tr>
          <tr>
            <td>Shared</td>
            <td>No</td>
          </tr>
          <tr>
            <td>Collaborations</td>
            <td>0</td>
          </tr>
          <?php if (($row['projectcomplete']) == 1) {
    ?>
          <tr>
            <td>Bend Sequence</td>
            <td><a href="#">Bend_Sequence_1009.pdf</a></td>
          </tr>
          <?php
} ?>
        </table>
        <div class="">
          <!-- <input class="button btn btn-lg btn-primary" onclick="window.location.href='useredit.html'" value="Edit User" class="btn btn-primary btn-block btn-lg" tabindex="7"> -->
          <!-- <input class="button btn btn-lg btn-primary" onclick="window.location.href='projects.html'" value="Cancel" class="btn btn-primary btn-block btn-lg" tabindex="8"> -->
        </div>
      </div>
      <div class="col-sm-3">
        <!-- <div class="col-xscol-sm-3 sidebar-offcanvas" id="sidebar"> -->
          <div class="list-group">
            <a href="#" class="list-group-item active">Model Files</a>
            <?php
            $qres = IgesFile::getProjectFile($id);
            foreach ($qres as $row) { ?>
            <a href="#" class="list-group-item"><?php echo $row['filename']; ?></a>
            <?php }  ?>
          </div>
        <!-- </div><!--/.sidebar-offcanvas-->
      </div><!--/row-->

    </div>
    </div>
    </div>
<!-- /6. $BORDERED_TABLES -->

	</div> <!-- / #content-wrapper -->
	<div id="main-menu-bg"></div>
</div> <!-- / #main-wrapper -->
</div>
</div>
</div>
</section>
<?php
//include header template
// get the contents, and echo it out.
require('templates/footer.php');
?>

<footer>
<div class="icon-bar">
<a href="myprojects.php"><i class="fa fa-arrow-left"></i></a>
<?php //var_dump($qres);
if (!empty($qres)) {?>
  <a href="<?php echo "myprojectview.php?fid=".Project::getProjectFileID($id)."&pid=".$id; ?>"><i class="fa fa-eye"></i></a>
<?php } else { ?>
  <a href="#"><i class="fa fa-eye"></i></a>
<?php }  ?>
<a class="active" href="#"><i class="fa fa-bars"></i></a>
<!-- <a href="myprojects.php"><i class="fa fa-share"></i></a> -->
<!-- <a href="#"><i class="fa fa-envelope"></i></a> -->
<!-- <a href="#"><i class="fa fa-globe"></i></a> -->
<a href="#"><i class="fa fa-edit"></i></a>
<a href="#"><i></i></a>
</div>
</footer>
<script type="text/javascript">
$(document).ready(function(){
    $("#myBtn").click(function(){
        $("#myModal").modal();
    });
});
</script>
