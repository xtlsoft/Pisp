<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\VM\DefaultResolver;

class Stopper {

    /**
     * Has stopped
     * 
     * @var bool
     */
    protected $stopped = false;

    /**
     * Stop the operation
     *
     * @return void
     */
    public function stop() {
        $this->stopped = true;
    }

    /**
     * Check has it stopped
     *
     * @return boolean
     */
    public function hasStopped(): bool {
        return $this->stopped;
    }

}