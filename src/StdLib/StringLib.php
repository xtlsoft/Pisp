<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\StdLib;

class StringLib extends LibraryBase {

    /**
     * Constructor
     */
    public function __construct() {

        $this->add("strcat", [$this, "strcat"], true);
        $this->add("join", [$this, "join"], true);
        $this->add("chr", [$this, "chr"], true);
        $this->add("ord", [$this, "ord"], true);
        $this->add("strlen", [$this, "strlen"], true);
        $this->add("print", [$this, "print"], true);

    }

    public function strcat($args, \Pisp\VM\VM $vm) {
        return implode("", $args);
    }

    public function join($args, \Pisp\VM\VM $vm) {
        return implode($args[0], array_slice($args, 1));
    }

    public function chr($args, \Pisp\VM\VM $vm) {
        if (count($args) != 1) {
            throw new \Pisp\Exceptions\RuntimeException("Error in chr: Invalid parameter count.");
        }
        return chr($args[0]);
    }

    public function ord($args, \Pisp\VM\VM $vm) {
        if (count($args) != 1) {
            throw new \Pisp\Exceptions\RuntimeException("Error in chr: Invalid parameter count.");
        }
        return ord($args[0]);
    }

    public function strlen($args, \Pisp\VM\VM $vm) {
        return strlen(implode("", $args));
    }

    public function ($args, $vm) {
        foreach ($args as $v) {
            if (is_string($v) || method_exists($v, "__toString")) {
                print($v);
            } else {
                @var_export($v);
            }
				}
				return $args;
    }

}

StandardLibrary::add(StringLib::class);
