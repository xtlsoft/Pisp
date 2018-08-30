<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\StdLib;

class Calculating extends LibraryBase {

    /**
     * Constructor
     */
    public function __construct() {

        $this->add("+", [$this, "sum"], true);
        $this->add("sum", [$this, "sum"], true);
        $this->add("-", [$this, "sub"], true);
        $this->add("sub", [$this, "sub"], true);
        $this->add("*", [$this, "mul"], true);
        $this->add("mul", [$this, "mul"], true);
        $this->add("/", [$this, "div"], true);
        $this->add("div", [$this, "div"], true);
        $this->add("mod", [$this, "mod"], true);
        $this->add("pow", [$this, "pow"], true);
        $this->add("min", [$this, "min"], true);
        $this->add("max", [$this, "max"], true);

    }

    public function sum($args, \Pisp\VM\VM $vm) {
        return array_sum($args);
    }

    public function sub($args, \Pisp\VM\VM $vm) {
        $toMinus = $args[0];
        return $toMinus - array_sum(array_slice($args, 1));
    }

    public function mul($args, \Pisp\VM\VM $vm) {
        return array_product($args);
    }

    public function div($args, \Pisp\VM\VM $vm) {
        $toDiv = $args[0];
        return $toDiv / array_product(array_slice($args, 1));
    }

    public function mod($args, \Pisp\VM\VM $vm) {
        if (count($args) != 2 || (!is_numeric($args[0])) || (!is_numeric($args[1])))
            throw new \Pisp\Exceptions\RuntimeException("Error in %: invalid argument");
        return $args[0] % $args[1];
    }

    public function pow($args, \Pisp\VM\VM $vm) {
        if (count($args) != 2 || (!is_numeric($args[0])) || (!is_numeric($args[1])))
            throw new \Pisp\Exceptions\RuntimeException("Error in pow: invalid argument");
        return pow($args[0], $args[1]);
    }

    public function min($args, \Pisp\VM\VM $vm) {
        return min($args);
    }

    public function max($args, \Pisp\VM\VM $vm) {
        return max($args);
    }

}

StandardLibrary::add(Calculating::class);
