<?php
/**
 * Pisp Project
 * 
 * @author Tianle Xu <xtl@xtlsoft.top>
 * @package Pisp
 * @license MIT
 */

namespace Pisp\StdLib;

class StandardLibrary {

    /**
     * The libraries
     *
     * @var array
     */
    protected static $libraries = [];

    /**
     * Add a library
     *
     * @param string $classname
     * @return void
     */
    public static function add(string $classname) {
        $object = new $classname;
        self::$libraries[] = $object;
    }

    /**
     * Register to a vm
     *
     * @param \Pisp\VM\VM $vm
     * @return void
     */
    public static function register(\Pisp\VM\VM $vm) {
        foreach (self::$libraries as $library) {
            $library->register($vm);
        }
    }

}

// Load libraries
(new Calculating);
(new Basic);
