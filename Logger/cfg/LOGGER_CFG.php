<?php

namespace AKTools\Logger;

class LOGGER_CFG {
    // log levels
    public const DEBUG = "debug";
    public const INFO = "info";
    public const NOTICE = "notice";
    public const WARNING = "warning";
    public const ERROR = "error";
    public const CRITICAL = "critical";

    public const DATE_FORMAT = "Y-m-d,H:i:s";

    // saving
    // absolute path to log file
    public const LOG_INFO_FILE = "app/log/info.log";
    // absolute path to log file
    public const LOG_ERROR_FILE = "app/log/error.log";
    public const LOG_SAVE_PATH = [
        self::DEBUG => self::LOG_INFO_FILE,
        self::INFO => self::LOG_INFO_FILE,
        self::NOTICE => self::LOG_INFO_FILE,
        self::WARNING => self::LOG_INFO_FILE,
        self::ERROR => self::LOG_ERROR_FILE,
        self::CRITICAL => self::LOG_ERROR_FILE
    ];
    public const LOG_SAVE = [
        self::DEBUG => false,
        self::INFO => false,
        self::NOTICE => true,
        self::WARNING => true,
        self::ERROR => true,
        self::CRITICAL => true
    ];
}