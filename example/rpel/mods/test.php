
<?php

$vm->define("test_php_function", function ($args, $vm) {
    return count($args);
});
