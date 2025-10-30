<?php

namespace FWK\Core\Controllers;

use FWK\Core\Resources\Response;
use FWK\Core\Resources\Loader;
use FWK\Enums\TwigContentTypes;
use FWK\Twig\TwigLoader;

/**
 * This is the base controller for all the jsonp controllers.
 *
 * This class extends Controller, see this class.
 *
 * @abstract
 * 
 * @responseType: JavaScript
 *
 * @see BaseJsonpController::run()
 * 
 * @see Controller
 *
 * @package FWK\Core\Controllers
 */
abstract class BaseJsonpController extends BaseJsonController {

    public const CALLBACK_FUNCTION = 'callbackFunction';

    /**
     * 
     * 
     * @see \FWK\Core\Controllers\Controller::addResponseHeaders()
     */
    protected function addResponseHeaders(): void {
        parent::addResponseHeaders();
        Response::addHeader('Content-Type: ' . Response::MIME_TYPE_JS . '; charset=' . CHARSET);
    }

    /**
     * 
     * 
     * @see \FWK\Core\Controllers\Controller::setType()
     */
    protected function setType(array $additionalData = [], string $header = null): void {
        Response::setType(Response::TYPE_JSONP);
    }

    /**
     *
     * @see \FWK\Core\Controllers\Controller::addTwigBaseFunctions()
     */
    protected function addTwigBaseFunctions(TwigLoader $twig) {
        Loader::twigFunctions(TwigContentTypes::JS)->addFunctions($twig->getTwigEnvironment());
    }

    /**
     * 
     * 
     * 
     * @see \FWK\Core\Controllers\Controller::addTwigBaseExtensions()
     */
    protected function addTwigBaseExtensions(TwigLoader $twig) {
        Loader::twigExtensions(TwigContentTypes::JS)->addExtensions($twig->getTwigEnvironment());
    }

    /**
     *
     * @see \FWK\Core\Controllers\Controller::render()
     */
    protected function render(String $content = null, String $layout = null, String $format = 'html'): string {
        return Controller::render('Content/' . ucfirst(TwigContentTypes::JSONP) . '/default.' . TwigContentTypes::JSONP . '.twig', null, TwigContentTypes::JSONP);
    }
}
