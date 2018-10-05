<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\StdLib;

// TODO: BREAK IS NOT THREAD SAFE, FIX IT!
class FlowControl extends LibraryBase {

    /**
     * Constructor
     */
    public function __construct() {

        $this->add("if", [$this, "myif"], false);
        $this->add("unless", [$this, "unless"], false);
        $this->add("while", [$this, "mywhile"], false);
        $this->add("loop", [$this, "loop"], false);
        $this->add("break", [$this, "mybreak"], false);

    }

    public function myif($args, \Pisp\VM\VM $vm) {
        if (count($args) != 2 && count($args) != 3) {
            throw new \Pisp\Exceptions\RuntimeException("Error in if: Invalid parameter count.");
        }
        $condition = $vm->runNode($args[0]);
        $then = $args[1];
        $else = isset($args[2]) ? $args[2] : null;
        if ($condition) {
            return $vm->runNode($then);
        } else {
            if ($else !== null) {
                return $vm->runNode($else);
            } else {
                return false;
            }
        }
    }

    public function unless($args, \Pisp\VM\VM $vm) {
        if (count($args) != 2 && count($args) != 3) {
            throw new \Pisp\Exceptions\RuntimeException("Error in if: Invalid parameter count.");
        }
        $condition = $vm->runNode($args[0]);
        $then = $args[1];
        $else = isset($args[2]) ? $args[2] : null;
        if (!$condition) {
            return $vm->runNode($then);
        } else {
            if ($else !== null) {
                return $vm->runNode($else);
            } else {
                return false;
            }
        }
    }

    public function mywhile($args, \Pisp\VM\VM $vm) {
        if (count($args) < 1) {
            throw new \Pisp\Exceptions\RuntimeException("Error in if: Invalid parameter count.");
        }
        $condition = $args[0];
        $instructions = array_slice($args, 1);
        $node = new \Pisp\Parser\AST\Root();
        foreach ($instructions as $instruction) {
            $node->addChild($instruction);
        }
        $rval = false;
        $vm->define("__loop_breaked__", false);
        while ($vm->runNode($condition)) {
            if ($vm->get("__loop_breaked__") === true) {
                $vm->define("__loop_breaked__", false);
                break;
            }
            $rval = $vm->runNode($node);
        }
        return $rval;
    }

    public function mybreak($args, \Pisp\VM\VM $vm) {
        $vm->define("__loop_breaked__", true);
        return true;
    }

    public function loop($args, \Pisp\VM\VM $vm) {
        $args = array_merge([(new \Pisp\Parser\AST\LiteralNode())->setData(true)], $args);
        return $this->mywhile($args, $vm);
    }

}

StandardLibrary::add(FlowControl::class);
