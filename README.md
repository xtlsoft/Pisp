# Pisp

A lisp-like language for php.

## Overview

This is a sample Hello World code of it.

PHP Code:

```php
<?php
require_once "vendor/autoload.php";
$pisp = new \Pisp\Pisp;
$code = file_get_contents("code.pisp");
$pisp->define("print", function ($args, $vm) {
    foreach ($args as $v) {
        if (is_string($v) || method_exists($v, "__toString")) {
            echo $v;
        } else {
            var_dump($v);
        }
    }
});
$pisp->execute($code);
```

Content of code.pisp:

```lisp
(print ["Hello World"] ["\n"])
```

Result:

```plain
Hello World
```

## Installation

```bash
composer require xtlsoft/pisp
```

## Documentation

### Basic PHP API

#### \Pisp\Pisp

We have built a facade for you.
You can use it easily.

```php
<?php
$pisp = new \Pisp\Pisp();
```

Right, The `\Pisp\Pisp` class is the facade.

It extends the `\Pisp\VM\VM` class and have an `execute` method to execute code directly.

For example:

```php
<?php
$code = '(print ["Hello World"] ["\n"])';
$pisp->execute($code);
```

#### \Pisp\VM\VM

This is the main VM class.

We have a define and a delete method which are used to define and delete functions.

Yes! Variables are also functions in Pisp because it is purely functional.

```php
<?php
$vm = new \Pisp\Pisp; // Also can be $vm = new \Pisp\VM\VM;

$vm->define("abc", 123);
$vm->define("+", function ($args, $vm) {
    return $args[0] + $args[1];
});

$vm->delete("abc");

echo $vm->execute("(+ 1 2)"); // 3
```

Have you noticed? When defining a function, it must a valid callback with 2 parameters.
The first one is the array of the real arguments, and the second one is the instance of the \Pisp\VM\VM class.

You can dynamically add functions.

#### \Pisp\Parser\Parser

This is for parsing code.

```php
<?php
$parser = new \Pisp\Parser\Parser;
$rslt = $parser->parse('(print ["Hello World\n"])');
var_export($rslt instanceof \Pisp\Parser\AST\Root); // true
```

#### \Pisp\Parser\ASTWalker

This is for walking the AST.

```php
<?php
$walker = new \Pisp\Parser\ASTWalker($rslt);
$walker->walk(function (\Pisp\Parser\AST\Node $node) {
    echo $node->name, PHP_EOL;
});
```

### Grammar and language specifications

#### Basic Grammar

A function call starts with a `(` and ends with a `)` .
Function name and arguments are separated by any blank characters.

Arguments are optional.

For example:

```lisp
(+ [1] [2])
(+
 [1]
 [2]
)
( + [1] [2] )
(a_function_call_without_arguments)
```

The literals are surrounded by `[` and `]` .

For example:

```lisp
(+ [1] [2])
(print ["a string"])
(+ [1.2] [1.4])
```

Moreover, Pisp supports lazy calls.

Just add an `@` before the function name and the arguments will be their ASTs.

```lisp
(@print (undefined_function))
```

This will outputs the var_dump result of the `\Pisp\Parser\AST\CallingNode` class.

#### Default Functions

Pisp doesn't include any functions by default. This means, if you runs the examples above, you will get a `NoFunctionException`. You must define them by yourself.

#### Comments

Pisp doesn't support comments now, but you can use a little trick to let it support it:

```php
<?php
$pisp = new \Pisp\Pisp;
$pisp->define("rem", function ($args, $vm) {
    return;
});
```

Then, you can just use:

```lisp
(@rem ["This is a comment"])
```

And this won't be executed.
