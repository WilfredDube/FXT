<?php
require_once('../includes/initialize.php');

//if not logged in redirect to login page
if(!$user->is_logged_in()){ header('Location: login.php'); }

//define page title
$title = 'Members Page';

//include header template
require('templates/header.php');
?>
<!-- <div class="navbar navbar-default navbar-blend-top" role="navigation">

      <div class="navbar-header">

        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">

          <span class="sr-only">Menu</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>

        </button>

        <span class="logo navbar-brand">

          <a id="logo" href="/">

            <span class="logo-img"></span>
            <span class="logo-label">
              Dagobert Renouf<br>
              <strong>Webdesigner&nbsp;Freelance</strong>
            </span>

          </a>

        </span>

      </div>

      <nav class="navbar-collapse collapse">

        <ul class="nav navbar-nav navbar-right">

          <li><a href="/a-propos">&Agrave; Propos</a></li>
          <li><a href="/portfolio-webdesigner">Portfolio</a></li>
          <li><a href="/competences">Comp&eacute;tences</a></li>
          <li><a href="/contact">Contact</a></li>
          <li class="lang"><a href="/en"><i class="icon-fontawesome-webfont-1"></i> <span class="short-label">En</span><span class="long-label">English version</span></a></li>

        </ul>

      </nav>/.nav-collapse

    </div>
		<header class="home-panel panel-default" id="panel-main">

	<div class="container panel-head">

		<h1>Design &amp;&nbsp;code</h1>

		<p class="lead">Je suis webdesigner et int&eacute;grateur freelance.</p>

		<a href="#panel-portfolio" class="scroll-for-more btn btn-dgbrt">
			Scrollez pour continuer
			<i class="icon-fa-chevron-down"></i>
		</a>

	</div>

</header> -->
<!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">FXtract</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li role="separator" class="divider"></li>
                <li class="dropdown-header">Nav header</li>
                <li><a href="#">Separated link</a></li>
                <li><a href="#">One more separated link</a></li>
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
			<header class="container">
			<h1>My Projects</h1>
			<p><a href='logout.php'>Logout</a></p>
 </header>
    </nav>

		<!-- <article> -->

<!-- <div class="jumbotron">

 <header class="container">

	 <h1>Comp&eacute;tences</h1>

	 <p>Men&eacute; par l'envie constante de cr&eacute;er des sites toujours plus impactants et vivants, j'ai développé mes compétences d'intégrateur web en parall&egrave;le de celles de webdesigner et visionnaire.</p>

 </header>

</div> -->
<div class="container">
<div class="row">
<ul class="nav nav-tabs">
 <li role="presentation" class="active"><a href="#">Active Projects</a></li>
 <li role="presentation"><a href="#">Archived</a></li>
 <!-- <li role="presentation"><a href="#">Messages</a></li> -->
</ul>
</div>

<div class="container1">
  <img src="style/1.jpg" alt="Avatar" class="image" style="width:100%">
  <div class="middle">
    <div class="text">John Doe</div>
  </div>
</div>
</div>

<?php
//include header template
require('templates/footer.php');
?>
