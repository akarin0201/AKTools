<?php

/**
 * \AKTools\Autoload\Autoloader()
 * 
 * @author akarin0201@protonmail.com
 */

namespace AKTools\Autoload;

use AKTools\Autoload\AUTOLOAD_CFG as CFG;
// use AKTools\Autoload\AutoloaderInterface as AutoloaderInterface;

class Autoloader implements AutoloaderInterface {

    /**
     * Attempts to require a file
     * 
     * @param string filename
     * 
     * @return bool true on succes
     */
    protected function requireFile(string $file): bool {
        if(file_exists($file)) {
            require_once $file;
            return true;
        }

        return false;
    }

    /**
     * Attempts to construct the filename and load the file
     * 
     * @param string namespace
     * @param string class name
     * @param array namespaces map
     * 
     * @return string|bool full filename or false
     */
    protected function loadMappedFile(string $namespace, string $class, array $map): string|bool {
        if(isset($map[$namespace]) === false)
            return false;

        foreach($map[$namespace] as $dir) {
            $file = $dir . $class . ".php";
            
            if($this->requireFile($file)) return $file;
        }

        return false;
    }
    
    /**
     * Attempts to load the class
     * 
     * @param string fully qualified class name
     * @param array namepsaces map
     * 
     * @return string|bool filename or failure
     */
    public function loadClass(string $className, array $map): string|bool {
        $prefix = $className;

        while(false !== $pos = strrpos($prefix, '\\')) {
            $namespace = substr($prefix, 0, $pos + 1);
            $class = substr($prefix, $pos + 1);

            $file = $this->loadMappedFile($namespace, $class, $map);

            if($file) return $file;

            $prefix = rtrim($namespace, '\\');
        }

        return false;
    }
}