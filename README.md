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
