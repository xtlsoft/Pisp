<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\StdLib;

class Basic extends LibraryBase {

    public function __construct() {
        $this->add("def", [$this, "define"], false);
        $this->add("define", [$this, "define"], false);
        $this->add("fn", [$this, "define"], false);
        $this->add("block", [$this, "block"], false);
        $this->add("@@", [$this, "block"], false);
        $this->add("do", [$this, "do"], false);
        $this->add("get", [$this, "get"], false);
    }

    public function define($args, \Pisp\VM\VM $vm) {
        if (count($args) != 2 && count($args) != 3) {
            throw new \Pisp\Exceptions\RuntimeException("Error in define: invalid arguments");
        }
        $name = $args[0];
        if ($name instanceof \Pisp\Parser\AST\LiteralNode) {
            $name = $name->data;
        }
        if ($name instanceof \Pisp\Parser\AST\CallingNode) {
            $name = $name->name;
        }
        if (count($args) == 2) {
            $value = $args[1];
        } else {
            $value = $args[2];
        }
        if ($value instanceof \Pisp\Parser\AST\LiteralNode) {
            $value = $value->data;
        }
        if ($value instanceof \Pisp\Parser\AST\CallingNode) {
            $value = $vm->runNode($value);
        }
        if (count($args) == 3) {
            $params = $args[1];
            if ($params instanceof \Pisp\Parser\AST\Node) {
                $params = $vm->runNode($params);
            }
            $val = $value;
            $value = function ($arguments, \Pisp\VM\VM $vm) use ($params, $val) {
                $now = [];
                foreach ($params as $k=>$param) {
                    $now[$param] = @$vm->get($param);
                    $vm->define($param, $arguments[$k]);
                }
                $rslt = $vm->runNode($val);
                foreach ($now as $k=>$v) {
                    $vm->define($k, $v);
                }
                return $rslt;
            };
        }
        $vm->define($name, $value);
        return $value;
    }

    public function block($args, \Pisp\VM\VM $vm) {
        $node = new \Pisp\Parser\AST\Root();
        array_map([$node, "addChild"], $args);
        return $node;
    }

    public function do($args, \Pisp\VM\VM $vm) {
        $r = null;
        foreach ($args as $v) {
            if ($v instanceof \Pisp\Parser\AST\Node) {
                $r = $vm->runNode($v);
            } else {
                $r = $v;
            }
        }
        return $r;
    }

    public function get($args, \Pisp\VM\VM $vm) {
        if (count($args) != 1) {
            throw new \Pisp\Exceptions\RuntimeException("Error in get: invalid arguments");
        }
        if ($args[0] instanceof \Pisp\Parser\AST\CallingNode) {
            $args[0] = $args[0]->name;
        }
        return $vm->get($args[0]);
    }

}

StandardLibrary::add(Basic::class);
