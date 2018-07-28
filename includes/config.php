<?php
ob_start ();
//session_start ();

// set timezone
date_default_timezone_set ( 'Africa/Harare' );

// database credentials
define ( 'DBHOST', 'localhost' );
define ( 'DBUSER', 'root' );
define ( 'DBPASS', 'password' );
define ( 'DBNAME', 'FXtest' );

// application address
define ( 'DIR', 'http://localhost/' );
define ( 'SITEEMAIL', 'noreply@localhost.com' );

?>
