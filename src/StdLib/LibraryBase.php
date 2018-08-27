<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\StdLib;

abstract class LibraryBase {

    /**
     * Registered Functions
     * 
     * @var array
     */
    protected $functions = [];

    /**
     * Add a function
     * 
     * @return void
     */
    protected function add(string $name, Callable $callback, bool $autoParseLazy = false) {
        $libraryBase = $this;
        if ($autoParseLazy) {
            $func = function ($args, \Pisp\VM\VM $vm) use ($callback, $libraryBase) {
                $args = array_map(function ($v) use ($libraryBase, $vm) {
                    return $libraryBase->parseLazy($v, $vm);
                }, $args);
                return $callback($args, $vm);
            };
        } else {
            $func = function ($args, \Pisp\VM\VM $vm) use ($callback, $libraryBase) {
                return $callback($args, $vm);
            };
        }
        $this->functions[$name] = $func;
    }

    /**
     * Register to a VM
     * 
     * @param \Pisp\VM\VM $vm
     * @return self
     */
    public function register(\Pisp\VM\VM $vm): LibraryBase {
        foreach ($this->functions as $k=>$v) {
            $vm->define($k, $v);
        }
        return $this;
    }

    /**
     * Create an instance of self
     */
    public static function instance(): LibraryBase {
        return new self;
    }

    /**
     * Parse the lazy call
     * 
     * @param \Pisp\Parser\AST\Node|mixed $param
     * @param \Pisp\VM\VM $vm
     * @return mixed
     */
    public function parseLazy($param, \Pisp\VM\VM $vm) {
        if ($param instanceof \Pisp\Parser\AST\Node) {
            return $vm->runNode($param);
        } else {
            return $param;
        }
    }

}