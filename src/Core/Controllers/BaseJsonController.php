<?php

namespace FWK\Core\Controllers;

use FWK\Twig\TwigLoader;
use FWK\Core\Resources\Response;
use FWK\Core\Resources\Loader;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\Utils;
use FWK\Enums\TwigContentTypes;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\Environment;
use FWK\Core\Theme\Theme;
use FWK\Enums\RouteType;
use SDK\Core\Exceptions\InvalidParameterException;

/**
 * This is the base controller for all the json controllers.
 *
 * This class extends Controller, see this class.
 *
 * @controllerData: 
 *  <p>self::RESPONSE: array</p>
 *  <p>self::RESPONSE[self::STATUS] => array</p>
 *  <p>self::RESPONSE[self::STATUS][self::CODE] => int status code</p>
 *  <p>self::RESPONSE[self::STATUS][self::MESSAGE] => string status message</p>
 * 
 * @responseMessageSuccess = 'Ok';
 * @responseMessageError = 'Error';
 * 
 * @twigContent: \themes\Internal\Content\Json\default.json.twig
 * 
 * @abstract
 *
 * @see BaseJsonController::run()
 * 
 * @see Controller
 *
 * @package FWK\Core\Controllers
 */
abstract class BaseJsonController extends Controller {

    protected const STATUS = 'status';

    protected const CODE = 'code';

    protected const MESSAGE = 'message';

    protected const SUCCESS = 'success';

    protected const RESPONSE = 'response';

    protected const REQUEST_ID = 'requestId';

    protected const FIELDS = 'fields';

    public const DATA = 'data';

    protected array $appliedParameters = [];

    protected string $responseMessage = 'Ok';

    protected string $responseMessageError = 'Error';

    /**
     * This method launches the adequate actions against the SDK (through the FWK services) and returns the response data. 
     *
     * @abstract
     *
     * @return Element
     */
    abstract protected function getResponseData(): ?Element;

    protected function alterTheme(): void {
        $theme = Theme::getInstance();
        $theme->setName(INTERNAL_THEME);
        $theme->setVersion('');
    }

    /**
     * 
     * 
     * @see \FWK\Core\Controllers\Controller::addResponseHeaders()
     */
    protected function addResponseHeaders(): void {
        parent::addResponseHeaders();
        Response::addHeader('Content-Type: application/json');
    }

    /**
     * @see \FWK\Core\Controllers\Controller::setType()
     */
    protected function setType(array $additionalData = [], string $header = null): void {
        Response::setType(Response::TYPE_JSON);
    }

    /**
     * This method parses the given Element and returns it.
     * 
     * @param Element $response
     * 
     * @return \SDK\Core\Dtos\Element
     */
    protected function parseResponseData(Element $response) {
        return $response;
    }

    /**
     * This method returns an array containing the status information, based on the given parameters.
     *
     * Format of the returned array:
     * <ul>
     * <li>Position self::CODE => contains the given code</li>
     * <li>Position self::MESSAGE => contains the given message</li>
     * </ul>
     *
     * @param int $code
     * @param string $message
     *
     * @return array
     */
    protected function getStatus(int $code, string $message): array {
        if ($code == 503 && !DEVEL_HEADER) {
            $message = 'Internal error';
        }
        return [
            self::CODE => $code,
            self::MESSAGE => $message
        ];
    }

    private function getErrorResponse(int $code, string $message, array $fields = []): array {
        return [
            self::STATUS => $this->getStatus($code, $message),
            self::DATA => [
                self::RESPONSE => $this->getResponseError($fields),
                self::REQUEST_ID => REQUEST_ID
            ]
        ];
    }

    /**
     * This method returns an array containing the response information.
     *
     * Format of the returned array:
     * <ul>
     * <li>Position self::SUCCESS => contains 1</li>
     * <li>Position self::MESSAGE => contains the message response</li>
     * </ul>
     *
     * @return array
     */
    protected function getResponse(): array {
        return [
            self::SUCCESS => 1,
            self::MESSAGE => $this->responseMessage
        ];
    }

    /**
     * This method returns an array containing the response error information.
     *
     * Format of the returned array:
     * <ul>
     * <li>Position self::SUCCESS => contains 0</li>
     * <li>Position self::MESSAGE => contains the error message response</li>
     * </ul>
     *
     * @return array
     */
    protected function getResponseError(array $fields): array {
        $arrayFields = [];
        foreach ($fields as $field) {
            $arrayFields[] = $field->toArray();
        }
        return [
            self::SUCCESS => 0,
            self::MESSAGE => $this->responseMessageError,
            self::FIELDS => $arrayFields
        ];
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
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    final protected function setControllerBaseData(): void {
        $response = [];
        $response[self::DATA] = [];
        try {
            $request = $this->getResponseData();
            if ($request === null) {
                $response = $this->getErrorResponse(503, 'Unavailable api response');
            } elseif ($request->getError() === null) {
                $response = [
                    self::STATUS => $this->getStatus(200, 'Ok'),
                    self::DATA => [
                        self::RESPONSE => $this->getResponse(),
                        self::DATA => $this->parseResponseData($request)
                    ]
                ];
            } else {
                throw new CommerceException(self::class . '. Controller Json error: ' . $request->getError()->getStatusMessage() . '. ' . $request->getError()->getMessage(), CommerceException::BASE_JSON_CONTROLLER_SET_DATA_ERROR, null, $request->getError());
            }
        } catch (InvalidParameterException $e) {
            $response = $this->getErrorResponse(400, $e->getMessage(),  $e->getError()->getFields());
        } catch (CommerceException $e) {
            $response = $this->getErrorResponse(503, $e->getMessage(),  $e->getError()->getFields());
        } catch (\Error | \Exception $e) {
            $response = $this->getErrorResponse(503, 'Error code:' . $e->getCode() . '. ' . $e->getMessage() . '. File:' . $e->getFile() . '. Line:' . $e->getLine());
        }
        if (Environment::get('DEVEL')) {
            $response[self::DATA]['appliedParameters'] = $this->appliedParameters;
        }
        $this->setDataValue(self::RESPONSE, $response);
    }

    /**
     *
     * @see \FWK\Core\Controllers\Controller::addTwigBaseFunctions()
     */
    protected function addTwigBaseFunctions(TwigLoader $twig) {
        Loader::twigFunctions(TwigContentTypes::JSON)->addFunctions($twig->getTwigEnvironment());
    }

    /**
     * 
     * 
     * 
     * @see \FWK\Core\Controllers\Controller::addTwigBaseExtensions()
     */
    protected function addTwigBaseExtensions(TwigLoader $twig) {
        Loader::twigExtensions(TwigContentTypes::JSON)->addExtensions($twig->getTwigEnvironment());
    }

    /**
     * 
     * 
     * 
     * @see \FWK\Core\Controllers\Controller::setTwig()
     */
    protected function setTwig(array $data = [], bool $loadCore = true, int $autoescape = 0): TwigLoader {
        return parent::setTwig([], false, $autoescape);
    }

    /**
     * 
     * 
     * 
     * Override content to 'Content/Json/default.json.twig', and set format to 'json' 
     * 
     * @see \FWK\Core\Controllers\Controller::render()
     */
    protected function render(String $content = null, String $layout = null, String $format = 'html'): string {
        return parent::render('Content/' . ucfirst(TwigContentTypes::JSON) . '/default.' . TwigContentTypes::JSON . '.twig', null, TwigContentTypes::JSON);
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
