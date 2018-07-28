<?php

function __autoload($class_name) {
	$class_name = strtolower($class_name);
  $path = IGES_LIB_PATH.DS."iges-{$class_name}.php";
  if(file_exists($path)) {
    require_once($path);
    echo "string";
  } else {
		die("The file {$class_name}.php could not be found.");
	}
}

require_once (IGES_LIB_PATH.'iges-tensile-strength.php');
require_once (IGES_LIB_PATH.'iges-bend.php');
require_once (IGES_LIB_PATH.'iges-bspline-curve.php');
require_once (IGES_LIB_PATH.'iges-bspline-surface.php');
require_once (IGES_LIB_PATH.'iges-compute-functions.php');
require_once (IGES_LIB_PATH.'iges-edge.php');
require_once (IGES_LIB_PATH.'iges-entity.php');
require_once (IGES_LIB_PATH.'iges-face.php');
require_once (IGES_LIB_PATH.'iges-file.php');
require_once (IGES_LIB_PATH.'iges-file-params.php');
require_once (IGES_LIB_PATH.'iges-loop.php');
require_once (IGES_LIB_PATH.'iges-parameter.php');
require_once (IGES_LIB_PATH.'iges-parser.php');
require_once (IGES_LIB_PATH.'iges-shell.php');
require_once (IGES_LIB_PATH.'iges-vertex.php');
require_once (IGES_LIB_PATH.'iges-bend-features.php');
require_once (IGES_LIB_PATH.'iges-extract-core-fumctions.php');

?>
