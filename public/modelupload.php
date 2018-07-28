<?php
require_once('../includes/initialize.php');
//ini_set('upload_temp_dir','/home/digitalincentives/www/loginregister/uploads/');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: ..'); }

if(isset($_POST['upload'])){

    if (is_uploaded_file($_FILES['classnotes']['tmp_name'])) {

	$file_name = $_POST['name'];
	$tmp_name = $_FILES['classnotes']['tmp_name'];
	$file_type = $_FILES['classnotes']['type'];
	$file_caption = $_POST['caption'];
	$material = $_POST['sel1'];

	//echo $upload->getMaterialID($material)."dsdsd";

	$mid = $upload->getMaterialID($material);

	//echo $_POST['caption'];
	//echo "sasasa".$upload->validate_file($file_name, $tmp_name, $file_type, $file_caption);
	if($upload->validate_file($file_name, $tmp_name, $file_type, $file_caption, $mid) == 1){
		header('Location: myprojects.php');
		exit;

	} else {
		$error[] = 'Uploading failed.';
	}

    } else {
	$error[] = 'Select file to upload.';
    }
}

//define page title
$title = 'Members Page';

//include header template
require('templates/header.php');

?>

<div class="container">

<div class="row">

<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
<form action="" enctype="multipart/form-data" method="post">
<h2>Upload Model File</h2>
<?php ?>
<!-- <p>Version 5.3</a></p> -->
<hr>
				<?php
				//check for any errors
				if(isset($error)){
					foreach($error as $error){
						echo '<p class="bg-danger">'.$error.'</p>';
					}
				}?>

<div class="form-group">
  <label for="sel1">Select Project</label>
  <select class="form-control input-lg" id="sel1" name="sel1" required>
  <option hidden></option>

  </select>
</div>
<div class="form-group">
<input type="text" name="name" id="name" class="form-control input-lg" placeholder="File Name" value="" tabindex="1" required>
</div>
<div class="form-group">
<textarea rows="4" class="form-control input-lg" placeholder="Project Description"></textarea>
</div>
<div class="form-group">
<label for="sel1">Select Material</label>
<select class="form-control input-lg" id="sel1" name="sel1" multiple title="Select Material" required>
<option hidden></option>

</select>
</div>
<div class="form-group">
<input type="file" name="classnotes" id="classnotes" value="" class="btn btn-primary btn-block btn-lg" placeholder="IGES File" tabindex="1" required/>
</div>
<div class="row">
<div class="col-xs-6 col-md-6 col-md-offset-3"><input type="submit" name="upload" id="upload" value="Upload" class="btn btn-primary btn-block btn-lg" tabindex="5"/></div>
<!-- ODO: FIX UPLOAD PAGE -->
</div>
<hr>
</form>
</div>

<?php
//include header template
// get the contents, and echo it out.
require('templates/footer.php');
?>
