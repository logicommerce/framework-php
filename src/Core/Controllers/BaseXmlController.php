<?php

namespace FWK\Core\Controllers;

use FWK\Core\Resources\Response;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Core\Theme\Theme;
use FWK\Enums\RouteType;
use FWK\Enums\TwigContentTypes;
use FWK\Twig\TwigLoader;

/**
 * This is the base controller for all the PDF controllers.
 *
 * This class extends Controller, see this class.
 *
 * @abstract
 *
 * @responseType: XML
 * 
 * @twigContent: \themes\Internal\Content\Xml\default.xml.twig
 * 
 * @see BaseXmlController::run()
 *
 * @see Controller
 *
 * @package FWK\Core\Controllers
 */
abstract class BaseXmlController extends Controller {

    protected function alterTheme(): void {
        $theme = Theme::getInstance();
        $theme->setName(INTERNAL_THEME);
        $theme->setVersion('');
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
    }

    /**
     * @see \FWK\Core\Controllers\Controller::addResponseHeaders()
     */
    protected function addResponseHeaders(): void {
        parent::addResponseHeaders();
        Response::addHeader('Content-type: ' . Response::MIME_TYPE_XML);
    }

    /**
     * @see \FWK\Core\Controllers\Controller::setType()
     */
    protected function setType(array $additionalData = [], string $header = null): void {
        Response::setType(Response::TYPE_XML);
    }

    /**
     * @see \FWK\Core\Controllers\Controller::addTwigBaseFunctions()
     */
    protected function addTwigBaseFunctions(TwigLoader $twig) {
        Loader::twigFunctions(TwigContentTypes::XML)->addFunctions($twig->getTwigEnvironment());
    }

    /**
     * 
     * 
     * 
     * @see \FWK\Core\Controllers\Controller::addTwigBaseExtensions()
     */
    protected function addTwigBaseExtensions(TwigLoader $twig) {
        Loader::twigExtensions(TwigContentTypes::XML)->addExtensions($twig->getTwigEnvironment());
    }

    /**
     *
     * @see \FWK\Core\Controllers\Controller::setTwig()
     */
    protected function setTwig(array $data = [], bool $loadCore = true, int $autoescape = 0): TwigLoader {
        return parent::setTwig([], false, $autoescape);
    }

    /**
     *
     *  Override content to 'Content/Xml/default.xml.twig', and set format to 'xml'
     *
     * @see \FWK\Core\Controllers\Controller::render()
     */
    protected function render(String $content = null, String $layout = null, String $format = 'html'): string {
        return parent::render('Content/' . ucfirst(TwigContentTypes::XML) . '/default.' . TwigContentTypes::XML . '.twig', null, TwigContentTypes::XML);
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
