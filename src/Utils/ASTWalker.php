<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\Utils;

use \Pisp\Parser\AST\Root;
use \Pisp\Parser\AST\Node;
use \Pisp\Utils\ASTWalker\WalkerInterface;

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
     * @param Callable $pre
     * @param Callable $after
     * @return void
     */
    public function __invoke(WalkerInterface $walker) {
        $this->walk($this->ast, [$walker, "before"], [$walker, "after"]);
    }

    /**
     * Walk a node
     *
     * @param Node $ast
     * @param Callable $pre
     * @param Callable $after
     * @return void
     */
    public function walk(Node $ast, Callable $pre, Callable $after) {
        $pre($ast);
        foreach ($ast->children as $child) {
            self::walk($child, $pre, $after);
        }
        $after($ast);
    }

}