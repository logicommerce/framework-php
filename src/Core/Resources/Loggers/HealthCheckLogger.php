<?php

namespace FWK\Core\Resources\Loggers;

use SDK\Core\Resources\Logger\LoggerAdapter;

/**
 * This is the HealthCheckLogger class, it manages the health check logs.
 * This class extends LoggerAdapter (SDK\Core\Resources\Logger\LoggerAdapter), see this class.
 * <br>The required parameters to generate a HealthCheckLogger are defined in the constant: HealthCheckLogger::REQUIRED_PARAMS.
 *
 * @see LoggerAdapter
 *
 * @see HealthCheckLogger::REQUIRED_PARAMS
 * @see HealthCheckLogger::LOG_HEALTH_CHECK
 *
 * @see HealthCheckLogger::getLoggerName()
 * @see HealthCheckLogger::getLogName()
 *
 * @package FWK\Core\Resources\Loggers
 */
final class HealthCheckLogger extends LoggerAdapter {

    public const LOG_HEALTH_CHECK = 'F210T00001';

    /**
     * Required parameters to generate a HealthCheckLogger log.
     */
    protected const REQUIRED_PARAMS = ['code', 'process', 'steps'];

    /**
     * It returns the name of the logger for a HealthCheckLogger.
     * 
     * 
     * @see \SDK\Core\Resources\Logger\LoggerAdapter::getLoggerName()
     */
    protected function getLoggerName(): string {
        return 'health_check_log';
    }

    /**
     * It returns the name of the log for a HealthCheckLogger.
     * 
     * 
     * @see \SDK\Core\Resources\Logger\LoggerAdapter::getLogName()
     */
    protected function getLogName(): string {
        return 'health_check';
    }
}
