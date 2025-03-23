<?php

/**
 * \AKTools\Autoload\Autoload()
 * 
 * Dodać lepszą kontrolę fallbacka jeżeli register() się nie uda
 * Przeanalizować czy Map() na pewno powinno być statyczne
 * Uporządkować interfejsy
 * 
 * @author akarin0201@protonmail.com
 */

namespace AKTools\Autoload;

use AKTools\Autoload\AUTOLOAD_CFG as CFG;
use AKTools\Autoload\Map as Map;
use AKTools\Autoload\Autoloader as Autoloader;

class Autoload {
    private array $map;
    private Autoloader $autoloader;

    public function __construct() {
        $this->map = Map::get();
        $this->autoloader = new Autoloader();
    }

    public function debugMap(): void {
        print_r($this->map);
    }

    /**
     * registers the autloader
     */
    public function register(): void {
        spl_autoload_register(function ($className) {
            $this->autoloader->loadClass($className, $this->map);
        });
    }

    /**
     * Adds a user defined namespace to map
     * 
     * @param string full namespace
     * @param string path to directory
     * @param bool also save to map.json
     * 
     * @return bool true on success
     */
    public function addNamespace(string $namespace, string $dir, bool $saveToFile = false): bool {
        // normalize namespace
        $namespace = trim($namespace, "\\") . "\\";

        // normalize dir path
        $dir = preg_replace("/[\\/\\\\]/", DIRECTORY_SEPARATOR, $dir);

        $this->map = array_merge_recursive($this->map, [$namespace => [$dir]]);

        if($saveToFile) {
            if(Map::saveToFile($this->map) === false) return false;
        }

        return true;
    }

    /**
     * Performs a new dir scan
     */
    public function refreshMap(): void {
        $map = Map::refresh();
    }
}