<?php

/**
 * \AKTools\Autoload\Map()
 * 
 * @author akarin0201@protonmail.com
 */

namespace AKTools\Autoload;

use AKTools\Autoload\AUTOLOAD_CFG as CFG;

class Map implements MapInterface{
    
    public static function get(): array {
        $map = self::mapFromFile()
            ?: self::mapFromScan()
            ?: self::mapFailure();

        if(count(CFG::CUSTOM) > 0)
            $map = self::appendCustom($map);

        if(CFG::SAVE_CUSTOM)
            self::saveToFile($map);

        return $map;
    }

    public static function refresh(): array {
        $map = self::mapFromScan()
            ?: self::mapFailure();
        
        if(count(CFG::CUSTOM) > 0)
            $map = self::appendCustom($map);

        if(CFG::SAVE_CUSTOM)
            self::saveToFile($map);

        return $map;
    }

    protected static function mapFailure(): array {
        return [];
    }

    /**
     * Appends custom namespaces defined in CFG::CUSTOM
     * 
     * @param array current map
     * 
     * @return array map with CFG::CUSTOM appended
     */
    protected static function appendCustom(array $map): array {
        $custom = CFG::CUSTOM;
        
        foreach($custom as &$path)
            $path = preg_replace("/[\\/\\\\]/", DIRECTORY_SEPARATOR, $path);
        unset($path);

        return array_merge_recursive($map, $custom);
    }

    /**
     * Attempts to find map.json
     * 
     * @return string|bool path or false
     */
    protected static function findMapFile(): string|bool {
        if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . CFG::MAP_FILE))
            return __DIR__ . DIRECTORY_SEPARATOR . CFG::MAP_FILE;

        else return false;
    }

    /**
     * Attempts to load namespaces map from file
     * 
     * @return array|bool map or false
     */
    protected static function mapFromFile(): array|bool {
        if($mapFile = self::findMapFile())
            return json_decode(file_get_contents($mapFile), JSON_OBJECT_AS_ARRAY) ?? false;

        return false;
    }

    /**
     * Attempts to find abs path to root dir
     * CAUTION!
     * 
     * @return string|bool path or false
     */
    protected static function findRootDir(): string|bool {
        $dir = __DIR__;
        $prevDir = null;

        // these are all conditions to prevent an infinite loop
        while($dir !== "" && $dir !== "/" && $dir !== $prevDir) {
            if(basename($dir) == CFG::ROOT_DIR)
                return basename($dir); // CAUTION! basename($dir) may be needed to be changed to just $dir
            $prevDir = $dir;
            $dir = dirname($dir);
        }

        return false;
    }

    /**
     * Maps namespace by path
     * 
     * @param string path
     * @return string namespace
     */
    protected static function mapNamespace(string $dir): string {
        $ancestry = array_reverse(explode(DIRECTORY_SEPARATOR, $dir));
        $namespace = [];
        $ancestryCount = count($ancestry);

        for($i = 0; $i < $ancestryCount; $i++) {
            $prevAncestor = $ancestry[$i + 1] ?? "fail";

            if($ancestry[$i] === ucfirst($ancestry[$i]))
                $namespace[] = $ancestry[$i];

            else if($prevAncestor === ucfirst($prevAncestor))
                continue;

            else if($ancestry[$i] === CFG::ROOT_DIR)
                $namespace[] = CFG::ROOT_NAMESPACE;

            else break;
        }

        if(empty($namespace)) $namespace[] = CFG::ROOT_NAMESPACE;

        $namespace = implode("\\", array_reverse($namespace)) . "\\";

        return $namespace;
    }

    /**
     * Performs a recursive search for directories
     * 
     * @return array map
     */
    protected static function recursiveMap(string $dir, array &$map): array {

        // check if dir is ignored
        if(in_array(str_replace(DIRECTORY_SEPARATOR, "/", $dir), CFG::IGNORE) === false) {

            $namespace = self::mapNamespace($dir);

            // check if namespace exists
            if(array_key_exists($namespace, $map) === false)
                $map[$namespace] = [];
            $map[$namespace][] = $dir . DIRECTORY_SEPARATOR;
        }

        $dirList = scandir($dir);
        

        foreach($dirList as $file) {
            if(is_dir($dir . DIRECTORY_SEPARATOR . $file) && $file !== "." && $file !== "..") {
                self::recursiveMap($dir . DIRECTORY_SEPARATOR . $file, $map);
            }
        }

        return $map;
    }

    /**
     * Saves map to file
     * 
     * @param array map
     * 
     * @return bool true on succes
     */
    public static function saveToFile(array $map): bool {
        if(file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . CFG::MAP_FILE, json_encode($map), LOCK_EX) === false)
            return false;

        return true;
    }

    /**
     * Attempts to create map by scanning dir structure
     * 
     * @return array|bool map or false
     */
    protected static function mapFromScan(): array|bool {
        $map = [];
        // est. abs path to root or fail
        if(($rootDir = self::findRootDir()) === false) return false;

        $map = self::recursiveMap($rootDir, $map);

        if(CFG::SAVE_ON_SCAN) {
            if(self::saveToFile($map) === false) {
                // log error
                print_r("Autoloader says: Saving file fucked up!");
            }
        }

        return $map;
    }
}