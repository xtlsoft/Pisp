<?php

require_once "vendor/autoload.php";

$parser = new \Pisp\Parser\Parser;

$code = <<<EOF
(main
    (print
        (add
            [1]
            [2 ]
        )
    )
)
EOF;

$root = $parser->parse($code);

file_put_contents("ast.txt", var_export($root, 1));