<?php
require_once('../includes/initialize.php');

// ini_set('display_errors','on');
//ini_set('file_uploads','on');
//ini_set('upload_temp_dir','/home/digitalincentives/www/loginregister/uploads/');

//if not logged in redirect to login page
if (!$user->is_logged_in()) {
    header('Location: login.php');
}

// echo $_SESSION['user']['username'];
$_POST['cool'] = 1;

if(isset($_POST['createproject'])) {
  if (isset($_POST['projectname']) && isset($_POST['selectmaterial']) && isset($_POST['descrproject'])) {
    // print_r(Project::getMaterialID($_POST['selectmaterial'])['m_id']);

    $project->projectname = $_POST['projectname'];
    $project->projectmaterialid = Project::getMaterialID($_POST['selectmaterial'])['m_id'];
    $project->projectdescription = $_POST['descrproject'];
    $project->projectownerid = User::getUserID($_SESSION['user']['username']);

    $project->save();
  }
} else {
  $_SESSION['nnnnn'] = "failed";
}

if (isset($_GET['delete_id'])) {
    $pid = $_GET['delete_id'];

    Project::delete($pid);

    header("Location: myprojects.php");
}

function getProjects($username)
{
    global $database;
    $query = "SELECT * FROM projects WHERE projectownerid=?";
    $userID[] = User::getUserID($username);
    $res = $database->getAllRows($query, $userID);

    return !empty($res) ? $res : false;
}

$username = $_SESSION['user']['username'];
//define page title
$title = 'My Projects';

//include header template
require('templates/header.php');

?>
<style media="screen">
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
  <div class="container">
  <!-- Project Creation Modal  -->
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="padding:35px 50px;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4><span></span>Create New Project</h4>
        </div>
        <div class="modal-body" style="padding:40px 50px;">
          <form role="form" action=" " method="post">
            <div class="form-group">
              <input type="text" class="form-control input-lg" name="projectname" placeholder="Enter Project Name" required>
            </div>
            <div class="form-group">
            <select class="form-control selectpicker input-lg" name="selectmaterial" title="Select Material" required>
            <option hidden></option>
            <?php
            $materials = Project::getAllMaterials();
            foreach ($materials as $value) {
              foreach ($value as $material)
              echo "<option>".$material."</option>";
            } ?>
            </select>
            </div>
            <div class="form-group">
              <textarea rows="4" name="descrproject" class="form-control input-lg" placeholder="Project Description" required></textarea>
            </div>
              <button type="submit" name="createproject" class="btn btn-success btn-block btn-lg">Create Project</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal End -->

  <div class="row">
 <div class="col-sm-3">
   <div>
     <h2>My Projects</h2>
   </div>
 </div>
 <div class="col-sm-3"></div>
 <div class="col-sm-3"></div>
 <div class="col-sm-3">
   <div class="col-sm-2"></div>
   <div class="col-sm-1">
   <button type="button" class="btn btn-danger btn-circle btn-lg" id="myBtn"><i class="fa fa-plus"></i></button>
   </div>
 </div>
</div>
<hr/>
  <div class="row">
              <div class="col-xs-6 col-lg-4 col-lg-offset-1">

              </div><!--/.col-xs-6.col-lg-4-->
              <div class="col-xs-6 col-lg-4 col-lg-offset-1">

              </div><!--/.col-xs-6.col-lg-4-->
            </div><!--/row-->
<div id="content-wrapper">
  <div class="row col-sm-12 ">

<!-- / .page-header -->

		<div class="row">
			<div class="col-sm-12">
<!--<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
 6. $BORDERED_TABLES ===========================================================================

				Bordered tables
-->

  <!-- <h2 class="sub-header">Users</h2> -->
  <div class="table-responsive">

    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for Projects..">

    <table class="table table-striped" id="myTable">
      <tr class="header">
        <th style="width:20%;">Project Name</th>
        <th style="width:30%;">Description</th>
        <th style="width:15%;">Material</th>
        <th style="width:15%;">Creation Date</th>
        <th style="width:15%;">Actions</th>
      </tr>
      <?php
        $rows = getProjects($username);
        if ($rows) {
            foreach ($rows as $row) {
              echo "<tr>";
                echo"<td>".($row['projectname'])."</td>";
                echo"<td>".($row['projectdescription'])."</td>";
                echo"<td>".($row['projectmaterialid'])."</td>";
                echo"<td>".($row['projectcreationdate'])."</td>";
                echo "<td>
                  <div class=\"btn-group\" role=\"group\" aria-label=\"\">
                    <button type=\"button\" class=\"btn btn-default\" onclick=\"window.location.href='myprojectdetails.php?pid=".$row['projectid']."&mid=".$row['projectmaterialid']."'\"><span class=\"fa fa-eye\"></span></button>
                    <button type=\"button\" class=\"btn btn-default\" onclick=\"window.location.href='#'\"><span class=\"fa fa-edit\"></span></button>
                    <button type=\"button\" class=\"btn btn-default\" onclick=\"return ConfirmDelete()\"><span class=\"fa fa-trash\"></span></button>
                  </div>
                </td>";
                echo "</tr>";
            }
        }
      ?>
    </table>
  </div>
<!-- /6. $BORDERED_TABLES -->

	</div> <!-- / #content-wrapper -->
	<div id="main-menu-bg"></div>
</div> <!-- / #main-wrapper -->
</div>
</div>
<script>
$(document).ready(function(){
    $("#myBtn").click(function(){
        $("#myModal").modal();
    });
});

function ConfirmDelete() {
  var status = confirm("Are you sure you want to delete?");
if(status)
  window.location.href="?delete_id=\".$row['projectid']\" ?>";
}
</script>
<?php
//include header template
// get the contents, and echo it out.
require('templates/footer.php');
?>
