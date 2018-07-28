<?php
require_once('../includes/initialize.php');

//logout
$user->logout();

//logged in return to index page
header('Location: ..');
exit;
?>
