<?php

namespace FWK\Core\Resources\Loggers;

use SDK\Core\Resources\Logger\LoggerAdapter;

/**
 * This is the DebugInfoLogger class, it manages the debug info logs.
 * This class extends LoggerAdapter (SDK\Core\Resources\Logger\LoggerAdapter), see this class.
 * <br>The required parameters to generate a DebugInfoLogger are defined in the constant: DebugInfoLogger::REQUIRED_PARAMS.
 *
 * @see LoggerAdapter
 * 
 * @see DebugInfoLogger::REQUIRED_PARAMS
 * @see DebugInfoLogger::ERROR_CAPTURER_LAST_ERROR
 * @see DebugInfoLogger::ERROR_CAPTURER_GENERATE_ERROR
 * @see DebugInfoLogger::EXCEPTION_HANDLER_ERROR
 * @see DebugInfoLogger::FWK_EXCEPTION_ERROR
 * 
 * @see DebugInfoLogger::getLoggerName()
 * @see DebugInfoLogger::getLogName()
 *
 * @package FWK\Core\Resources\Loggers
 */
final class DebugInfoLogger extends LoggerAdapter {

    /**
     * Required parameters to generate a DebugInfoLogger log.
     */
    protected const REQUIRED_PARAMS = ['code', 'data'];

    public const ERROR_CAPTURER_LAST_ERROR = 'S110C00001';

    public const ERROR_CAPTURER_GENERATE_ERROR = 'S110C00002';

    public const EXCEPTION_HANDLER_ERROR = 'S110C00003';

    public const FWK_EXCEPTION_ERROR = 'S110C00004';

    /**
     * It returns the name of the logger for a DebugInfoLogger.
     * 
     * 
     * @see \SDK\Core\Resources\Logger\LoggerAdapter::getLoggerName()
     */
    protected function getLoggerName(): string {
        return 'debug_info_log';
    }

    /**
     * It returns the name of the log for a DebugInfoLogger.
     * 
     * 
     * @see \SDK\Core\Resources\Logger\LoggerAdapter::getLogName()
     */
    protected function getLogName(): string {
        return 'debug_info';
    }
}
