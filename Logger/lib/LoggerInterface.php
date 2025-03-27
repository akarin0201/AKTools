<?php

namespace AKTools\Logger;

interface LoggerInterface {

    public function debug(string $message, array $args = [], bool $forceSave = false): string;

    public function info(string $message, array $args = [], bool $forceSave = false): string;

    public function notice(string $message, array $args = [], bool $forceSave = false): string;

    public function warning(string $message, array $args = [], bool $forceSave = false): string;

    public function error(string $message, array $args = [], bool $forceSave = true): string;

    public function critical(string $message, array $args = [], bool $forceSave = true): string;
}