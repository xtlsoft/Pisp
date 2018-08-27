<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\Parser;

use \Pisp\Parser\AST\Root;
use \Pisp\Parser\AST\Node;

class ASTWalker {

    /**
     * The AST
     *
     * @var \Pisp\Parser\AST\Root
     */
    protected $ast = null;

    /**
     * Constructor
     *
     * @param Root $ast
     */
    public function __construct(Root $ast) {
        $this->ast = $ast;
    }

    /**
     * Walk the root
     *
     * @return void
     */
    public function __invoke(Callable $callback) {
        $this->walk($this->ast, $callback);
    }

    /**
     * Walk a node
     *
     * @param Node $ast
     * @param Callable $callback
     * @return void
     */
    public function walk(Node $ast, Callable $callback) {
        $callback($ast);
        foreach ($ast->children as $child) {
            self::walk($child, $callback);
        }
    }

}