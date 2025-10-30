<?php

namespace FWK\Controllers;

use FWK\Core\Controllers\Controller;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Enums\Parameters;
use FWK\Twig\TwigLoader;
use SDK\Core\Resources\Server;
use FWK\Core\Theme\Theme;
use FWK\Enums\RouteType;

/**
 * This is the FeedController class.
 * This class extends Controller, see this class.
 *
 * @responseType: Feed type
 * 
 * @controllerData: self::CONTROLLER_ITEM: \SDK\Dtos\Common\DataFile
 * 
 * @twigContent: \themes\{{themeName}}\{{version}}\Content\Feed\default.html.twig
 * 
 * @RouteType: \SDK\Enums\RouteType::FEED
 * 
 * @see BaseHtmlController
 *
 * @package FWK\Controllers
 */
class FeedController extends Controller {

    private string $type = '';

    private string $mimeType = '';

    private string $fileName = '';

    protected function alterTheme(): void {
        $theme = Theme::getInstance();
        $theme->setName(INTERNAL_THEME);
        $theme->setVersion('');
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getFeedParameters();
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        $hash = $this->getRequestParam(Parameters::ID, false);
        if (is_null($hash)) {
            $hash = $this->getRequestParam(Parameters::HASH, true);
        }
        Loader::service(Services::DATA_FEED)->addGetDataFeed($requests, self::CONTROLLER_ITEM, $hash, $this->getRequestParam(Parameters::LANGUAGE_CODE, true));
    }

    /**
     * This method checks if the data required for the correct run of the controller has a correct value, in opposite, it breaks the execution of the controller
     *
     * @param Object $data
     *            is the data required for the correct run of the controller
     * @param string $onExceptionCode
     *            is the Exception to be thrown if the check is ko.
     *            
     * @return void
     */
    protected function checkCriticalServiceLoaded(?Object $data, string $onExceptionCode = CommerceException::CONTROLLER_UNDEFINED_CRITICAL_DATA): void {
        if (!is_null($data->getHttpStatus()) && $data->getHttpStatus()->getCode() === 202) {
            Response::addHeader(Server::get('SERVER_PROTOCOL') . ' 202 ' . $data->getHttpStatus()->getMessage());
        } else if (is_null($data)) {
            $this->breakControllerProcess('Missing data on the Service response', $onExceptionCode);
        } else if (!is_null($data->getError())) {
            if ($data->getError()->getStatus() === 400) {
                Response::addHeader(Server::get('SERVER_PROTOCOL') . ' 400 ' . $data->getError()->getMessage());
            } else {
                $this->breakControllerProcess('Missing data on the Service response. ' . $data->getError()->getMessage(), $onExceptionCode);
            }
        }
    }

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     */
    protected function setControllerBaseData(): void {
        $datafeed = $this->getControllerData(self::CONTROLLER_ITEM);
        $this->type = constant(Response::class . '::TYPE_' . $datafeed->getType());
        $this->mimeType = constant(Response::class . '::MIME_TYPE_' . $datafeed->getType());
        $this->fileName = $datafeed->getName();
    }

    /**
     *
     * @see \FWK\Core\Controllers\Controller::addResponseHeaders()
     */
    protected function addResponseHeaders(): void {
        parent::addResponseHeaders();
        Response::addHeader('Content-type: ' . $this->mimeType);
        Response::addHeader('Content-Disposition: attachment; filename=' . $this->fileName);
    }

    /**
     *
     * @see \FWK\Core\Controllers\Controller::setType()
     */
    protected function setType(array $additionalData = [], string $header = null): void {
        Response::setType($this->type);
    }

    /**
     *
     * @see \FWK\Core\Controllers\Controller::addTwigBaseFunctions()
     */
    protected function addTwigBaseFunctions(TwigLoader $twig) {
        Loader::twigFunctions(ucfirst($this->type))->addFunctions($twig->getTwigEnvironment());
    }

    /**
     * 
     * 
     * @see \FWK\Core\Controllers\Controller::addTwigBaseExtensions()
     */
    protected function addTwigBaseExtensions(TwigLoader $twig) {
        Loader::twigExtensions(ucfirst($this->type))->addExtensions($twig->getTwigEnvironment());
    }

    /**
     *
     *
     * @see \FWK\Core\Controllers\Controller::setTwig()
     */
    protected function setTwig(array $data = [], bool $loadCore = true, int $autoescape = 0): TwigLoader {
        return parent::setTwig([], false, $autoescape);
    }

    /**
     *
     * Override content to 'Content/Json/default.json.twig', and set format to 'json'
     *
     * @see \FWK\Core\Controllers\Controller::render()
     */
    protected function render(String $content = null, String $layout = null, String $format = 'html'): string {
        return parent::render('Content/Feed/' . ucfirst($this->type) . '/default.' . $this->type . '.twig', null, $this->type);
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
