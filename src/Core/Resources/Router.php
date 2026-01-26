<?php

namespace FWK\Core\Resources;

use SDK\Core\Enums\Traits\EnumResolverTrait;
use SDK\Core\Resources\Server;
use SDK\Core\Resources\Timer;
use SDK\Core\Resources\Environment;
use SDK\Dtos\Common\Route;
use FWK\Enums\Services;
use FWK\Enums\RouteType;
use FWK\Core\Resources\Route as FwkRoute;
use SDK\Application;
use FWK\Core\Theme\Theme;
use SDK\Enums\SessionType;

/**
 * The Router class responsability is to analyze the path of the request, determine the controller that has to process it and enroute the request to the corresponding controller.
 *
 * @see Router::getRoute()
 * @see Router::execute()
 * @see Router::error()
 * @see Router::notFound()
 * 
 * @see EnumResolverTrait
 *
 * @package FWK\Core\Resources
 */
final class Router {
    use EnumResolverTrait;

    private $route = null;

    private $apps = null;

    private $app = null;

    private $internalController = null;

    /**
     * Constructor.
     *
     * @param array $apps
     *            List of the Apps associated to the commerce.
     */
    public function __construct($apps = []) {
        Utils::addTimerDebugFlag('Router-constructor', Timer::START_SUFFIX);
        $this->apps = $apps;
        $this->route = RouterFactory::routeNotFound();
        Utils::addTimerDebugFlag('Router-constructor', Timer::END_SUFFIX);
    }

    private function enroute(): void {
        Utils::addTimerDebugFlag('Router-enroute', Timer::START_SUFFIX);
        $controller = Loader::getInternalController();
        if (!is_null($controller)) {
            $this->route = $controller['route'];
            $this->internalController = $controller['controller'];
        } else {
            $loaded = false;
            if (!$loaded) {
                $this->route = Loader::service(Services::ROUTE)->getRoute();
                if ($this->route->getStatus() === 200) {
                    if ($this->route->getType() === RouteType::PAGE) {
                        $this->route = new FwkRoute($this->route->toArray(), Loader::service(Services::PAGE)->getPageById($this->route->getId()));
                    }
                    Session::getInstance()->initRoute($this->route);
                }
            }
        }
        $this->reloadRouteLinkedInstances();
        Utils::addTimerDebugFlag('Router-enroute', Timer::END_SUFFIX);
    }

    private function reloadRouteLinkedInstances() {
        Language::reloadInstance($this->route->getLanguage());
        Theme::reloadInstance($this->route);
        RoutePaths::reloadInstance();
    }

    /**
     * This method returns the request url info in a Route object.
     *
     * @return Route
     */
    public function getRoute(): Route {
        return $this->route;
    }

    /**
     * This method analyzes the path of the request, determines the controller that has to process it, enroutes the request to the corresponding controller and executes it.
     *
     * @internal
     * It differentiates three request types: 
     * <ol>
     * <li>Internal: for these requests it is not needed to access the Api to determine the destination of the request it can be resolved without access it.</li>
     * <li>Api: these are not internal requests, so it is required to access the Api to determine the destination of the request.</li>
     * </ol>
     *
     * @return void
     */
    public function execute(): void {
        $themeConfigurationCommerce = Theme::getInstance()->getConfiguration()->getCommerce();
        if (!Application::getInstance()->getEcommerceSettings()->getGeneralSettings()->getActive()) {
            $this->closeCommerce();
        } else {
            $this->enroute();
            if (in_array($this->route->getStatus(), [301, 302])) {
                $this->location();
            }
            if (!$themeConfigurationCommerce->getMaintenanceAllowAccess() && !in_array($this->route->getType(), $themeConfigurationCommerce->getMaintenanceAvailableRoutes())) {
                $this->closeCommerce();
            } else if (LcFWK::getLoginRequired() && Session::getInstance()->getBasket()->getType() === SessionType::ANONYMOUS && !in_array($this->route->getType(), $themeConfigurationCommerce->getLoginRequiredAvailableRoutes())) {
                $this->loginRequired();
            } else {
                switch ($this->route->getStatus()) {
                    case 200:
                    case 403:
                        $this->executeController($this->route->getType());
                        break;
                    case 404:
                        $this->notFound();
                        break;
                    case 500:
                        $this->error();
                        break;
                    default:
                        $this->notFound();
                }
            }
        }
    }

    private function executeController(string $type, array $data = []) {
        Utils::addTimerDebugFlag('Router-executeController', Timer::START_SUFFIX);
        if (!is_null($this->internalController)) {
            $controller = $this->internalController;
        } else {
            $controller = Loader::controller($this->route);
        }
        $controller->run($data);
        Utils::addTimerDebugFlag('Router-executeController', Timer::END_SUFFIX);
    }

    /**
     * This method enroutes the request to close commerce controller and executes it.
     *
     * @param array $data
     *
     * @return void
     */
    public function closeCommerce(array $data = []) {
        $this->route = RouterFactory::routeCloseCommerce();
        $this->executeController(RouteType::CLOSE_COMMERCE, $data);
    }

    /**
     * This method enroutes the request to error controller and executes it.
     *
     * @param array $data
     *
     * @return void
     */
    public function error(array $data = []) {
        Response::addHeader(Server::get('SERVER_PROTOCOL') . ' 500 Internal Error');
        $this->route = RouterFactory::routeError();
        $this->executeController(RouteType::ERROR, $data);
    }

    /**
     * This method enroutes the request to the not found controller and executes it.
     *
     * @param int $status
     *
     * @return void
     */
    public function notFound(int $status = 404) {
        Response::addHeader(Server::get('SERVER_PROTOCOL') . ' ' . $status);
        $this->route = RouterFactory::routeNotFound($status);
        $this->executeController(RouteType::NOT_FOUND, [
            "status" => $status
        ]);
    }

    /**
     * This method enroutes the request to login required controller and executes it.
     *
     * @param array $data
     *
     * @return void
     */
    public function loginRequired(array $data = []) {
        $this->route = RouterFactory::routeLoginRequired();
        $this->executeController(RouteType::LOGIN_REQUIRED, $data);
    }

    private function location() {
        $redirectURL =  Utils::interceptURL($this->route->getRedirectUrl());
        Response::redirect($redirectURL . Utils::parseArrayToPathParameters(Utils::deleteParamsFromRequest([URL_ROUTE])), $this->route->getStatus(), true);
    }
}
