<?php
require_once('../includes/initialize.php');

//if logged in redirect to members page
if ($user->is_logged_in()) {
    header('Location: memberpage.php');
}

//if form has been submitted process it
if (isset($_POST['submit'])) {

    //very basic validation
    if (strlen($_POST['username']) < 3) {
        $error[] = 'Username is too short.';
    } else {
        try {
            $params = array(':username' => $_POST['username']);
            $row = User::find_user_by_sql('SELECT username FROM members WHERE username = :username', $params);
            // $stmt->execute(array(':username' => $_POST['username']));
            // $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $row = array_shift($row);
            //print_r(($row));
        } catch (PDOException $e) {
        }

        if (!empty($row->username)) {
            $error[] = 'Username provided is already in use.';
        }
    }

    if (strlen($_POST['password']) < 3) {
        $error[] = 'Password is too short.';
    }

    if (strlen($_POST['passwordConfirm']) < 3) {
        $error[] = 'Confirm password is too short.';
    }

    if ($_POST['password'] != $_POST['passwordConfirm']) {
        $error[] = 'Passwords do not match.';
    }

    //email validation
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error[] = 'Please enter a valid email address';
    } else {
        $params = array(':email' => $_POST['email']);
        $row = User::find_user_by_sql('SELECT email FROM members WHERE email = :email', $params);

        $row = array_shift($row);
        //print_r(($row));

        if (!empty($row->email)) {
            $error[] = 'Email provided is already in use.';
        }
    }


    //if no errors have been created carry on
    if (!isset($error)) {
        $us = new User();
        $us->firstname = $_POST['firstname'];
        $us->lastname = $_POST['lastname'];
        $us->username = $_POST['username'];
        $us->password = $_POST['password'];
        $us->email = $_POST['email'];

        $us->save();
    // $us->memberID = $db->lastInsertId('memberID');
    }
}

//define page title
$title = 'Demo';

//include header template
require('templates/header.php');
?>


<div class="container">

	<div class="row">

	    <div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
			<form role="form" method="post" action="" autocomplete="off">
				<h2>Please Sign Up</h2>
				<p>Already a member? <a href='login.php'>Login</a></p>
				<hr>

				<?php
                //check for any errors
                if (isset($error)) {
                    foreach ($error as $error) {
                        echo '<p class="bg-danger">'.$error.'</p>';
                    }
                }

                //if action is joined show sucess
                if (isset($_GET['action']) && $_GET['action'] == 'joined') {
                    echo "<h2 class='bg-success'>Registration successful, please check your email to activate your account.</h2>";
                }
                ?>

				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="form-group">
							<input type="text" name="firstname" id="firstname" class="form__label form-control input-lg" placeholder="First Name" value="<?php if (isset($error)) {
                    echo $_POST['firstname'];
                } ?>" tabindex="1">
						</div>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="form-group">
							<input type="text" name="lastname" id="lastname" class="form__label form-control input-lg" placeholder="Last Name" value="<?php if (isset($error)) {
                    echo $_POST['lastname'];
                } ?>" tabindex="2">
						</div>
					</div>
				</div>
				<div class="form-group">
					<input type="text" name="username" id="username" class="form__label form-control input-lg" placeholder="User Name" value="<?php if (isset($error)) {
                    echo $_POST['username'];
                } ?>" tabindex="3">
				</div>
				<div class="form-group">
					<input type="email" name="email" id="email" class="form__label form-control input-lg" placeholder="Email Address" value="<?php if (isset($error)) {
                    echo $_POST['email'];
                } ?>" tabindex="4">
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="form-group">
							<input type="password" name="password" id="password" class="form__label form-control input-lg" placeholder="Password" tabindex="5">
						</div>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="form-group">
							<input type="password" name="passwordConfirm" id="passwordConfirm" class="form__label form-control input-lg" placeholder="Confirm Password" tabindex="6">
						</div>
					</div>
				</div>

				<div class="row">

  <!-- <div class="col-xs-6 col-md-4"></div> -->
  <div class="col-md-6 col-md-offset-3"><input class="button" type="submit" name="submit" value="Register" class="btn btn-primary btn-block btn-lg" tabindex="7"></div>
  <!-- <div class="col-xs-6 col-md-4"></div> -->

				</div>
			</form>
		</div>
	</div>

</div>

<?php
//include header template
require('templates/footer.php');
?>
