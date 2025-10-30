<?php

use FWK\Core\Exceptions\FatalException;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Resources\Loggers\ExceptionLogger;
use FWK\Core\Exceptions\ExceptionHandler;
use FWK\Core\Resources\Loggers\DebugInfoLogger;
use SDK\Core\Exceptions\InvalidParameterException;
use Twig\Error\Error as TwigError;

/**
 * This is our user-defined exception handler function to be called when an uncaught exception occurs.
 * It instanciates an ExceptionHandler object (see FWK\Core\Exceptions\ExceptionHandler) to manage the captured exception.
 * 
 * @param \Throwable $e
 * 
 * @return void
 * 
 * @see ExceptionHandler
 */
function exceptionCapturerFWK(\Throwable $e): void {
    $errorHandler = new \FWK\Core\Exceptions\ExceptionHandler($e);
    $errorHandler->error();
}

/**
 * This is the function for execution on shutdown.
 * 
 * @return void
 */
function errorCapturerFWK(): void {
    $last_error = error_get_last();

    if (is_null($last_error)) {
        return;
    }

    if (isset($last_error->e)) {
        $message = $last_error->e->getMessage();
    } else {
        $message = $last_error['message'];
    }

    DebugInfoLogger::getInstance()->debug($message, [
        'code' => DebugInfoLogger::ERROR_CAPTURER_LAST_ERROR,
        'data' => $last_error
    ]);

    try {
        ExceptionLogger::getInstance()->error('ERROR CAPTURED - ' . $message, [
            'class' => $last_error['file'] ?? '',
            'message' => $last_error['message'] ?? '',
            'code' => $last_error['code'] ?? ''
        ]);
    } catch (\Exception | \Error $e) {
        DebugInfoLogger::getInstance()->debug($e->getMessage(), [
            'code' => DebugInfoLogger::ERROR_CAPTURER_GENERATE_ERROR,
            'data' => $e
        ]);
    }
}

function callMethod(callable $method, callable $finally = null, string $message = '', array $args = [], bool $staticRedirect = false): bool {
    $onerror = false;
    try {
        $method($args);
    } catch (CommerceException $e) {
        $onerror = true;
        $errorHandler = new ExceptionHandler($e);
        $message = $errorHandler->runException('CommerceException: ' . $message, $staticRedirect);
    } catch (FatalException $e) {
        $onerror = true;
        $errorHandler = new ExceptionHandler($e);
        $message = $errorHandler->runException('FatalException: ' . $message, $staticRedirect);
    } catch (TwigError $e) {
        $onerror = true;
        $errorHandler = new ExceptionHandler($e);
        $message = $errorHandler->runException('TwigException: ' . $message, $staticRedirect);
    } catch (\Exception $e) {
        $onerror = true;
        $errorHandler = new ExceptionHandler($e);
        $message = $errorHandler->runException('GenericException: ' . $message, $staticRedirect);
    } catch (\Error $e) {
        $onerror = true;
        $errorHandler = new ExceptionHandler($e);
        try {
            $message = $errorHandler->runException('ErrorException: ' . $message, $staticRedirect);
        } catch (\Exception | \Error $nestedError) {
            $message = $errorHandler->error($nestedError);
        }
    } finally {
        if ($finally !== null) {
            $finally($message);
        }
    }
    return $onerror;
}

if (defined('DEVEL') && DEVEL) {
    function exception_error_handler($errno, $errstr, $errfile, $errline) {
        if (error_reporting() && strpos($errstr, "Creation of dynamic property") === 0) {
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
    }
    set_error_handler("exception_error_handler");
}

set_exception_handler('exceptionCapturerFWK');
register_shutdown_function('errorCapturerFWK');
