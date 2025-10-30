<?php

namespace FWK\Core\Controllers;

use FWK\Twig\TwigLoader;
use FWK\Core\Resources\Language;
use FWK\Core\Resources\Response;
use FWK\Core\Exceptions\CommerceException;
use SDK\Dtos\Common\Route;
use SDK\Application;
use SDK\Core\Resources\Timer;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Services\BatchService;
use FWK\Core\FilterInput\FilterInputHandler;
use FWK\Core\FilterInput\FilterInputFactory;
use FWK\Enums\ControllerData;
use FWK\Core\Theme\Theme;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use FWK\Core\ViewHelpers\ViewHelpers;
use FWK\Core\Resources\CacheControl;
use SDK\Core\Resources\Cookie;
use SDK\Core\Resources\VarnishManagement;
use FWK\Core\Resources\SeoItems;
use FWK\Core\Resources\RoutePaths;
use FWK\Core\Resources\DateTimeFormatter;
use FWK\Enums\Parameters;
use FWK\Twig\TwigLoaderInterface;
use SDK\Core\Exceptions\InvalidParameterException;

/**
 * This is the Controller main class.
 *
 * The Controller is the component of our MVC that is responsible to obtain and calculate
 * all the required data for the request and prepare it in the adequate format to be sent
 * to Twig (Twig is a template engine, the component of our MVC that is in charge of rendering the response).
 * 
 * @see Controller::run()
 * @see Controller::getGlobalDataKeys()
 * @see Controller::getRequestParams()
 * @see Controller::getRequestParam()
 * 
 * @loggedInRequired: protected bool $loggedInRequired. Default value false, sets true for return a forbidden request if the user is not logged in.
 * @salesAgentRequired: protected bool $salesAgentRequired. Default value false, sets true for return a forbidden request if the user is not a sales agent.
 * @companyAccountsRequired: protected bool $companyAccountsRequired. Default value false, sets true for return a forbidden request if the user is not a company account.
 * @simulatedUserForbbiden: protected bool $simulatedUserForbbiden. Default value false, sets true for return a forbidden request if the user is a simulated user.
 * 
 * @getAllSalesAgentCustomers: protected bool $getAllSalesAgentCustomers. Default value true, sets false for get paginated brands
 * 
 * 
 * @abstract
 *
 * @originParams: \FWK\Core\FilterInput\FilterInputFactory::getPluginModuleCodeParameter()
 *
 * @package FWK\Core\Controllers
 */
abstract class Controller {

    private Route $route;

    private array $data = [];

    private ?BatchRequests $apiBatchRequest = null;

    private const DATA_RESERVED_KEYS = [
        self::CONTROLLER_ITEM,
        ControllerData::COMMERCE_DATE_TIME,
        ControllerData::CONTENT,
        ControllerData::CORE_MODE,
        ControllerData::LANGUAGE_SHEET,
        ControllerData::LAYOUT,
        ControllerData::MACROS_CORE,
        ControllerData::REQUEST_PARAMETERS,
        ControllerData::ROUTE_PATHS,
        ControllerData::ROUTE,
        ControllerData::SEO_ITEMS,
        ControllerData::SERVER_TIME,
        ControllerData::SESSION,
        ControllerData::SETTINGS,
        ControllerData::THEME_CONFIGURATION,
        ControllerData::TIMER,
        ControllerData::VERSION,
        ControllerData::VIEW_HELPERS,
        ControllerData::CACHE_HASH,
    ];

    /**
     * $route will contains the language retuned from the API in route['language']<br>
     * Default value 'en'
     *
     * @var Language
     */
    protected Language $language;

    /**
     * This attribute references the TwigLoader instance of the controller,
     * this is the component of our MVC that is in charge of rendering the response.
     *
     * @var TwigLoaderInterface
     */
    protected ?TwigLoaderInterface $twig = null;

    private array $apiBatchResult = [];

    private array $themeBatchKeys = [];

    protected ?\DateTime $commerceDateTime = null;

