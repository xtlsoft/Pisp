<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\Parser\AST;

class Node {

    /**
     * The node's type.
     *
     * @var string
     */
    public $type = "expr";
    
    /**
     * The name of the node.
     *
     * @var string
     */
    public $name = "collection";

    /**
     * Children of it
     *
     * @var Node[]
     */
    public $children = [];

    /**
     * The parent of it
     *
     * @var Node
     */
    public $parent = null;

    /**
     * The node's data
     *
     * @var mixed
     */
    public $data = null;

    /**
     * Add a child
     *
     * @param Node $child
     * @return self
     */
    public function addChild(Node $child): Node {
        $this->children[] = $child;
        return $this;
    }

    /**
     * Set the data
     *
     * @param mixed $data
     * @return self
     */
    public function setData($data): Node {
        $this->data = $data;
        return $this;
    }

}