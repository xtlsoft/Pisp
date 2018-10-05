<?php

require_once "vendor/autoload.php";

$parser = new \Pisp\Parser\Parser;

$code = <<<EOF
(do
  (print abc)
  (@def bcd 321)
  (print bcd)
  (@def bcde (do (@get print)))
  (bcde (+ 1 2 (- (mod 120 9) 2 3 )))
  (@def
    repeat
    ["a", "b"]
    (@block
      (print a b a b)
    )
  )
  (@fn
    print-2-string
    ["str1", "str2"]
    (@@@
      (print (str1) (str2))
    )
  )
  (repeat "abc" "bcd")
  (print-2-string "Hello" "World")
  (print Hello)
  (@if (- 1 2)
    (print 1)
    (print 2)
  )
  (@def cond 10)
  (@while (cond)
    (print "\nCond is " cond)
    (@unless (- cond 5)
      (break)
    )
    (@def cond (- cond 1))
  )
  (print "\n")
  (@loop
    (print "Input number 5 => ")
    (@unless (- input 5)
      (break)
    )
  )
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

$vm->define("input", function ($args, $vm) {
    return trim(fgets(STDIN));
});

$vm->define("__defaultResolver__", function ($name, $args, $vm) {
    return [true, $name];
});

\Pisp\StdLib\StandardLibrary::register($vm);

$vm->run($root);
echo PHP_EOL;
