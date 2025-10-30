<?php

namespace FWK\Core\Resources;

use SDK\Core\Resources\Timer;
use SDK\Core\Resources\ApiRequest;
use SDK\Core\Resources\Cookie;
use SDK\Application;
use SDK\Core\Exceptions\ApiRequestException;
use SDK\Services\Parameters\Groups\Product\ProductsParametersGroup;
use FWK\Enums\Services;
use SDK\Services\Parameters\Groups\Basket\AddProductParametersGroup;
use SDK\Services\Parameters\Groups\Basket\ProductOptionParametersGroup;
use FWK\Enums\Parameters;
use FWK\Core\FilterInput\FilterInput;
use FWK\Core\FilterInput\FilterInputHandler;
use SDK\Core\Resources\Redis;
use FWK\Controllers\Util\Internal\HealthCheckController;
use FWK\Enums\RouteType;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Services\BatchService;
use SDK\Core\Resources\Environment;
use FWK\Core\Resources\Loggers\HealthCheckLogger;
use SDK\Core\Builders\RequestBuilder;
use SDK\Core\Resources\Connection;
use SDK\Core\Resources\Server;
use SDK\Enums\StockType;
use SDK\Services\Parameters\Groups\Basket\ProductOptionValueParametersGroup;

/**
 * This is the HealthCheck class.
 * The purpose of this class is to execute a general health check of the system.
 *
 * @see HealthCheck::run()
 * 
 * @package FWK\Core\Resources  
 */
class HealthCheck {

    const TIMER = 'time_taken';

    const MSG = 'message';

    const DATA = 'data';

    const CONFIGURATION = 'configuration';

    const SUCCESS = 'success';

    const ENVIRONMENT_VALUES = 'environmentValues';

    const STEPS = 'steps';

    const STEP_1_APPLICATION = 'step_1_application';

    const STEP_2_REDIS = 'step_2_redis';

    const STEP_3_SESSION = 'step_3_session';

    const STEP_4_API_CONNECTION = 'step_4_ApiConnection';

    const STEP_5_ADD_PRODUCT = 'step_5_addProduct';

    const STEP_6_BATCH_REQUEST = 'step_6_batchRequests';

    const STEP_7_CREATE_CONTROLLER = 'step_7_createController';

    const STEP_8_RENDER_RESPONSE = 'step_8_renderResponse';

    const STEP_9_CURL_TIMETAKEN = 'step_9_curlTimeTaken';

    const STEP_10_LC_FWK = 'step_10_LcFWK';

    const STEP_11_OPCACHE = 'step_11_opcache';

    const SUMMARY = 'summary';

    private static Connection $connection;

    private $timer;

    private $healthChecks = [];

    private $filterParams = [];

    private $healthCheckController = null;

