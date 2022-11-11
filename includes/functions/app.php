<?php

function debug(...$var) { // permet de faire un var_dump plus lisible. Peut contenir plusieurs arguments (debug($var1, $var2, $var3, ...))
    foreach ($var as $value) {
        echo '<pre>';
        print_r($value);
        echo '</pre>';
    }
}

?>