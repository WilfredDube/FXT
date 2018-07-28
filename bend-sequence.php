<?php

require_once 'vendor/autoload.php';

use mcordingley\LinearAlgebra\Matrix;

$matrix = new Matrix([
  [0, 1, 2],
  [3, 4, 5],
  [6, 7, 8]
]);

$threeByThreeIdentityMatrix = Matrix::identity(3);

var_dump($matrix);

?>