    protected ?\IntlCalendar $commerceCalendar = null;

    /**
     * This attribute contains the parameters of the request (GET or POST parameters)
     */
    protected array $requestParams = [];

    /**
     * This attribute define if the controller required a logged in session
     */
    protected bool $loggedInRequired = false;

    /**
     * This attribute define if the controller required a sales Agent in session
     */
    protected bool $salesAgentRequired = false;

    /**
     * This attribute define if the controller required a company account in session
     */
    protected bool $companyAccountsRequired = false;

    /**
     * This attribute define if the controller simulated user
     */
    protected bool $simulatedUserForbbiden = false;

    /**
     * This is the name of the main controller item, for example,
     * if your request is over a productController, the itemController has the product that referer the url.
     */
    public const CONTROLLER_ITEM = 'controllerItem';

    /**
     * This method is the one in charge of defining base functions that are needed
     * for the Twig template and adding them to the TwigLoader given by parameter.
     *
     * @abstract
     *
     * @param TwigLoader $twig
     *
     * @return void
     */
    abstract protected function addTwigBaseFunctions(TwigLoader $twig);

    /**
     * This method is the one in charge of defining base extensions that are needed
     * for the Twig template and adding them to the TwigLoader given by parameter.
     *
     * @abstract
     *
     * @param TwigLoader $twig
     *
     * @return void
     */
    abstract protected function addTwigBaseExtensions(TwigLoader $twig);

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @abstract
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    abstract protected function setControllerBaseBatchData(BatchRequests $requests): void;

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     *
     * @abstract
     */
    abstract protected function setControllerBaseData(): void;

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @abstract
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    abstract protected function setBatchData(BatchRequests $request): void;

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @abstract
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    abstract protected function setData(array $additionalData = []): void;

    /**
     * This method sets the response type when execute self::run()
     *
     * @abstract
     *
     * @return void
     *
     * @see self::run()
     */
    abstract protected function setType(): void;


    /**
     * This method validate if the session is logged in
     *
     * @abstract
     *
     * @return void
     */
    abstract protected function validateLoggedIn(): void;

    /**
     * This method validate if the session is sales agent
     *
     * @abstract
     *
     * @return void
     */
    abstract protected function validateSalesAgent(): void;

    /**
     * This method validate if the session is a company account
     *
     * @abstract
     *
     * @return void
     */
    abstract protected function validateCompanyAccounts(): void;

    /**
     * This method validate if the session is a simulated user
     *
     * @abstract
     *
     * @return void
     */
    abstract protected function forbiddenSimulatedUser(): void;

    protected static ?Theme $theme = null;

    protected static function getTheme(): Theme {
        if (is_null(self::$theme)) {
            self::$theme = Theme::getInstance();
        }
        return self::$theme;
    }

    /**
     * Set controller route from Route sends to the constructor. By default unset filterOption parameter from change language path because this filter e
     *
     * @param Route $route
     */
    protected function setRoute(Route $route): void {
        $parameters = Utils::getQueryStringParameters();
        unset($parameters[Parameters::PATH]);
        foreach ($parameters as $name => $values) {
            if (strpos($name, Parameters::FILTER_OPTION) === 0) {
                unset($parameters[$name]);
            }
        }
        $pathParameters = Utils::parseArrayToPathParameters($parameters);
        if (strlen($pathParameters)) {
            foreach ($route->getAvailableLanguages() as $availableLanguage) {
                $availableLanguage->setUrl($availableLanguage->getUrl() . $pathParameters);
            }
        }
        $this->route = $route;
    }

