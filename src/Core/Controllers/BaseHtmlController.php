<?php

namespace FWK\Core\Controllers;

use FWK\Twig\TwigLoader;
use FWK\Core\Resources\Loader;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\SeoItems;
use FWK\Core\Resources\Metatag;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Enums\RouteType;
use FWK\Enums\TwigContentTypes;

/**
 * This is the base controller for all the html controllers. 
 * 
 * This class extends Controller, see this class.
 * 
 * @responseType: HTML
 * 
 * @abstract
 * 
 * @see Controller
 * 
 * @package FWK\Core\Controllers
 */
abstract class BaseHtmlController extends Controller {

    /**
     * 
     * 
     */
    protected function addTwigBaseFunctions(TwigLoader $twig) {
        Loader::twigFunctions(TwigContentTypes::HTML)->addFunctions($twig->getTwigEnvironment());
    }

    /**
     * 
     * 
     */
    protected function addTwigBaseExtensions(TwigLoader $twig) {
        Loader::twigExtensions(TwigContentTypes::HTML)->addExtensions($twig->getTwigEnvironment());
    }

    /**
     * @see \FWK\Core\Controllers\Controller::setType()
     */
    protected function setType(array $additionalData = [], string $header = null): void {
        Response::setType(Response::TYPE_HTML);
    }

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
     * @see \FWK\Core\Controllers\Controller::getSeoItems()
     */
    protected function getSeoItems(): ?SeoItems {
        $seoItems = new SeoItems($this->getRoute(), $this->language);
        if (strstr($this->getRoute()->getType(), 'BLOG')) {
            $seoItems->addMetatag((new Metatag())->setName(SeoItems::AUTHOR)->setContent($this->getRoute()->getCanonical()));
        }

        return $seoItems;
    }

    /**
     * This method validate if the session is logged in. Else Redirect to User path
     *
     * @return void
     */
    protected function validateLoggedIn(): void {
        if (!Utils::isSessionLoggedIn($this->getSession())) {
            Response::redirect(RoutePaths::getPath(RouteType::USER));
        }
    }

    /**
     * This method validate if the session is sales agent. Else Redirect to User path
     *
     * @return void
     */
    protected function validateSalesAgent(): void {
        if (!Utils::isSalesAgent($this->getSession())) {
            Response::redirect(RoutePaths::getPath(RouteType::USER));
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
     * This method launch forbidden response if the user is simulated. Else Redirect to User path
     *
     * @return void
     */
    protected function forbiddenSimulatedUser(): void {
        if (Utils::isSimulatedUser($this->getSession())) {
            Response::redirect(RoutePaths::getPath(RouteType::USER));
        }
    }
}
