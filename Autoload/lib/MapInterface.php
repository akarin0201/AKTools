<?php

namespace AKTools\Autoload;

interface MapInterface {
    public static function get(): array;

    public static function refresh(): array;

    public static function saveToFile(array $map): bool;
}