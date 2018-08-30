<?php

require_once "vendor/autoload.php";

$parser = new \Pisp\Parser\Parser;

$code = <<<EOF
(do
  (print abc)
  (@def bcd [321])
  (print bcd)
  (@def bcde (do (@get print)))
  (bcde (+ 1 2 (- (mod 120 9) 2 3 )))
  (@def
    repeat
    [: "a", "b"]
    (@block
      (print a b a b)
    )
  )
  (@fn
    print-2-string
    [: "str1", "str2"]
    (@@@
      (print (str1) (str2))
    )
  )
  (repeat ["abc"] ["bcd"])
  (print-2-string ["Hello"] ["World"])
  (print Hello)
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

$vm->define("__defaultResolver__", function ($name, $args, $vm) {
    return [true, $name];
});

\Pisp\StdLib\StandardLibrary::register($vm);

$vm->run($root);
echo PHP_EOL;
