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
use \Pisp\Parser\AST\CallingNode;
use \Pisp\Parser\AST\LiteralNode;
use \Pisp\Exceptions\ParseException;

class Parser {

    /**
     * Constructor
     */
    public function __construct() {

    }

    /**
     * Clean the given code
     *
     * @param string $code
     * @return string
     */
    protected function cleanCode(string $code): string {
        $code = trim($code);
        return preg_replace("/([\s]+)/", " ", $code);
    }

    /**
     * Parse the code
     *
     * @param string $code
     * @return \Pisp\Parser\AST\Root
     */
    public function parse(string $code): Root {
        $root = new Root;
        $this->doParse($code, $root);
        return $root;
    }

    /**
     * Do the parse operation
     *
     * @param string $code
     * @param Node $parentNode
     * @return void
     */
    protected function doParse(string $code, Node $parentNode) {

        $code = $this->cleanCode($code);
        if ($code === "") {
            return;
        }

        if (substr($code, 0, 1) == '(' && substr($code, -1, 1) == ')') {
            $this->doParseCalling(str_split(substr($code, 1, -1)), $parentNode);
        } else if (substr($code, 0, 1) == '[' && substr($code, -1, 1) == ']') {
            $this->doParseLiteral(str_split(substr($code, 1, -1)), $parentNode);
        } else {
            throw new ParseException("Parse error: unmatched brackets.");
        }

    }

    /**
     * Do parse calling node
     *
     * @param array $code
     * @param Node $parentNode
     * @return void
     */
    protected function doParseCalling(array $code, Node $parentNode) {
        $node = new CallingNode;
        $node->parent = $parentNode;
        $stack = new \SplStack();
        $stack2 = new \SplStack();
        $splited = [""];
        $curr = 0;
        foreach ($code as $k=>$v) {
            if ($v === '(' && $stack2->isEmpty()) {
                $stack->push(true);
            } else if (($v === ')') && !$stack->isEmpty() && $stack2->isEmpty()) {
                $stack->pop();
            }
            if ($v === '[') {
                $stack2->push(true);
            } else if (($v === ']') && !$stack2->isEmpty()) {
                $stack2->pop();
            }
            if ($stack->count() <= 1) {
                if ($v == " " && $stack->isEmpty() && $stack2->isEmpty()) {
                    $curr ++;
                    $splited[$curr] = "";
                }
            }
            $splited[$curr] .= $v;
        }
        var_dump($splited);
        $node->name = $splited[0];
        $parentNode->addChild($node);
        for ($i = 1; $i < count($splited); ++ $i) {
            $v = $splited[$i];
            $this->doParse($v, $node);
        }
    }

    /**
     * Do parse of literals
     *
     * @param array $code
     * @param Node $parentNode
     * @return void
     */
    protected function doParseLiteral(array $code, Node $parentNode) {
        $node = new LiteralNode;
        $code = join($code, "");
        $node->setData(eval("return " . $code. ";"));
        $node->parent = $parentNode;
        $parentNode->addChild($node);
    }

}