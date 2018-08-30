<?php

require_once "../../vendor/autoload.php";

$pisp = new \Pisp\Pisp;

\Pisp\StdLib\StandardLibrary::register($pisp);

$pisp->define("__defaultResolver__", function (string $name, array $args, \Pisp\VM\VM $vm) {
  if (substr($name, 0, 1) == "/") {
    return [true, $name];
  } else if (in_array(strtoupper($name), ["GET", "POST", "PUT", "DELETE", "WEBSOCKET", "CLI", "PATCH"])) {
    $rule = new Rule();
    $rule->method = strtoupper($name);
    $rule->regex = $args[0];
    $rule->controller = $args[1];
    return [true, $rule];
  } else if (substr($name, 0, 1) == "&") {
    return [true, substr($name, 1)];
  } else {
    return [false, null];
  }
});

class Rule {
  public $method = "";
  public $regex = "";
  public $controller = "";
}

class Group {
  public $base = "";
  public $rules = null;
}

class Router {
  public $rules = [];
}

$pisp->define("group", function (array $args, \Pisp\VM\VM $vm) {
  $group = new Group();
  $group->base = $args[0];
  $group->rules = array_slice($args, 1);
  return $group;
});

$pisp->define("router", function (array $args, \Pisp\VM\VM $vm) {
  $router = new Router();
  $router->rules = $args;
  return $router;
});

var_dump($pisp->execute(file_get_contents($argv[1])));
