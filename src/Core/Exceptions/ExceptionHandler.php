<?php

namespace FWK\Core\Exceptions;

use FWK\Core\Resources\Router;
use FWK\Core\Resources\Response;
use SDK\Core\Resources\Loggers\ExceptionLogger;
use FWK\Core\Resources\Loggers\DebugInfoLogger;
use SDK\Core\Exceptions\ConnectionException;
use SDK\Core\Exceptions\InvalidParameterException;
use SDK\Core\Resources\Environment;

/**
 * This is the ExceptionHandler class.
 * The purpose of this class is to manage exceptions.
 * 
 * @example 
 * It is used by our user-defined exception handler function to be called when an uncaught 
 * exception occurs (FWK\Core\Exceptions\ErrorCapturer.php -> exceptionCapturerFWK)
 *
 * @see ExceptionHandler::errorDumper()
 * @see ExceptionHandler::runException()
 * @see ExceptionHandler::error()
 * 
 * @package FWK\Core\Exceptions
 */
class ExceptionHandler {

    private \Throwable $e;

    /**
     * Constructor.
     * 
     * @param \Throwable $e Exception to manage.
     */
    public function __construct(\Throwable $e) {
        $this->e = $e;
    }

    /**
     * This method returns an string with the dump of the Throwable element given by parameter.
     * 
     * @param \Throwable $e
     * 
     * @return string
     */
    public static function errorDumper(\Throwable $e): string {
        if (!Environment::get('DEVEL') || !isset($e->xdebug_message)) {
            return ($e->getMessage() . '. In file:' . $e->getFile() . '. On line:' . $e->getLine());
        } else {
            return "<table>  $e->xdebug_message </table>";
        }
    }

    /**
     * This method outputs the error.
     * The type parameter is used to give an error title to the error when it is another one different from ConnectionException or CommerceException.
     * 
     * @internal
     * Enroutes the request to error controller and executes it.
     * 
     * @param string $type
     */
    private function outputError(string $type) {
        if ($this->e instanceof ConnectionException && ($this->e->getError()->getStatus() >= 400 && $this->e->getError()->getStatus() < 500)) {
            (new Router())->notFound($this->e->getError()->getStatus());
        } elseif ($this->e instanceof CommerceException) {
            if ($this->e->getCode() === CommerceException::LOADER_CONTROLLER_NOT_FOUND) {
                (new Router())->notFound($this->e->getCode());
            } else {
                (new Router())->error([
                    'title' => $this->e->getMessage()
                ] + $this->toArray());
            }
        } elseif ($this->e instanceof InvalidParameterException) {
            if (Environment::get('DEVEL')) {
                (new Router())->error([
                    'title' => $this->e->getMessage()
                ] + $this->toArray());
            } else {
                (new Router())->notFound();
            }
        } else {
            (new Router())->error([
                'title' => $type
            ] + $this->toArray());
        }
    }

    /**
     * This method runs the exception management.
     * <ol>
     * <li>It saves a log of the error.</li>
     * <li>It outputs the error.
     * <br>-If staticRedirect is true then it outputs an static 503 error output calling to FWK\Core\Exceptions\ErrorWithoutController.php
     * <br>-If staticRedirect is false then it enroutes the request to error controller and executes it.
     * </li>
     * </ol>
     * The string type parameter is used to save it in the log, to set a type in a non-static redirect and to concatenate it in the return string.
     * <br>This method also returns an string indicating 'Request uncompleted ' concatenated with the type pased by parameter.
     * FWK\Core\Exceptions\ErrorWithoutController.php This is the static error page for staticRedirect cases.
     * 
     * @param string $type
     * @param bool $staticRedirect 
     * 
     * @return string
     * 
     */
    public function runException(string $type = 'undefined', bool $staticRedirect = false): string {
        $this->logError($type);
        if ($staticRedirect) {
            header('HTTP/1.1 503 Service Temporarily Unavailable');
            if (Environment::get('DEVEL')) {
                echo $this->errorDumper($this->e);
            }
            include_once(ERROR_WITHOUT_CONTROLLER);
        } else {
            callMethod(function (array $args = []) {
                $this->outputError($args['type']);
            }, null, '', [
                'type' => $type
            ], true);
        }

        return 'Request uncompleted ' . $type;
    }

    /**
     * This method generates a log of the error, outputs it only in DEVEL mode, and returns a string indicating "ERROR" if $nestedError is null or "NestedError" otherwise.
     * 
     * @param null|\Throwable nestedError
     * 
     * @return string
     */
    public function error(?\Throwable $nestedError = null): string {
        $body = "Final Error: {$this->e->getMessage()}";
        if (is_null($nestedError)) {
            $title = "ERROR";
        } else {
            $body .= "\nNested Error: {$nestedError->getMessage()}";
            $this->e = $nestedError;
            $title = "NestedError";
        }
        $this->logError($title);
        if (Environment::get('DEVEL')) {
            Response::output($body);
        }
        return $title;
    }

    private function logError($title) {
        try {
            ExceptionLogger::getInstance()->error($title . ' - ' . $this->e->getMessage(), $this->toArray() + [
                'class' => __CLASS__
            ]);
        } catch (\Exception | \Error $e) {
            try {
                DebugInfoLogger::getInstance()->debug($e->getMessage(), [
                    'code' => DebugInfoLogger::EXCEPTION_HANDLER_ERROR,
                    'data' => $e
                ]);
            } catch (\Exception | \Error $e) {
                if (Environment::get('DEVEL')) {
                    echo $this->errorDumper($this->e);
                }
            }
        }
    }

    private function toArray(): array {
        return [
            'message' => $this->e->getMessage(),
            'code' => $this->e->getCode(),
            'file' => $this->e->getFile(),
            'line' => $this->e->getLine(),
            'exception' => self::errorDumper($this->e, true)
        ];
    }
}
