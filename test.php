<?php

require_once "vendor/autoload.php";

$parser = new \Pisp\Parser\Parser;

$code = <<<EOF
(do
  (print abc)
  (@def bcd [321])
  (print bcd)
  (@def bcde (do (@get print)))
  (bcde (+ 1 2))
)
EOF;

$root = $parser->parse($code);

$vm = new \Pisp\VM\VM;

$vm->define("abc", "123");

$vm->define("print", function ($args, $vm) {
    foreach ($args as $v) {
        if (is_string($v) || method_exists($v, "__toString")) {
            print($v);
        } else {
            @var_export($v);
        }
    }
});

$lib = new \Pisp\StdLib\Calculating();
$lib->register($vm);
$lib2 = new \Pisp\StdLib\Basic();
$lib2->register($vm);

$vm->run($root);
echo PHP_EOL;
