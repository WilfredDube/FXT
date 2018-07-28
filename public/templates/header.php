<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php if(isset($title)){ echo $title; }?></title>
    <link href="style/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="../style/admin/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./style/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="style/main.css">
    <link rel="stylesheet" href="style/hero.css">
    <link rel="stylesheet" href="style/overlay.css">
    <?php if(!$user->is_logged_in()){
    echo "<link rel=\"stylesheet\" href=\"style/index.css\">";
    } ?>
    <script src="style/bootstrap/js/jquery.js"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script type="text/javascript">
            function load_modal(){
                    $('#myModal').modal('show');
            }

            function load_modal2(){
                    $('#myMachines').modal('show');
            }
    </script>
    <style>

    .modal {
            position: absolute;
            top: 3%;
            left:15%;
            margin-left:-150px;
            mbargin-tbop:-150px;
    }
    </style>
    <style>
      #myInput {
        background-image: url('/css/searchicon.png');
        /* Add a search icon to input */
        background-position: 10px 12px;
        /* Position the search icon */
        background-repeat: no-repeat;
        /* Do not repeat the icon image */
        width: 100%;
        /* Full-width */
        font-size: 16px;
        /* Increase font-size */
        padding: 12px 20px 12px 40px;
        /* Add some padding */
        border: 1px solid #ddd;
        /* Add a grey border */
        margin-bottom: 12px;
        /* Add some space below the input */
      }

      #myTable {
        border-collapse: collapse;
        /* Collapse borders */
        width: 100%;
        /* Full-width */
        border: 1px solid #ddd;
        /* Add a grey border */
        font-size: 18px;
        /* Increase font-size */
      }

      #myTable th,
      #myTable td {
        text-align: left;
        /* Left-align text */
        padding: 12px;
        /* Add padding */
      }

      #myTable tr {
        /* Add a bottom border to all table rows */
        border-bottom: 1px solid #ddd;
      }

      #myTable tr.header,
      #myTable tr:hover {
        /* Add a grey background color to the table header and on hover */
        background-color: #f1f1f1;
      }
    </style>
    <script>
      function myFunction() {
        // Declare variables
        var input, filter, table, tr, td, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");

        // Loop through all table rows, and hide those who don't match the search query
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[0];
          td1 = tr[i].getElementsByTagName("td")[1];
          if (td || td1) {
            if ((td.innerHTML.toUpperCase().indexOf(filter) > -1) || (td1.innerHTML.toUpperCase().indexOf(filter) > -1)) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }
        }
      }
    </script>
    <style>
      body {
        margin: 0;
      }

      .icon-bar {
        width: 100%;
        background-color: #555;
        overflow: auto;
      }

      .icon-bar a {
        float: left;
        width: 20%;
        text-align: center;
        padding: 12px 0;
        transition: all 0.3s ease;
        color: white;
        font-size: 36px;
      }

      .icon-bar a:hover {
        background-color: #000;
      }

      .active {
        background-color: #4CAF50 !important;
      }
    </style>
<body>
  <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="..">FXtract</a>
      </div>
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="nav navbar-nav">
<?php
if (!$user->is_logged_in()) {
?>
          <li class="active"><a href="#">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#contact">Contact</a></li>
<?php
}else {
?>
           <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Projects <span class="caret"></span></a>
            <ul class="dropdown-menu">
            <?php if (isset($_POST['cool'])) {
              $res = Project::find_all();

              foreach ($res as $value) {
              ?>
  <!-- <li><a id="myBtn" href="">New Project</a></li> -->
  <!-- <li role="separator" class="divider"></li> -->
  <!-- <li><a href="modelupload.php">New Model</a></li> -->
  <!-- <li role="separator" class="divider"></li> -->
  <li><a href='<?php echo "myprojectdetails.php?pid=".$value['projectid']."&mid=".$value['projectmaterialid']; ?>'><?php echo $value['projectname']; ?></a></li>
  <li role="separator" class="divider"></li>
  <?php        # code...
      }}?>
      <li><a href="myprojects.php">All Projects</a></li>

              <!--li><a href="#">View Files</a></li>
              <li><a href="#">Features</a></li>

              <li role="separator" class="divider"></li>
              <li class="dropdown-header">Nav header</li>
              <li><a href="#">Separated link</a></li>
              <li><a href="#">One more separated link</a></li-->


          </ul> </li>
            <?php if (isset($_POST['process'])) {?>
                         <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Features<span class="caret"></span></a>
            <ul class="dropdown-menu">
  <li><a href="#">Extract Features</a></li>
              <li role="separator" class="divider"></li>
                          <!--li><a href="uploadhistory.php">Extract Fields</a></li>
              <li role="separator" class="divider"></li>
                          <li><a href="#">View Brep</a></li-->
                          <li>  <a href="#" onclick="load_modal()">Full Model Features</a></li>
              <li role="separator" class="divider"></li>
              <!-- <li>  <a href="#" onclick="load_modal2()">Tool Selection</a></li> -->
              <li role="separator" class="divider"></li>
                          <li>  <a href="#" onclick="load_modal2()">Face Adjacency Graph</a></li>
              <!--li role="separator" class="divider"></li>
              <li class="dropdown-header">Nav header</li>
              <li><a href="#">Separated link</a></li>
              <li><a href="#">One more separated link</a></li-->

            </ul>
<?php
}?>
<?php if (isset($_POST['process'])) {?>
             <li class="dropdown">
<a href="#" onclick="load_modal2()">Machines</a>
</li>
<?php
}?>

          </ul> </li>

  <ul class="nav navbar-nav navbar-right">
    <li class="dropdown">
<a marked="1" href="#" class="dropdown-toggle user-menu" data-toggle="dropdown">
<!--img src="Main%20Menu%20-%20Layouts%20-%20PixelAdmin_files/1.jpg"-->
<span class="glyphicon glyphicon-user"><?php echo " ".$_SESSION['user']['username'];?></span>
</a>
<ul class="dropdown-menu">
<li><a marked="1" href="#"><span class="label label-warning pull-right">New</span>Profile</a></li>
<li><a marked="1" href="#"><span class="badge badge-primary pull-right">New</span>Account</a></li>
<li><a marked="1" href="#"><i class="dropdown-icon fa fa-cog"></i>&nbsp;&nbsp;Settings</a></li>
<li class="divider"></li>
<li><a marked="1" href="logout.php"></i>&nbsp;&nbsp;Log Out</a></li>
</ul>
</li>
  </ul>
<?php
}?>

      </div><!--/.nav-collapse -->
    </div>
  </nav>
