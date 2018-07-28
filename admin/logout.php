<?php
require_once('../includes/initialize.php');

//logout
$user->adminLogout();

//logged in return to index page
header('Location: .');
exit;
?>
