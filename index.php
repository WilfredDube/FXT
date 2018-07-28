<?php
require_once('includes/initialize.php');

//if logged in redirect to members page
if( $user->is_logged_in() ){ header('Location: public/myprojects.php'); }

//define page title
$title = 'FXtract';

//include header templated
require('public/templates/indexheader.php');
?>

<div class="hero-image">

 <div class="hero-text">
	 <h1 class="hero-title">Design. Plan. Collaborate. Manufacture.</h1>
	 <h2 class="hero-subtitle">The world's leading computer aided process planner</h2>
	 <button class="button" onclick="window.location.href='public/register.php'"><span>Get Started </span></button>
	 <!-- <span><input class="button type="button" value="Get Started" onclick="window.location.href='public/register.php'" /></span> -->

 </div>
</div>

<?php
// include header template
require('public/templates/footer.php');
?>
