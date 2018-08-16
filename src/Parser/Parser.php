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
        return preg_replace("/^\[([\s]+)/", " ", $code);
    }

    /**
     * Parse the code
     *
     * @param string $code
     * @return \Pisp\Parser\AST\Root
     */
    public function parse(string $code): Root {
        $code = $this->parseComment($code);
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
            $this->doParseLiteral(str_split(trim(substr($code, 1, -1))), $parentNode);
        } else if (str_replace([' ', ')', '(', '[', ']', ';'], ['', '', '', '', '', ''], $code) == $code) {
            $this->doParseCalling(str_split($code), $parentNode);
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
        $data = $this->parseLiteral($code);
        $node->setData($data);
        $node->parent = $parentNode;
        $parentNode->addChild($node);
    }

    /**
     * Parse a literal
     *
     * @param array $code
     * @return mixed
     */
    public function parseLiteral(array $code) {
        $codeStr = join($code, "");
        if ($code[0] == '"' || $code[0] == "'") {
            $data = substr($codeStr, 1, -1);
        } else if (is_numeric($codeStr)) {
            $data = $codeStr * 1;
        } else if ($code[0] == ':') {
            $data = [];
            $flag1 = false;
            $flag2 = false;
            $curr = "";
            foreach ($code as $k=>$v) {
                if ($k === 0) continue;
                if ($v === '"' && !$flag2) $flag1 = !$flag1;
                if ($v === "'" && !$flag1) $flag2 = !$flag2;
                if ($v === ',' && !$flag1 && !$flag2) {
                    $data[] = $this->parseLiteral(str_split(trim($curr)));
                    $curr = "";
                    continue;
                }
                $curr .= $v;
            }
            $data[] = $this->parseLiteral(str_split(trim($curr)));
        } else {
            $data = null;
        }
        return $data;
    }

    /**
     * Parse the comment
     *
     * @param string $codeStr
     * @return string
     */
    protected function parseComment(string $codeStr): string {
        $code = str_split($codeStr);
        $commentFlag = false;
        $rslt = "";
        foreach ($code as $k=>$v) {
            if ($v === '#' && @$code[$k + 1] === '|') {
                $commentFlag = true;
                continue;
            } else if ($v === '#' && $code[$k - 1] === '|') {
                $commentFlag = false;
                continue;
            }
            if ($commentFlag) {
                continue;
            }
            $rslt .= $v;
        }
        return $rslt;
    }

}