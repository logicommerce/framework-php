<?php

namespace FWK\Core\Controllers;

use FWK\Core\Resources\Response;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Enums\RouteType;
use FWK\Enums\TwigContentTypes;
use FWK\Twig\TwigLoader;

/**
 * This is the base controller for all the js controllers.
 *
 * This class extends Controller, see this class.
 *
 * @abstract
 * 
 * @responseType: JavaScript
 *
 * @see BaseJsController::run()
 * 
 * @see Controller
 *
 * @package FWK\Core\Controllers
 */
abstract class BaseJsController extends Controller {

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
    }

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
        Response::setType(Response::TYPE_JS);
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
        return parent::render(null, null, TwigContentTypes::JS);
    }

    /**
     * This method validate if the session is logged in. Else generate a forbidden response
     *
     * @return void
     */
    protected function validateLoggedIn(): void {
        if (!Utils::isSessionLoggedIn($this->getSession())) {
            Response::forbidden();
        }
    }

    /**
     * This method validate if the session is sales agent. Else generate a forbidden response
     *
     * @return void
     */
    protected function validateSalesAgent(): void {
        if (!Utils::isSalesAgent($this->getSession())) {
            Response::forbidden();
        }
    }

    /**
     * This method validate if the company accounts are enabled. Else Redirect to User path
     *
     * @return void
     */
    protected function validateCompanyAccounts(): void {
        if (!Utils::isCompanyAccounts()) {
            Response::redirect(RoutePaths::getPath(RouteType::USER));
        }
    }

    /**
     * This method launch forbidden response if the user is simulated. Else generate a forbidden response
     *
     * @return void
     */
    protected function forbiddenSimulatedUser(): void {
        if (Utils::isSimulatedUser($this->getSession())) {
            Response::forbidden();
        }
    }
}
