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
        if (count($args) != 2) {
            throw new \Pisp\Exceptions\RuntimeException("Error in define: invalid arguments");
        }
        $name = $args[0];
        if ($name instanceof \Pisp\Parser\AST\LiteralNode) {
            $name = $name->data;
        }
        if ($name instanceof \Pisp\Parser\AST\CallingNode) {
            $name = $name->name;
        }
        $value = $args[1];
        if ($value instanceof \Pisp\Parser\AST\LiteralNode) {
            $value = $value->data;
        }
        if ($value instanceof \Pisp\Parser\AST\CallingNode && $value->name === "do") {
            $value = $vm->runNode($value);
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