    protected function alterTheme(): void {
    }

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route) {
        Utils::addTimerDebugFlag('Controller-constructor', Timer::START_SUFFIX);
        Response::addController(static::class);
        Utils::addTimerDebugTag('controller', static::class);
        $this->setRoute($route);
        Theme::reloadInstance($this->route);
        $this->alterTheme();
        Controller::$theme = Theme::getInstance();
        CacheControl::reloadInstance($this->route->getType());

        $this->setType();

        $this->requestParams = $this->getFilterRequestParams();

        if ($this->loggedInRequired) {
            $this->validateLoggedIn();
        }

        if ($this->salesAgentRequired) {
            $this->validateSalesAgent();
        }

        if ($this->companyAccountsRequired) {
            $this->validateCompanyAccounts();
        }

        if ($this->simulatedUserForbbiden) {
            $this->forbiddenSimulatedUser();
        }

        $themeInst = self::getTheme();
        if ($this->isForbidden()) {
            $themeInst->runForbiddenResponse();
        }

        DateTimeFormatter::init(Utils::calculateLocale($this->route->getCountry(), $this->route->getLanguage()));
        $this->commerceDateTime = new \DateTime('now', DateTimeFormatter::getTimezone());
        $this->commerceCalendar = DateTimeFormatter::getCalendar();
        $this->language = Language::getInstance();
        $this->apiBatchRequest = new BatchRequests();
        $themeInst->addBatchRequests($this->apiBatchRequest);
        $this->themeBatchKeys = array_column($this->apiBatchRequest->getRequests()['requests'], 'requestId');
        Utils::addTimerDebugFlag('Controller-constructor', Timer::END_SUFFIX);
    }

    /**
     * This method returns if the request should be run as a forbidden request.
     *
     * @return bool
     */
    protected function isForbidden(): bool {
        return ($this->route->getStatus() === 403);
    }

    private function getFilterRequestParams(): array {
        return array_merge(FilterInputHandler::getFilterFilterInputs(self::getOriginParams(), self::getFilterParams()), FilterInputHandler::getFilterFilterInputs($this->getOriginParams(), $this->getFilterParams()));
    }

    /**
     * This method returns an array of the params indicating in each node the param name, and the filter to apply.
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     */
    protected function getFilterParams(): array {
        return FilterInputFactory::getUrlParameter();
    }

    /**
     * This method returns the origin of the params (see FilterInputHandler::PARAMS_FROM_GET, FilterInputHandler::PARAMS_FROM_QUERY_STRING or FilterInputHandler::PARAMS_FROM_POST,...).
     * This function must be override in extended controllers to add new parameters to self::requestParams
     *
     * @return mixed
     *
     * @see FilterInputHandler
     */
    protected function getOriginParams() {
        return FilterInputHandler::PARAMS_FROM_GET;
    }

    /**
     * This method sets the the response header when execute self::run().
     * By default sets 'Access-Control-Allow-Origin:' to the host commerce
     *
     * @abstract
     *
     * @return void
     *
     * @see self::run()
     */
    protected function addResponseHeaders(): void {
        Response::addHeader('Access-Control-Allow-Origin: ' . Application::getInstance()->getEcommerceSettings()->getGeneralSettings()->getStoreURL());
    }

    /**
     * This method executes the controller following these steps:
     * <ol>
     * <li>Get required data</li>
     * <li>Create TwigLoader</li>
     * <li>Render Twig template</li>
     * <li>Output Twig response</li>
     * </ol>
     *
     * @param array $additionalData
     *            with the data to add to the controller data.
     * @param string $header
     *            to be added to the header of the request response (default null)
     *            
     * @return void
     */
    public function run(array $additionalData = [], string $header = null): void {
        Utils::addTimerDebugFlag('Controller-run', Timer::START_SUFFIX);
        $this->fillData($additionalData);
        $this->addResponseHeaders();
        $this->response($this->render(), $header);
        Utils::addTimerDebugFlag('Controller-run', Timer::END_SUFFIX);
    }

    /**
     * This method builds the Twig object that will render the request.
     *
     * @param array $data
     *            -> This data will be merged with the controller.data
     * @param bool $loadCore
     *            -> Set if is required to load the Twig Core Macros functionality
     * @param int $autoescape
     *            -> @see \FWK\Enums\TwigAutoescape
     *            
     * @return TwigLoader
     *
     * @see \FWK\Enums\TwigAutoescape
     */
    protected function setTwig(array $data = [], bool $loadCore = true, int $autoescape = 0): TwigLoader {
        Utils::addTimerDebugFlag('setTwig', Timer::START_SUFFIX);
        if ($this->twig === null) {
            $this->setDefaultData();
            $this->twig = new TwigLoader(self::getTheme());
            $this->twig->load($data + $this->getData(), $autoescape, $loadCore);
            $this->addTwigBaseFunctions($this->twig);
            $this->addTwigBaseExtensions($this->twig);
        }
        Utils::addTimerDebugFlag('setTwig', Timer::END_SUFFIX);
        return $this->twig;
    }

    /**
     * This method sets the controller data that is common in all Twig templates
     *
     */
    private function setDefaultData(): void {
        $this->data +=  $this->getDefaultData();
    }

    /**
     * This method sets the controller data that is common in all Twig templates
     *
     * @return array
     */
    protected function getDefaultData(): array {
        $theme = self::getTheme();
        $data = [];
        $data[ControllerData::CACHE_HASH] = Cookie::get("cache-hash");
        $data[ControllerData::COMMERCE_CALENDAR] = $this->commerceCalendar;
        $data[ControllerData::COMMERCE_DATE_TIME] = $this->commerceDateTime;
        $data[ControllerData::CORE_MODE] = $theme->getMode();
        $data[ControllerData::LANGUAGE_SHEET] = $this->getLanguageSheet();
        $data[ControllerData::REQUEST_PARAMETERS] = $this->getRequestParams();
        $data[ControllerData::ROUTE_PATHS] = RoutePaths::getInstance();
        $data[ControllerData::ROUTE] = $this->route;
        $data[ControllerData::SEO_ITEMS] = $this->getSeoItems();
        $data[ControllerData::SERVER_TIME] = date("Y-m-d H:i:s");
        $data[ControllerData::SESSION] = $this->getSession();
        $data[ControllerData::SETTINGS] = Application::getInstance()->getEcommerceSettings();
        $data[ControllerData::THEME_CONFIGURATION] = $theme->getConfiguration();
        $data[ControllerData::TIMER] = Timer::getTimer('twig');
        $data[ControllerData::VIEW_HELPERS] = new ViewHelpers($this->language, $theme, $this->getSession());
        return $data;
    }

    /**
     * This method returns the global data keys
     *
     * @return array
     */
    final public static function getGlobalDataKeys(): array {
        return [
            ControllerData::CACHE_HASH,
            ControllerData::COMMERCE_CALENDAR,
            ControllerData::COMMERCE_DATE_TIME,
            ControllerData::CORE_MODE,
            ControllerData::LANGUAGE_SHEET,
            ControllerData::REQUEST_PARAMETERS,
            ControllerData::ROUTE_PATHS,
            ControllerData::ROUTE,
            ControllerData::SEO_ITEMS,
            ControllerData::SERVER_TIME,
            ControllerData::SESSION,
            ControllerData::SETTINGS,
            ControllerData::THEME_CONFIGURATION,
            ControllerData::TIMER,
            ControllerData::VIEW_HELPERS
        ];
    }

    /**
     * This method returns the metatags value
     *
     * @return SeoItems|NULL
     */
    protected function getSeoItems(): ?SeoItems {
        return null;
    }

    /**
     * This method returns the session value, if the request is cacheable returns null
     *
     * @return Session|NULL
     */
    protected function getSession(): ?Session {
        return Session::getInstance();
    }

    /**
     * This method sets the base variable to use in all Twig template
     *
     * @return array
     */
    private function getData(): array {
        return $this->data;
    }

    /**
     * This method initialize applied parameters, runs previously to run preSendControllerBaseBatchData
     *
     */
    protected function initializeAppliedParameters(): void {
    }

    /**
     * This method runs previously to run ControllerBaseBatchData
     *
     */
    protected function preSendControllerBaseBatchData(): void {
    }

    /**
     * This method runs previously to run setControllerBaseData
     *
     */
    protected function preSetControllerBaseData(): void {
    }

    /**
     * This method merges all required data into the Controller data array.
     * The order adding data is:
     * <ul>
     * <li>Controller BatchData. @see \FWK\Controllers\Controller::sendBatchData()</li>
     * <li>Controller setData. @see \FWK\Controllers\Controller::setData()</li>
     * <li>$additionalData parameter</li>
     * </ul>
     *
     * @param array $additionalData
     *
     * @return void
     */
    private function fillData(array $additionalData = []): void {
        Utils::addTimerDebugFlag('fillData', Timer::START_SUFFIX);

        $this->initializeAppliedParameters();

        Utils::addTimerDebugFlag('batchData', Timer::START_SUFFIX);
        $this->preSendControllerBaseBatchData();
        $this->apiBatchResult = $this->sendBatchData();
        foreach ($this->apiBatchResult as $key => $value) {
            $this->setDataValue($key, $value);
        }
        Utils::addTimerDebugFlag('batchData', Timer::END_SUFFIX);

        Utils::addTimerDebugFlag('setData', Timer::START_SUFFIX);
        $this->preSetControllerBaseData();
        $this->setControllerBaseData();
        foreach (self::getTheme()->getCalculatedData($this->apiBatchResult) as $key => $value) {
            if (in_array($key, $this->themeBatchKeys, true)) {
                $this->deleteControllerData($key);
            }
            $this->setDataValue($key, $value);
        }
        foreach ($additionalData as $key => $value) {
            $this->setDataValue($key, $value);
        }
        $this->setData();
        Utils::addTimerDebugFlag('setData', Timer::END_SUFFIX);

        Utils::addTimerDebugFlag('fillData', Timer::END_SUFFIX);
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
        $msgError = 'Missing data on the Service response';
        if (is_null($data)) {
            $this->breakControllerProcess($msgError, $onExceptionCode);
        } else if (!is_null($data->getError())) {
            $msgError .= '. Data class: ' . get_class($data) . '. ' . $data->getError()->getMessage();
            $this->breakControllerProcess($msgError, $onExceptionCode);
        }
    }

    /**
     * This method breaks the execution of the controller throwing a CommerceException with the message and code given by parameters.
     *
     * @param string $exceptionMessage
     * @param int $exceptionCode
     *
     * @return void
     *
     * @throws CommerceException
     */
    protected function breakControllerProcess(string $exceptionMessage, string $exceptionCode = CommerceException::CONTROLLER_REDIRECT_ERROR): void {
        throw new CommerceException($exceptionMessage, $exceptionCode);
    }

    /**
     * This private method sends the batch requests of the controller to the SDK to obtain the data and returns the batch result
     *
     * @return array with the result of the batch request
     */
    private function sendBatchData(): array {
        $this->setControllerBaseBatchData($this->apiBatchRequest);
        $this->setBatchData($this->apiBatchRequest);
        $batchResult = [];
        if (!empty($this->apiBatchRequest->getRequests()['requests'])) {
            $batchResult = BatchService::getInstance()->send($this->apiBatchRequest);
            if (isset($batchResult[self::CONTROLLER_ITEM])) {
                $this->checkCriticalServiceLoaded($batchResult[self::CONTROLLER_ITEM]);
            }
        }
        return $batchResult;
    }

    /**
     * This method returns the value from controller data by key value, if the key doesn't exist it returns null
     *
     * @param string $key
     *
     * @return mixed|NULL
     */
    protected function &getControllerData(string $key): mixed {
        $response = null;
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return $response;
    }

    /**
     * This method delete the value from controller data by key value, if the key doesn't exist it returns false
     *
     * @param string $key
     *
     * @return bool
     */
    protected function deleteControllerData(string $key): bool {
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * This method returns the route of this controller
     *
     * @return Route
     */
    protected function getRoute(): Route {
        return $this->route;
    }

    /**
     * This method sets a key->value into de controller data.
     * <ul>
     * <li>If the key that you try to insert is a self::DATA_RESERVED_KEYS then it throws an exception with code CommerceException::CODE_CONTROLLER_SET_DATA_VALUE_RESERVED_KEY</li>
     * <li>If the key already exists then it throws an exception with code CommerceException::CODE_CONTROLLER_SET_DATA_VALUE_ALREADY_DEFINED_KEY</li>
     * </ul>
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     *
     * @throws CommerceException
     */
    protected function setDataValue(string $key, mixed $value): void {
        if (isset(self::DATA_RESERVED_KEYS[$key])) {
            throw new CommerceException("The key '" . $key . "' is a reserved key, please check Controller::DATA_RESERVED_KEYS", CommerceException::CONTROLLER_SET_DATA_VALUE_KEY_RESERVED);
        } else {
            if (isset($this->data[$key])) {
                throw new CommerceException("The key '" . $key . "' is already defined in this application", CommerceException::CONTROLLER_SET_DATA_VALUE_KEY_ALREADY_DEFINED);
            }
            $this->data[$key] = $value;
        }
    }

    /**
     * This method returns the language sheet of the controller's language
     *
     * @return array
     */
    protected function getLanguageSheet(): array {
        return $this->language->getLanguageSheet();
    }

    /**
     * This method executes the renderization of the page and returns this renderization.
     *
     * @param string $content
     *            -> If different to null, it overrides the theme content to load in Twig
     * @param string $layout
     *            -> If different to null, it overrides the theme layout to load in Twig
     * @param string $format
     *            -> Default value: 'html'. Set the format to mark the name of the layout. For example, with "json" value, Twig render going to load default.json.twig
     *            
     * @return string containing the renderization of the page.
     */
    protected function render(string $content = null, string $layout = null, string $format = 'html'): string {
        $this->setTwig();
        return $this->twig->render($content, $layout, $format);
    }

    /**
     * This method generates the request response
     *
     * @param string $output
     *            is the render of the page to be set to the response.
     * @param string $header
     *            is a header to be added to the response.
     *            
     * @return void
     */
    final protected function response(string $output, string $header = null): void {
        $cacheable = $this->isCacheable();
        Utils::addTimerDebugFlag('response', Timer::START_SUFFIX);
        if (self::getTheme()->getName() !== INTERNAL_THEME) {
            Cookie::set('version', self::getTheme()->getVersion());
        }
        if ($this->getSession() !== null) {
            Cookie::set('logged', Utils::isSessionLoggedIn($this->getSession()) ? 1 : 0);
        }
        if (!$cacheable) {
            Cookie::set('folcsVersion', Utils::getFolcsVersion($cacheable));
        }
        Response::addCacheHeaders($cacheable);
        Response::addHeader($header);
        VarnishManagement::send(Response::getStructHeaders(), $cacheable);
        Response::output($output);
        Utils::addTimerDebugFlag('response', Timer::END_SUFFIX);
    }

    /**
     * Returns if the requests is cacheable
     *
     * @return bool
     */
    protected function isCacheable(): bool {
        return CacheControl::getInstance()->isCacheable(Cookie::get('cache-hash'));
    }

    /**
     * Returns a array with de filtered parameters
     *
     * @return array
     */
    public function getRequestParams(): array {
        return $this->requestParams;
    }

    /**
     * Returns the value of a filtered request parameter.
     *
     * @param string $parameter
     * @param bool $required
     *            Sets if the request paramenter is required.
     * @param mixed $default
     *
     * @throws CommerceException
     *
     * @return mixed|null
     */
    public function getRequestParam(string $parameter, bool $required = false, $default = null) {
        if (isset($this->getRequestParams()[$parameter])) {
            return $this->getRequestParams()[$parameter];
        } else {
            if ($required) {
                throw new InvalidParameterException("The parameter '" . $parameter . "' is required", CommerceException::CONTROLLER_UNDEFINED_REQUIRED_PARAMETER);
            }
            return $default;
        }
    }
}
