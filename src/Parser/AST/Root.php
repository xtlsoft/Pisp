<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\Parser\AST;

class Root extends Node {

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        $this->name = "root";
        $this->type = "root";
    }

}