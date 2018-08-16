<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\Parser\AST;

class LiteralNode extends Node {

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        $this->name = "literal";
        $this->type = "literal";
    }

}