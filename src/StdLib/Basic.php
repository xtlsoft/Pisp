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

    }

    public function define($args, \Pisp\VM\VM $vm) {
        if (count($args) != 2) {
            throw new \Pisp\Exceptions\RuntimeException("Error in define: invalid arguments");
        }
        $name = $args[0];
        $value = $args[1];
        if ($value instanceof \Pisp\Parser\AST\LiteralNode) {
            $value = $value->data;
        }
        $vm->define($name, $value);
        return $value;
    }

}