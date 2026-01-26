<?php

namespace FWK\Core\Resources;

use SDK\Core\Resources\Timer;
use SDK\Application;
use SDK\Core\Resources\Cookie;
use SDK\Core\Resources\Environment;

/**
 * The commerce class is the responsible to start and manage the main processing flow of a request. 
 *
 * @see Commerce::start()
 *
 * @package FWK\Core\Resources
 */
class Commerce {

    private $apps;

    private $router;

    /**
     * Constructor.
     *
     * @param array $apps
     *            List of the Apps associated to the commerce.
     */
    public function __construct($apps = []) {
        if (isset($_GET[URL_ROUTE]) && ($_GET[URL_ROUTE] === 'healthcheck.php' || $_GET[URL_ROUTE] === 'heartbeat.php')) {
            $healthCheck = new HealthCheck();
            $healthCheck->run($_GET[URL_ROUTE] === 'healthcheck.php');
            exit();
        }
        $this->apps = $apps;
        CacheControl::setInitialCacheHash(Cookie::get('cache-hash'));
    }

    private function application(): bool {
        $onError = callMethod(function () {
            Response::getInstance();
            Response::addCommerceId(Environment::get('COMMERCE_ID'));
            Application::getInstance()->startApplication(LcFWK::start());
        }, null, 'Commerce application start', [], true);
        return $onError;
    }

    /**
     * This method starts the main processing flow of the request.
     * 
     * @internal
     * It initializes, loads and manages the main components that are required to process the request.<br>
     * 
     * <ol>
     *  <li>At first the application is initialized:
     *      <ul>
     *      <li>The connection to Core API is opened obtaining the Token that allows to make requests to the API.</li>
     *      <li>It gets and stores in cache all the Commerce configuration that is defined in LogiCommerce. 
     *          This cache provides quick access to this data avoying requests to the API. Cache timing is defined in the LcFWK::getLifeTimeCacheApplication().</li>
     *      </ul>
     *  </li>
     *  <li>
     *      Next step consists on initialize the session:<br>
     *      Based on the LcFWK::getUseCacheRedisSession(), the session information will be fetched from the PHP server or Redis.
     *  </li>
     *  <li>
     *      Last step consists on analyze the request, this is done using the Router object.
     *  </li>
     * </ol>
     * 
     * @return void
     * 
     * @see Application
     * @see Session
     * @see Router
     */
    public function start() {
        callMethod(function () {
            Utils::addTimerDebugFlag('Commerce-start', Timer::START_SUFFIX);
            Utils::addTimerDebugFlag('application', Timer::START_SUFFIX);
            $onError = $this->application();
            Utils::addTimerDebugFlag('application', Timer::END_SUFFIX);
            if (!$onError) {
                Utils::addTimerDebugFlag('Session-start', Timer::START_SUFFIX);
                $session = Session::getInstance();
                $onError = $session->start();
                Application::getInstance()->setPlugins($session->getNavigationHash() ?? "");
                Utils::addTimerDebugFlag('Session-start', Timer::END_SUFFIX);
                if (!$onError) {
                    Language::reloadInstance($session->getGeneralSettings()->getLanguage());
                    $this->router = new Router($this->apps);
                    $this->request();
                }
            }
        }, function () {
            self::end();
        }, 'Commerce start');
    }

    /**
     * This method ends the main processing flow of the request.
     */
    public static function end() {
        Utils::addTimerDebugFlag('Commerce-start', Timer::END_SUFFIX);
        Utils::stopTimerDebug();
        if (TIMER_DEBUG) {
            Utils::outputTimerDebug();
        }
        exit();
    }

    private function request() {
        Utils::addTimerDebugFlag('request', Timer::START_SUFFIX);
        $this->router->execute();
        Utils::addTimerDebugFlag('request', Timer::END_SUFFIX);
    }
}
