<?php

require_once "../../vendor/autoload.php";

$vm = new \Pisp\Pisp;

\Pisp\StdLib\StandardLibrary::register($vm);

$vm->define("_php", function ($args, $vm) {
    return eval($args[0]);
});

$epel = new \Pisp\Utils\RPEL($vm);

$epel->run();
