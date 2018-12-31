<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\Utils;

class ClosureWalker {

    /**
     * Before closure
     *
     * @var Callable
     */
    protected $before = null;
    /**
     * After closure
     *
     * @var Callable
     */
    protected $after = null;

    /**
     * Constructor
     *
     * @param Callable $before
     * @param Callable $after
     */
    public function __construct(Callable $before, Callable $after) {
        $this->before = $before;
        $this->after = $after;
    }

    /**
     * Before walking a node
     *
     * @param Node $node
     * @return void
     */
    public function before(Node $node) {
        return call_user_func($this->before, $node);
    }

    /**
     * After walking a node
     *
     * @param Node $node
     * @return void
     */
    public function after(Node $node) {
        return call_user_func($this->after, $node);
    }

}