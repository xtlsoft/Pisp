<?php

require_once "../../vendor/autoload.php";

$vm = new \Pisp\Pisp;

\Pisp\StdLib\StandardLibrary::register($vm);

$epel = new \Pisp\Utils\EPEL($vm);

$epel->run();
