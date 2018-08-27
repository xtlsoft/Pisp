<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp;

class Pisp extends \Pisp\VM\VM {

    /**
     * Execute the code
     *
     * @param string $code
     * @return void
     */
    public function execute(string $code) {
        return $this->run((new \Pisp\Parser\Parser)->parse($code));
    }

}