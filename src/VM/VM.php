<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\VM;

use \Pisp\Parser\AST\Root;
use \Pisp\Parser\AST\Node;
use \Pisp\Parser\AST\CallingNode;
use \Pisp\Parser\AST\LiteralNode;

class VM {

    /**
     * Functions
     *
     * @var array
     */
    protected $functions = [];

    /**
     * Default resolvers
     * 
     * @var array
     */
    protected $defaultResovlers = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->define("__defaultResolver__", [$this, "resolveDefault"]);
    }
    
    /**
     * Register a default resolver
     *
     * @param Callable $resolver
     * @param string $prefix
     * @return self
     */
    public function registerDefaultResolver(Callable $resolver, string $prefix = "NO"): self {
        @$this->defaultResolvers[$prefix][] = $resolver;
        return $this;
    }

    public function resolveDefault(string $name, array $args, \Pisp\VM\VM $vm) {
        $callbacks = "";
        $stopper = new DefaultResolver\Stopper;
        
    }

    /**
     * Run the root node
     *
     * @param Root $node
     * @return mixed
     */
    public function run(Root $node) {
        return $this->runNode($node);
    }

    /**
     * Run a node
     *
     * @param Node $node
     * @return void
     */
    public function runNode(Node $node) {
        if ($node instanceof LiteralNode) {
            return $node->data;
        } else if ($node instanceof CallingNode) {
            $name = $node->name;
            if (substr($name, 0, 1) == "@") {
                $args = $node->children;
                $name = substr($name, 1);
            } else {
                $args = [];
                foreach ($node->children as $child) {
                    $args[] = $this->runNode($child);
                }
            }
            return $this->doFunction($name, $args);
        } else if ($node instanceof Root) {
            $r = null;
            foreach ($node->children as $child) {
                $r = $this->runNode($child);
            }
            return $r;
        } else {
            throw new \Pisp\Exceptions\UnknownNodeException("Unknown node type: {$node->type}");
        }
    }

    /**
     * Do a function
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function doFunction(string $name, array $args) {
        if (!isset($this->functions[$name])) {
            if (isset($this->functions['__defaultResolver__'])) {
                $resolver = $this->functions['__defaultResolver__'];
                $r = $resolver($name, $args, $this);
                if ($r[0]) {
                    return $r[1];
                }
            }
            throw new \Pisp\Exceptions\NoFunctionException("Unknown function: {$name}");
            return;
        }
        $func = $this->functions[$name];
        if (is_callable($func)) {
            // ARGUMENTS VM_ENV
            return $func($args, $this);
        } else if ($func instanceof Node) {
            return $this->runNode($func);
        } else {
            return $func;
        }
    }

    /**
     * Define a function
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function define(string $name, $value): VM {
        $this->functions[$name] = $value;
        return $this;
    }

    /**
     * Delete a function
     *
     * @param string $name
     * @return self
     */
    public function delete(string $name): VM {
        unset($this->functions[$name]);
        return $this;
    }

    /**
     * Get a function
     * 
     * @param string $name
     * @return mixed
     */
    public function get(string $name) {
        return $this->functions[$name];
    }

}
