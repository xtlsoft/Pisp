<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\Utils;

interface WalkerInterface {

    /**
     * Before walking a node
     *
     * @param Node $node
     * @return void
     */
    public function before(Node $node);

    /**
     * After walking a node
     *
     * @param Node $node
     * @return void
     */
    public function after(Node $node);

}