<?php

namespace FWK\Core\Resources;

use FWK\Enums\RouteItems;
use FWK\Enums\RouteType;

/**
 * This factory has the function of concentrating the logic of creating the different Routes that will not arrive from the API
 *
 * The function GetType from Route class check if the type is a correct value, if you create a new type you must create a new CONST in FWK\Enums\RouteType
 *
 * @see FWK\Enums\RouteType
 *
 * @see RouterFactory::routeError()
 * @see RouterFactory::routeNotFound()
 * @see RouterFactory::routeApp()
 * @see RouterFactory::routeInternal()
 * @see RouterFactory::routeInternalUtil()
 * @see RouterFactory::routeCloseCommerce()
 * @see RouterFactory::routeLoginRequired()
 *
 * @package FWK\Core\Resources
 *         
 * @see SDK\Dtos\Common\Router
 */
final class RouterFactory {

    static private function makeRoute(array $dataRoute): Route {
        $sessionRoute = Session::getInstance()->getDefaultRoute()->toArray();
        $sessionRoute[RouteItems::THEME][RouteItems::LAYOUT] = DEFAULT_ROUTE[RouteItems::THEME][RouteItems::LAYOUT];
        $sessionRoute[RouteItems::THEME][RouteItems::CONTENT] = DEFAULT_ROUTE[RouteItems::THEME][RouteItems::CONTENT];
        return new Route(array_replace_recursive($sessionRoute, $dataRoute));
    }

    /**
     * This method creates and returns a new Route with the default values that an close coommerce Route needs.
     *
     * @return Route
     */
    static public function routeCloseCommerce(): Route {
        return self::makeRoute([
            RouteItems::TYPE => RouteType::CLOSE_COMMERCE,
            RouteItems::METADATA => [
                RouteItems::INDEXABLE => false,
                RouteItems::LINK_FOLLOWING => false
            ]
        ]);
    }

    /**
     * This method creates and returns a new Route with the default values that login required Route needs.
     *
     * @return Route
     */
    static public function routeLoginRequired(): Route {
        return self::makeRoute([
            RouteItems::TYPE => RouteType::LOGIN_REQUIRED,
            RouteItems::METADATA => [
                RouteItems::INDEXABLE => false,
                RouteItems::LINK_FOLLOWING => false
            ]
        ]);
    }

    /**
     * This method creates and returns a new Route with the default values that an Error Route needs.
     *
     * @return Route
     */
    static public function routeError(): Route {
        return self::makeRoute([
            RouteItems::TYPE => RouteType::ERROR,
            RouteItems::STATUS => 503,
            RouteItems::METADATA => [
                RouteItems::INDEXABLE => false,
                RouteItems::LINK_FOLLOWING => false
            ]
        ]);
    }

    /**
     * This method creates and returns a new Route with the default values that a NotFound Route needs.
     * 
     *
     * @param int $status
     *
     * @return Route
     */
    static public function routeNotFound(int $status = 404): Route {
        return self::makeRoute([
            RouteItems::TYPE => RouteType::NOT_FOUND,
            RouteItems::STATUS => $status,
            RouteItems::METADATA => [
                RouteItems::INDEXABLE => false,
                RouteItems::LINK_FOLLOWING => false
            ]
        ]);
    }

    /**
     * This method creates and returns a new Route with the default values that an App Route needs.
     *
     * @return Route
     */
    static public function routeApp(): Route {
        return self::makeRoute([
            RouteItems::TYPE => 'app',
            RouteItems::METADATA => [
                RouteItems::INDEXABLE => false,
                RouteItems::LINK_FOLLOWING => false
            ]
        ]);
    }

    /**
     * This method creates and returns a new Route with the default values that an Internal Route needs.
     *
     * @param string $controller
     * @param string $class
     *
     * @return Route
     */
    static public function routeInternal(string $controller, string $class): Route {
        return self::makeRoute([
            RouteItems::TYPE => $controller,
            RouteItems::ENUM_CLASS => $class,
            RouteItems::METADATA => [
                RouteItems::INDEXABLE => false,
                RouteItems::LINK_FOLLOWING => false
            ]
        ]);
    }

    /**
     * This method creates and returns a new Route with the default values that an InternalUtil Route needs.
     *
     * @param string $controller
     * @param string $class
     *
     * @return Route
     */
    static public function routeInternalUtil(string $controller, string $class): Route {
        return self::makeRoute([
            RouteItems::TYPE => $controller,
            RouteItems::ENUM_CLASS => $class,
            RouteItems::THEME => [
                RouteItems::NAME => INTERNAL_THEME
            ],
            RouteItems::METADATA => [
                RouteItems::INDEXABLE => false,
                RouteItems::LINK_FOLLOWING => false
            ]
        ]);
    }
}
