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
     * Check is Blank character
     *
     * @param string $ch
     * @return bool
     */
    protected function isBlankCharacter(string $ch): bool {
        static $blanks = [' ', "\t", "\n", "\r", ''];
        return in_array($ch, $blanks);
    }

    /**
     * Clean the given code
     *
     * @param string $code
     * @return string
     */
    public function cleanCode(string $code): string {
        $codeArr = str_split(trim($code));
        $quote = false;
        $flag = '';
        $rslt = '';
        foreach ($codeArr as $k=>$v) {
            if ($v === '"' && (@$codeArr[$k - 1] != "\\") && $flag === false) $flag = '"';
            if ($v === '"' && (@$codeArr[$k - 1] != "\\") && $flag === '"') $flag = '';
            if ($v === "'" && (@$codeArr[$k - 1] != "\\") && $flag === false) $flag = "'";
            if ($v === "'" && (@$codeArr[$k - 1] != "\\") && $flag === "'") $flag = '';
            if ($flag === '') {
                $rslt .= $v;
                continue;
            }
            if ($quote) {
                if (!$this->isBlankCharacter($v)) {
                    $quote = false;
                    $rslt .= $v;
                }
            } else {
                if ($this->isBlankCharacter($v)) {
                    $quote = true;
                    $rslt .= " ";
                } else {
                    $rslt .= $v;
                }
            }
        }
        return $rslt;
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
            $this->doParseLiteral(str_split($code), $parentNode);
        } else if (substr($code, 0, 1) == '"' || substr($code, 0, 1) == "'") {
            $this->doParseLiteral(str_split($code), $parentNode);
        } else if (is_numeric($code)) {
            $this->doParseLiteral(str_split($code), $parentNode);
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
        if (count($code) === 1 && ($code[0] === "" || $code[0] === " ")) return;
        $node = new CallingNode;
        $node->parent = $parentNode;
        $stack = new \SplStack();
        $stack2 = new \SplStack();
        $inquote1 = false;
        $inquote2 = false;
        $splited = [""];
        $curr = 0;
        foreach ($code as $k=>$v) {
            if ($v === '"' && $code[$k - 1] !== '\\' && !$inquote2) $inquote1 = !$inquote1;
            if ($v === "'" && $code[$k - 1] !== '\\' && !$inquote1) $inquote2 = !$inquote2;
            if ($v === '(' && $stack2->isEmpty() && !$inquote2 && !$inquote1) {
                $stack->push(true);
            } else if (($v === ')') && !$stack->isEmpty() && $stack2->isEmpty() && !$inquote2 && !$inquote1) {
                $stack->pop();
            }
            if ($v === '[' && !$inquote2 && !$inquote1) {
                $stack2->push(true);
            } else if (($v === ']') && !$stack2->isEmpty() && !$inquote2 && !$inquote1) {
                $stack2->pop();
            }
            if ($stack->count() <= 1) {
                if ($v === ' ' && $stack->isEmpty() && $stack2->isEmpty() && (!$inquote1) && (!$inquote2)) {
                    $curr ++;
                    $splited[$curr] = "";
                }
            }
            $splited[$curr] .= $v;
        }
        $real = [];
        foreach ($splited as $v) {
            if (!$this->isBlankCharacter($v)) {
                $real[] = trim($v);
            }
        }
        $splited = $real;
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
            $data = $this->parseStringLiteral($data);
        } else if (is_numeric($codeStr)) {
            $data = $codeStr * 1;
        } else if ($code[0] == '[') {
            $data = [];
            $flag1 = false;
            $flag2 = false;
            $stack = new \SplStack();
            $curr = "";
            if ($code[count($code) - 1] !== ']') throw new ParseException("Unmatched brackets.");
            unset($code[count($code) - 1]);
            foreach ($code as $k=>$v) {
                if ($k === 0) continue;
                if ($v === '"' && $code[$k - 1] !== '\\' && !$flag2) $flag1 = !$flag1;
                if ($v === "'" && $code[$k - 1] !== '\\' && !$flag1) $flag2 = !$flag2;
                if ($v === '[' && !$flag1 && !$flag2) $stack->push(true);
                if ($v === ']' && !$flag1 && !$flag2) {
                    try {
                        $stack->pop();
                    } catch (\Exception $e) {
                        throw new ParseException("Unmatched brackets.");
                    }
                }
                if ($v === ',' && !$flag1 && !$flag2 && $stack->isEmpty()) {
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
     * Parse a string literal
     *
     * @param string $data
     * @return string
     */
    public function parseStringLiteral(string $data): string {
        static $map = [
            '\n'   => "\n",
            '\r'   => "\r",
            '\t'   => "\t",
            '\\\'' => "'",
            '\\"'  => '"',
            '\\\\' => '\\',
        ];
        static $map_values = [
            "\\\n" => '\\\n',
            "\\\r" => '\\\r',
            "\\\t" => '\\\t',
            "\\'"  => '\\\\\'',
            '\\"'  => '\\\\"',
        ];
        $data = str_replace(array_keys($map), array_values($map_values), $data);
        $data = str_replace(array_keys($map_values), array_values($map_values), $data);
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
        $commentStack = new \SplStack();
        $rslt = "";
        foreach ($code as $k=>$v) {
            if ($v === '#' && @$code[$k + 1] === '|') {
                $commentStack->push(true);
                continue;
            } else if ($v === '#' && $code[$k - 1] === '|') {
                if ($commentStack->isEmpty()) {
                    throw new \Pisp\Exceptions\ParseException("Comment brackets not matched.");
                }
                $commentStack->pop();
                continue;
            }
            if (!$commentStack->isEmpty()) {
                continue;
            }
            $rslt .= $v;
        }
        return $rslt;
    }

}
