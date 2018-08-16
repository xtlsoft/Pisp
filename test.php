<?php

require_once "vendor/autoload.php";

$parser = new \Pisp\Parser\Parser;

$code = <<<EOF
(@print ["Hello World". "\\r\\n"] ["New Hello"])
EOF;

$root = $parser->parse($code);

$vm = new \Pisp\VM\VM;

$vm->define("print", function ($args, $vm) {
    foreach ($args as $v) {
        if (is_string($v) || method_exists($v, "__toString")) {
            print($v);
        } else {
            var_dump($v);
        }
    }
});

echo $vm->run($root);