<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('file_uploads','on');

// Define the core paths
// Define them as absolute paths to make sure that require_once works as expected

// DIRECTORY_SEPARATOR is a PHP pre-defined constant
// (\ for Windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

defined('SITE_ROOT') ? null :
	define('SITE_ROOT', realpath(__DIR__ . '/..'));

defined('INCLUDE_PATH') ? null :define('FILEREPOSITORY', SITE_ROOT.DS.'uploads');
defined('INCLUDE_PATH') ? null : define('INCLUDE_PATH', SITE_ROOT.DS.'includes');
defined('IGES_LIB_PATH') ? null : define('IGES_LIB_PATH', SITE_ROOT.DS.'libs/iges/');
defined('TOOL_LIB_PATH') ? null : define('TOOL_LIB_PATH', SITE_ROOT.DS.'libs/tool-lib');
defined('CLASS_PATH') ? null : define('CLASS_PATH', SITE_ROOT.DS.'classes/');
defined('FILE_REPOSITORY') ? null : define('FILE_REPOSITORY', SITE_ROOT.DS.'uploads/');

// load config file first
require_once(INCLUDE_PATH.DS.'config.php');

// load database
require_once(CLASS_PATH.DS.'database.php');
require_once(CLASS_PATH.DS.'user.php');
// require_once(CLASS_PATH.DS.'upload.php');
require_once(CLASS_PATH.DS.'project.php');
// require __DIR__ .'/vendor/autoload.php';
// load basic functions next so that everything after can use them
//require_once(LIB_PATH.DS.'functions.php');

// load core objects
require_once(IGES_LIB_PATH.DS.'iges-init.php');
require_once(TOOL_LIB_PATH.DS.'tool-library.php');
// require_once(LIB_PATH.DS.'session.php');
// require_once(LIB_PATH.DS.'database.php');
// require_once(LIB_PATH.DS.'database_object.php');
// require_once(LIB_PATH.DS.'pagination.php');
// require_once(LIB_PATH.DS."phpMailer".DS."class.phpmailer.php");
// require_once(LIB_PATH.DS."phpMailer".DS."class.smtp.php");
// require_once(LIB_PATH.DS."phpMailer".DS."language".DS."phpmailer.lang-en.php");
//
// // load database-related classes
// require_once(LIB_PATH.DS.'user.php');
// require_once(LIB_PATH.DS.'photograph.php');
// require_once(LIB_PATH.DS.'comment.php');
?>
