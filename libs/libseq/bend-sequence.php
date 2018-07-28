<?php
require_once 'vendor/autoload.php';

use mcordingley\LinearAlgebra\Matrix;

class BendSequence {
  // public x;

  public function manipu() {
    $matrix = new Matrix([
      [0, 1, 2],
      [3, 4, 5],
      [6, 7, 8]
    ]);

    $threeByThreeIdentityMatrix = Matrix::identity(3);

    var_dump($threeByThreeIdentityMatrix);
  }

}

$x = new BendSequence();
$x->manipu();

?>
