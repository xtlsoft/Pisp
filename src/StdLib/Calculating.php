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

        $this->add("+", [$this, "plus"], true);
        $this->add("plus", [$this, "plus"], true);
        $this->add("-", [$this, "minus"], true);
        $this->add("minus", [$this, "minus"], true);
        $this->add("*", [$this, "mul"], true);
        $this->add("mul", [$this, "mul"], true);
        $this->add("/", [$this, "div"], true);
        $this->add("div", [$this, "div"], true);
        $this->add("mod", [$this, "mod"], true);
        $this->add("pow", [$this, "pow"], true);
        $this->add("min", [$this, "min"], true);
        $this->add("max", [$this, "max"], true);

    }

    public function plus($args, \Pisp\VM\VM $vm) {
        return array_sum($args);
    }

    public function minus($args, \Pisp\VM\VM $vm) {
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
