<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\Parser\AST;

class CallingNode extends Node {

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        $this->name = "calling";
        $this->type = "calling";
    }

}