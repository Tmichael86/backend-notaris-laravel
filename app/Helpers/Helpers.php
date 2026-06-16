
<?php

if(!function_exists('formatUang')) {
    function formatUang($value) {
        return number_format($value, 0, ',', '.');
    }
}