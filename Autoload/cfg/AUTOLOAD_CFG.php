<?php

namespace AKTools\Autoload;

class AUTOLOAD_CFG {

    // relative path to the map.json
    public const MAP_FILE = "../cfg/map.json";

    // your root dir
    public const ROOT_DIR = "app";
    public const ROOT_NAMESPACE = "Vendor";

    /**
     * Directories to be ommited by mapper
     * 
     * eg.
     * IGNORE = [
     *     "app/public",
     *     "app/some/other/dir"
     * ]
     */
    public const IGNORE = [];

    /**
     * Custom namespaces to be appended to the map
     * 
     * eg.
     * CUSTOM = [
     *     "MyNamespace\\" => [
     *         "path/to/MyNamespace/src",
     *         "path/to/MyNamespace/lib"
     *     ],
     *     "Some\Other\Namespace" => [
     *         "path/to/dir"
     *     ]
     * ]
     */
    public const CUSTOM = [];

    // decides wether CUSTOM should be saved to file
    public const SAVE_CUSTOM = false;
    // decides wether the map should be saved to map.json after scan
    public const SAVE_ON_SCAN = true;
}