    /**
     * Constructor of the HealthCheck.
     */
    public function __construct() {
        $this->timer = Timer::getTimer('healthCheck');
        $this->healthChecks = $this->initStep();
        $this->healthChecks[self::DATA] = [];
        $this->healthChecks[self::DATA][self::ENVIRONMENT_VALUES] = [];
        $this->healthChecks[self::DATA][self::CONFIGURATION] = [];
        $this->healthChecks[self::DATA][self::STEPS] = [];

        self::$connection ??= Connection::getInstance();

        $this->healthChecks[self::CONFIGURATION]['$_GET'] = $_GET;

        $availableParams = [
            Parameters::PRODUCT_ID => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_INT
            ]),
            Parameters::TEMPLATE => new FilterInput([
                FilterInput::CONFIGURATION_FILTER_KEY_ENABLE_MODIFICATION => false,
                FilterInput::CONFIGURATION_FILTER_KEY_FILTER_VALIDATE_ID => FILTER_VALIDATE_BOOLEAN
            ])
        ];
        $this->filterParams = FilterInputHandler::getFilterFilterInputs(FilterInputHandler::PARAMS_FROM_GET, $availableParams);

        $this->healthChecks[self::CONFIGURATION]['filterParams'] = $this->filterParams;
    }

    private function initStep(): array {
        $step = [];
        $step[self::TIMER] = 0;
        $step[self::MSG] = '';
        $step[self::SUCCESS] = 0;
        $step[self::CONFIGURATION] = null;
        $step[self::DATA] = null;
        return $step;
    }

    private function runStep(string $step, callable $method): void {
        try {
            $this->timer->addFlag($step . Timer::START_SUFFIX);
            $method();
        } catch (ApiRequestException | \Exception | \Error $e) {
            $this->healthChecks[self::DATA][self::STEPS][$step][self::MSG] = $e->getMessage();
        } finally {
            $this->timer->addFlag($step . Timer::END_SUFFIX);
        }
    }

    /**
     * This method executes the health check. It consists into check some basic mechanisms of the commerce like:
     * <ul>
     * <li>Check start Application.</li>
     * <li>Check Redis.</li>
     * <li>Check Session.</li>
     * <li>Check Api Connection.</li>
     * <li>Check Add Product.</li>
     * <li>Check Batch Request.</li>
     * <li>Check Create Controller.</li>
     * <li>Check Render Response.</li>
     * <li>Timings.</li>
     * </ul>
     * If the request contains the parameter 'template=true' then it returns a json response with the checks results. 
     * <br>Otherwise, it generates a 'health check' log register with the checks results.
     */
    public function run(bool $healthcheck = true): void {
        $this->timer->start();
        $someError = false;
        try {
            $environmentVariables = Environment::getAll();
            unset($environmentVariables['APP_KEY']);
            unset($environmentVariables['APP_ID']);
            unset($environmentVariables['COMMERCE_ID']);

            if ($healthcheck) {
                $this->runStep(self::STEP_1_APPLICATION, function (): void {
                    $this->checkStartApplication();
                });
                $this->runStep(self::STEP_2_REDIS, function (): void {
                    $this->checkRedis();
                });
                $this->runStep(self::STEP_3_SESSION, function (): void {
                    $this->checkSession();
                });
                $this->runStep(self::STEP_4_API_CONNECTION, function (): void {
                    $this->checkApiConnection();
                });
                $this->runStep(self::STEP_5_ADD_PRODUCT, function (): void {
                    $this->checkAddProduct();
                });
                $this->runStep(self::STEP_6_BATCH_REQUEST, function (): void {
                    $this->checkBatchRequest();
                });
                $this->runStep(self::STEP_7_CREATE_CONTROLLER, function (): void {
                    $this->checkCreateController();
                });
                $this->runStep(self::STEP_8_RENDER_RESPONSE, function (): void {
                    $this->checkRenderResponse();
                });
                $this->runStep(self::STEP_9_CURL_TIMETAKEN, function (): void {
                    $this->checkCurlTimeTaken();
                });
                $this->runStep(self::STEP_10_LC_FWK, function (): void {
                    $this->checkLcFWK();
                });
                $this->runStep(self::STEP_11_OPCACHE, function (): void {
                    $this->getOpcache();
                });

                $this->healthChecks[self::DATA][self::ENVIRONMENT_VALUES] = [
                    'server' => $_SERVER,
                    'environmentVariables' => $environmentVariables,
                    'definedConstantsUser' => get_defined_constants(true)['user'],
                    'cookies' => [
                        'token' => Cookie::exist('token') ? Cookie::get('token') : 'undefined',
                        ApiRequest::BASKET_TOKEN => Cookie::exist(ApiRequest::BASKET_TOKEN) ? Cookie::get(ApiRequest::BASKET_TOKEN) : 'undefined',
                        Session::SESSION_TOKEN => Cookie::exist(Session::SESSION_TOKEN) ? Cookie::get(Session::SESSION_TOKEN) : 'undefined'
                    ],
                    'headers' => getallheaders(),
                ];
            }


            // Calculate the time for each step
            $this->timer->stop(false);

            $step_count = 0;
            foreach ($this->timer->getTimeBetweenFlags() as $stepName => $stepTime) {
                $stepTimerFlag = explode('_', $stepName, 2);
                if (isset($this->healthChecks[self::DATA][self::STEPS][$stepTimerFlag[1]])) {
                    $this->healthChecks[self::DATA][self::STEPS][$stepTimerFlag[1]][self::TIMER] = $stepTime;
                }
                if (!$this->healthChecks[self::DATA][self::STEPS][$stepTimerFlag[1]][self::SUCCESS]) {
                    $someError = true;
                }
                $step_count++;
            }
            $this->healthChecks[self::TIMER] = $this->timer->getLoggedTime();
            $this->healthChecks[self::MSG] = 'All steps checked';
            $this->healthChecks[self::SUCCESS] = 1;
        } catch (\Error $e) {
            $this->healthChecks[self::MSG] = 'ERROR: ' . $e->getMessage();
            $someError = true;
        }

        if (!$healthcheck && $someError) {
            header(Server::get('SERVER_PROTOCOL') . ' ' . 503, true);
        }

        if (isset($this->filterParams[Parameters::TEMPLATE]) && $this->filterParams[Parameters::TEMPLATE] == true) {
            header('Content-Type: application/json', true);
            echo json_encode($this->healthChecks, true);
        } else {
            if ($_GET[URL_ROUTE] != 'heartbeat.php') {
                HealthCheckLogger::getInstance()->info($this->healthChecks[self::MSG], $this->toLogArray());
            }
        }
    }

    private function toLogArray(): array {
        $process = [];
        $process[self::TIMER] = $this->healthChecks[self::TIMER];
        $process[self::MSG] = $this->healthChecks[self::MSG];
        $process[self::SUCCESS] = $this->healthChecks[self::SUCCESS];

        $steps = [];
        foreach ($this->healthChecks[self::DATA][self::STEPS] as $name => $result) {
            $steps[$name] = [];
            $steps[$name][self::TIMER] = $result[self::TIMER];
            $steps[$name][self::MSG] = $result[self::MSG];
            $steps[$name][self::SUCCESS] = $result[self::SUCCESS];
        }

        return [
            'process' => $process,
            'steps' => $steps,
            'code' => HealthCheckLogger::LOG_HEALTH_CHECK
        ];
    }

    private function checkStartApplication(): void {
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_1_APPLICATION] = $this->initStep();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_1_APPLICATION][self::CONFIGURATION] = [
            'API_URL' => Environment::get('API_URL'),
            'USE_CACHE_REDIS_SESSIONS' => LcFWK::getUseCacheRedisSession(),
            'SITE_PHYSICAL_PATH' => SITE_PHYSICAL_PATH
        ];
        Application::getInstance()->startApplication(LcFWK::start());
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_1_APPLICATION][self::DATA] = [
            'token' => Application::getInstance()->get('token')
        ];
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_1_APPLICATION][self::MSG] = 'Start Application OK';
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_1_APPLICATION][self::SUCCESS] = 1;
    }

    private function checkRedis(): void {
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_2_REDIS] = $this->initStep();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_2_REDIS][self::CONFIGURATION] = [
            'REDIS_HOST' => Environment::get('REDIS_HOST'),
            'REDIS_PORT' => Environment::get('REDIS_PORT'),
            'DATA_STORAGE_HOST' => Environment::get('DATA_STORAGE_HOST'),
            'DATA_STORAGE_PORT' => Environment::get('DATA_STORAGE_PORT'),
            'DATA_STORAGE_USE_TLS' => Environment::get('DATA_STORAGE_USE_TLS'),
            'DATA_STORAGE_USER' => Environment::get('DATA_STORAGE_USER'),
            'USE_CACHE_REDIS_SESSIONS' => LcFWK::getUseCacheRedisSession(),
            'LIFE_TIME_SESSION' => LcFWK::getLifeTimeSession()
        ];
        if (Redis::isEnabled()) {
            $testKey = 'TestKey_healthCheck:' . REQUEST_ID;
            $testKeyValue = 'TestValue_healthCheck:' . REQUEST_ID;
            $testTTL = 17;
            Redis::set($testKey, $testKeyValue, $testTTL);
            $data = Redis::get($testKey);
            if ($data === $testKeyValue) {
                $this->healthChecks[self::DATA][self::STEPS][self::STEP_2_REDIS][self::DATA] = 'Correct data save and read in Redis, value: ' . $testKeyValue . ', and key: ' . $testKey;
            } else {
                $this->healthChecks[self::DATA][self::STEPS][self::STEP_2_REDIS][self::DATA] = 'Data save and read in Redis has diferent values: $testKeyValue= ' . $testKeyValue . ',  data read from Redis: ' . $data;
            }
        } else {
            $this->healthChecks[self::DATA][self::STEPS][self::STEP_2_REDIS][self::DATA] = 'Redis is disabled';
        }
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_2_REDIS][self::MSG] = 'Check Redis handler OK';
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_2_REDIS][self::SUCCESS] = 1;
    }

    private function checkSession(): void {
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_3_SESSION] = $this->initStep();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_3_SESSION][self::CONFIGURATION] = [
            'API_URL' => Environment::get('API_URL'),
            'sessionClass' => get_class(Session::getInstance()),
            'LIFE_TIME_SESSION' => LcFWK::getLifeTimeSession(),
            'USE_CACHE_REDIS_SESSIONS' => LcFWK::getUseCacheRedisSession(),
        ];
        Session::getInstance()->start();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_3_SESSION][self::DATA] = $_SESSION;
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_3_SESSION][self::MSG] = 'Check start Session OK';
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_3_SESSION][self::SUCCESS] = 1;
    }

    private function checkApiConnection(): void {
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_4_API_CONNECTION] = $this->initStep();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_4_API_CONNECTION][self::CONFIGURATION] = [
            'API_URL' => Environment::get('API_URL'),
            'API_TIMEOUT' => API_TIMEOUT,
            'PATH' => '/settings',
            'COOKIES' => Cookie::getAll()
        ];
        $apiReq = self::$connection->doRequest((new RequestBuilder())->path('/settings')->build());
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_4_API_CONNECTION][self::DATA] = $apiReq;
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_4_API_CONNECTION][self::MSG] = 'Check ApiConnection, get settings OK';
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_4_API_CONNECTION][self::SUCCESS] = 1;
    }

    /**
     *
     * @throws \Exception
     */
    private function checkAddProduct(): void {
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_5_ADD_PRODUCT] = $this->initStep();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_5_ADD_PRODUCT][self::CONFIGURATION] = [
            'API_URL' => Environment::get('API_URL')
        ];

        $productService = Loader::service(Services::PRODUCT);
        $productsProductParametersGroup = new ProductsParametersGroup();
        if (isset($this->filterParams[Parameters::PRODUCT_ID]) && $this->filterParams[Parameters::PRODUCT_ID] !== null) {
            $productsProductParametersGroup->setIdList($this->filterParams[Parameters::PRODUCT_ID]);
            $this->healthChecks[self::DATA][self::STEPS][self::STEP_5_ADD_PRODUCT][self::CONFIGURATION] += [
                'product' => 'From $_Get: ' . $this->filterParams[Parameters::PRODUCT_ID]
            ];
        } else {
            $productsProductParametersGroup->setStockType(StockType::AVAILABLE_PREVISION_AND_ONREQUEST);
            $productsProductParametersGroup->setRandomItems(1);
            $this->healthChecks[self::DATA][self::STEPS][self::STEP_5_ADD_PRODUCT][self::CONFIGURATION] += [
                'product' => 'From OnlyInStock(true), setStockType(StockType::AVAILABLE_PREVISION_AND_ONREQUEST), setRandomItems(1)'
            ];
        }
        $product = $productService->getProducts($productsProductParametersGroup);
        $addProductParameters = new AddProductParametersGroup();
        if (!count($product->getItems())) {
            throw new \Exception('Any product returned from Api request');
        }
        $addProductParameters->setId($product->getItems()[0]->getId());
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_5_ADD_PRODUCT][self::CONFIGURATION] += [
            'addProductId' => $product->getItems()[0]->getId()
        ];
        $addProductParameters->setQuantity(1);
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_5_ADD_PRODUCT][self::CONFIGURATION] += [
            'setQuantity' => 1
        ];
        $productOptionsParameters = [];
        $productOptionsParametersCongfig = [];
        foreach ($product->getItems()[0]->getOptions() as $option) {
            if ($option->getRequired()) {
                $newOption = new ProductOptionParametersGroup();
                $newOption->setId($option->getId());
                if (!count(($option->getValues()))) {
                    throw new \Exception('Any option value in a required option id: ' . $option->getId());
                }
                $optionValue = new ProductOptionValueParametersGroup();
                $optionValue->setValue($option->getValues()[0]->getId());
                $newOption->addValue($optionValue);
                $productOptionsParameters[] = $newOption;
                $productOptionsParametersCongfig[] = [
                    'id' => $option->getId(),
                    'value' => $option->getValues()[0]->getId()
                ];
            }
        }
        $addProductParameters->setOptions($productOptionsParameters);
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_5_ADD_PRODUCT][self::CONFIGURATION] += [
            'setOptions' => $productOptionsParametersCongfig
        ];
        $response = Loader::service(\FWK\Enums\Services::BASKET)->addProduct($addProductParameters);
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_5_ADD_PRODUCT][self::DATA] = $response;
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_5_ADD_PRODUCT][self::MSG] = 'Check AddProduct, ' . ((strLen($response->getToken()) > 0) ? 'Success Product added ' : "Product don't added");
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_5_ADD_PRODUCT][self::SUCCESS] = 1;
    }

    private function checkBatchRequest(): void {
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_6_BATCH_REQUEST] = $this->initStep();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_6_BATCH_REQUEST][self::CONFIGURATION] = [
            'addGetBannersByPosition' => 1,
            'addGetPagesByPosition' => 1
        ];

        $batchRequest = new BatchRequests();
        Loader::service(Services::BANNER)->addGetBannersByPosition($batchRequest, 'BannersByPosition_1', 1);
        Loader::service(Services::PAGE)->addGetPagesByPosition($batchRequest, 'PagesByPosition_1', 1);
        $batchResult = BatchService::getInstance()->send($batchRequest);

        $this->healthChecks[self::DATA][self::STEPS][self::STEP_6_BATCH_REQUEST][self::DATA] = $batchResult;
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_6_BATCH_REQUEST][self::MSG] = 'Check send batchRequests, OK';
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_6_BATCH_REQUEST][self::SUCCESS] = 1;
    }

    private function checkCreateController(): void {
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_7_CREATE_CONTROLLER] = $this->initStep();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_7_CREATE_CONTROLLER][self::CONFIGURATION] = [
            'Controller' => RouteType::HEALTHCHECK,
            'ENUM_CLASS' => 'FWK\Enums\RouteTypes\InternalUtil'
        ];

        $this->healthCheckController = new HealthCheckController(RouterFactory::routeInternalUtil(RouteType::HEALTHCHECK, 'Util'));

        $this->healthChecks[self::DATA][self::STEPS][self::STEP_7_CREATE_CONTROLLER][self::DATA] = ($this->healthCheckController == !null ? get_class($this->healthCheckController) : 'null');
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_7_CREATE_CONTROLLER][self::MSG] = 'Check create a controller, OK';
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_7_CREATE_CONTROLLER][self::SUCCESS] = 1;
    }

    private function checkRenderResponse(): void {
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_8_RENDER_RESPONSE] = $this->initStep();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_8_RENDER_RESPONSE][self::CONFIGURATION] = [
            'Controller' => ($this->healthCheckController == !null ? get_class($this->healthCheckController) : 'null'),
            'TwigOptionAutoreload' => LcFWK::getTwigOptionAutoreload(),
            'TwigOptionStrictVariables' => LcFWK::getTwigOptionStrictVariables(),
            'TwigOptionCache' => LcFWK::getTwigOptionCache(),
            'TwigOptionOptimizations' => LcFWK::getTwigOptionOptimizations()
        ];

        $this->healthCheckController->testRender();

        $this->healthChecks[self::DATA][self::STEPS][self::STEP_8_RENDER_RESPONSE][self::DATA] = $this->healthCheckController->testRender();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_8_RENDER_RESPONSE][self::MSG] = 'Check render using Twig, OK';
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_8_RENDER_RESPONSE][self::SUCCESS] = 1;
    }

    private function checkCurlTimeTaken(): void {
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_9_CURL_TIMETAKEN] = $this->initStep();
        $apiPort = Environment::get('API_URL_PORT');
        if (!is_null($apiPort) && is_numeric($apiPort)) {
            $apiPort = ':' . $apiPort;
        } else {
            $apiPort = '';
        }
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_9_CURL_TIMETAKEN][self::DATA]['API'] =
            self::testUrl(Environment::get('API_URL') . $apiPort . '/health');

        if (isset(REQUEST_HEADERS['TEST_REQUEST'])) {
            $this->healthChecks[self::DATA][self::STEPS][self::STEP_9_CURL_TIMETAKEN][self::DATA]['testRequest'] =
                self::testUrl(REQUEST_HEADERS['TEST_REQUEST']);
        }

        $this->healthChecks[self::DATA][self::STEPS][self::STEP_9_CURL_TIMETAKEN][self::MSG] = 'Check curl, OK';
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_9_CURL_TIMETAKEN][self::SUCCESS] = 1;
    }

    private function testUrl(string $url): array {
        $response['url'] = $url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'ip: 127.0.0.1'
        ));
        $response['timeTake_ms'] = round(microtime(true) * 1000);
        $response['response'] = curl_exec($ch);
        $response['timeTake_ms'] = round(microtime(true) * 1000) - $response['timeTake_ms'];
        curl_close($ch);
        return $response;
    }

    private function checkLcFWK(): void {
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_10_LC_FWK] = $this->initStep();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_10_LC_FWK][self::DATA] = [
            'errorOnCacheableZeroTTL' => LcFWK::getErrorOnCacheableZeroTTL(),
            'lifeTimeCacheApplication' => LcFWK::getLifeTimeCacheApplication(),
            'lifeTimeCacheLcFWK' => LcFWK::getLifeTimeCacheLcFWK(),
            'lifeTimeCacheObjects' => LcFWK::getLifeTimeCacheObjects(),
            'lifeTimeCachePlugins' => LcFWK::getLifeTimeCachePlugins(),
            'lifeTimeSession' => LcFWK::getLifeTimeSession(),
            'logEnabled' => LcFWK::getLogEnabled(),
            'loggerConnectionEnabled' => LcFWK::getLoggerConnectionEnabled(),
            'loggerDebugInfoEnabled' => LcFWK::getLoggerDebugInfoEnabled(),
            'loggerExceptionEnabled' => LcFWK::getLoggerExceptionEnabled(),
            'loggerHealthcheckEnabled' => LcFWK::getLoggerHealthcheckEnabled(),
            'loggerLevel' => LcFWK::getLoggerLevel(),
            'loggerRequestHandlerEnabled' => LcFWK::getLoggerRequestHandlerEnabled(),
            'loggerTimerEnabled' => LcFWK::getLoggerTimerEnabled(),
            'logHandler' => LcFWK::getLogHandler(),
            'logLevel' => LcFWK::getLogLevel(),
            'twigOptionAutoreload' => LcFWK::getTwigOptionAutoreload(),
            'twigOptionCache' => LcFWK::getTwigOptionCache(),
            'twigOptionOptimizations' => LcFWK::getTwigOptionOptimizations(),
            'twigOptionStrictVariables' => LcFWK::getTwigOptionStrictVariables(),
            'useCacheRedisObject' => LcFWK::getUseCacheRedisObject(),
            'useCacheRedisSession' => LcFWK::getUseCacheRedisSession(),
            'maintenance' => LcFWK::getMaintenance(),
            'maintenanceAllowIps' => LcFWK::getMaintenanceAllowIps(),
            'phpCommerceToken' => LcFWK::getPhpCommerceToken(),
            'cleanCachePath' => LcFWK::getCleanCachePath(),
        ];
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_10_LC_FWK][self::MSG] = 'Check LcFWK, OK';
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_10_LC_FWK][self::SUCCESS] = 1;
    }


    private function getOpcache(): void {
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_11_OPCACHE] = $this->initStep();
        $opcache = opcache_get_status();
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_11_OPCACHE][self::DATA] = [
            'opcache_enabled' => $opcache['opcache_enabled'],
            'cache_full' => $opcache['cache_full'],
            'restart_pending' => $opcache['restart_pending'],
            'restart_in_progress' => $opcache['restart_in_progress'],
            'memory_usage' => $opcache['memory_usage'],
            'interned_strings_usage' => $opcache['interned_strings_usage'],
            'opcache_statistics' => $opcache['opcache_statistics'],
            'jit' => $opcache['jit']
        ];
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_11_OPCACHE][self::MSG] = 'Check opcache, OK';
        $this->healthChecks[self::DATA][self::STEPS][self::STEP_11_OPCACHE][self::SUCCESS] = 1;
    }
}
