<?php

/**
 * \AKTools\Logger\Logger()
 * 
 * @author akarin0201@protonmail.com
 */

namespace AKTools\Logger;

use AKTools\Logger\LOGGER_CFG as CFG;

class Logger implements LoggerInterface {

    /**
     * Replaces {key} with corresponding value
     * 
     * @param string message to interpolate
     * @param array array where {key} => value
     * 
     * @return string interpolated message
     */
    protected function interpolate(string $message, array $args): string {
        if(count($args) > 0) {
            $replaced = $message;
            foreach($args as $k => $v) {
                if(!is_array($v) && (!is_object($v) || method_exists($v, '__toString'))) {
                    $replaced = str_replace("{" . $k . "}", $v, $replaced);
                }
            }

            return $replaced;
        }

        return $message;
    }

    /**
     * Saves message to log file
     * 
     * @param string destination file
     * @param string message
     * 
     * @return string|bool saved message or error message
     */
    protected function saveToFile(string $fileName, string $message): string {
        if(file_put_contents(preg_replace("/[\\/\\\\]/", DIRECTORY_SEPARATOR, $fileName), $message . "\n", FILE_APPEND | LOCK_EX) === false) {
            return $this->error("Logger Could not save the message to {fileName}!", ["fileName" => $fileName]);
        }

        return $message;
    }

    /**
     * Creates a full Logger message
     * 
     * @param string log level
     * @param string message
     * @param array argumets
     * 
     * @return string complete message
     */
    protected function createMessage(string $logLevel, string $message, array $args): string {
        return "[" . date(CFG::DATE_FORMAT) . "] " . strtoupper($logLevel) . ": " . $this->interpolate($message, $args);
    }

    /**
     * Logs a message and saves it to file if desired
     * 
     * @param string AKTools\Logger\LOGGER_CFG log level
     * @param string message
     * @param array arguments
     * @param bool save to file
     * 
     * @return string log message
     */
    protected function log(string $level, string $message, array $args, bool $forceSave): string {
        $message = $this->createMessage($level, $message, $args);
        if($forceSave || CFG::LOG_SAVE[$level]) {
            return $this->saveToFile(CFG::LOG_SAVE_PATH[$level], $message);
        }
        return $message;
    }

    /**
     * 
     * User interface methods
     * 
     */

    public function debug(string $message, array $args = [], bool $forceSave = false): string {
        return $this->log(CFG::DEBUG, $message, $args, $forceSave);
    }

    public function info(string $message, array $args = [], bool $forceSave = false): string {
        return $this->log(CFG::INFO, $message, $args, $forceSave);
    }

    public function notice(string $message, array $args = [], bool $forceSave = false): string {
        return $this->log(CFG::NOTICE, $message, $args, $forceSave);
    }

    public function warning(string $message, array $args = [], bool $forceSave = false): string {
        return $this->log(CFG::WARNING, $message, $args, $forceSave);
    }

    public function error(string $message, array $args = [], bool $forceSave = false): string {
        return $this->log(CFG::ERROR, $message, $args, $forceSave);
    }

    public function critical(string $message, array $args = [], bool $forceSave = false): string {
        return $this->log(CFG::CRITICAL, $message, $args, $forceSave);
    }
}