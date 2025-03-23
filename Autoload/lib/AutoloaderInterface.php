<?php

namespace AKTools\Autoload;

interface AutoloaderInterface {
    
    public function loadClass(string $className, array $map): string|bool;
}