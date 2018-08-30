<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\Utils;

class EPEL {

    /**
     * Check if the brackets matched
     *
     * @param string $code
     * @return bool
     */
    public static function checkBrackets(string $code): bool {
        $brackets = [
            "(" => ")",
            "[" => "]",
        ];
        $brackets2 = array_values($brackets);
        $stack = new \SplStack();
        $skipping = false;
        $code_splited = str_split($code);
        foreach ($code_splited as $v) {
            if ($v == '"') $skipping = !$skipping;
            if ($skipping) continue;
            if (isset($brackets[$v])) {
                $stack->push($brackets[$v]);
            }
            if (in_array($v, $brackets2)) {
                if ($stack->pop() != $v) {
                    throw new \Pisp\Exceptions\ParseException("Unmatched brackets: $v");
                }
            }
        }
        return $stack->isEmpty();
    }

    /**
     * The VM
     *
     * @var \Pisp\VM\VM
     */
    protected $vm = null;

    /**
     * Constructor
     *
     * @param \Pisp\VM\VM $vm
     */
    public function __construct(\Pisp\VM\VM $vm) {
        $this->vm = $vm;
    }

    /**
     * Read line
     *
     * @param string $prompt
     * @return string
     */
    public function read(string $prompt = "Pisp > "): string {
        $line = readline($prompt);
        readline_add_history($line);
        if (trim($line) === "exit") {
            echo "Bye\r\n";
            exit(0);
        }
        return $line;
    }

    /**
     * Read all the code
     *
     * @return string
     */
    public function readAll(): string {
        $code = $this->read();
        while (!self::checkBrackets($code)) {
            $code .= "\r\n" . $this->read("  >>> ");
        }
        return $code;
    }

    /**
     * Run EPEL for once
     *
     * @return mixed
     */
    public function runOnce() {
        $e = null;
        try {
            $code = $this->readAll();
        } catch (\Exception $e) {
            $e = "!Error: {$e->getMessage()}";
        }
        if ($e != null) return $e;
        try {
            $rslt = $this->vm->run((new \Pisp\Parser\Parser)->parse($code));
        } catch (\Exception $e) {
            $e = "!Error: {$e->getMessage()}";
        }
        if ($e != null) return $e;
        return $rslt;
    }

    /**
     * Pretty print result
     *
     * @param mixed
     * @return void
     */
    public function prettyPrint($rslt) {
        echo "==> " . json_encode($rslt) . "\r\n";
    }

    /**
     * Run EPEL loop
     *
     * @return void
     */
    public function run() {

        while (true) {
            $r = $this->runOnce();
            $this->prettyPrint($r);
        }

    }

}
