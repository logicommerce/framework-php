<?php

namespace FWK\Core\Controllers;

use FWK\Core\Resources\RoutePaths;
use FWK\Enums\RouteType;
use FWK\Core\Resources\RouterFactory;
use SDK\Core\Enums\Traits\EnumResolverTrait;
use SDK\Dtos\Common\Route;
use FWK\Core\Resources\Utils;
use SDK\Core\Resources\Environment;

/**
 * This is the ControllersFactory class, a factory of Controllers.
 * This class facilitates the creation of the Controllers.
 *
 * @abstract
 *
 * @see ControllersFactory::getController()
 * @see ControllersFactory::getInternalController()
 * @see ControllersFactory::getPath()
 *
 * @package FWK\Core\Controllers
 */
abstract class ControllersFactory {
    use EnumResolverTrait;

    private const CONTROLLERS_PATH = 'Controllers';

    private const PHYSICAL_LOCATION_DIRECTORY = 'PhysicalLocation';

    private const ACCOUNT_DIRECTORY = 'Account';

    private const UTIL_DIRECTORY = 'Util';

    private const FIXED_DIRECTORIES = [
        RouteType::ACCOUNT => self::ACCOUNT_DIRECTORY,
        RouteType::REGISTERED_USER => self::ACCOUNT_DIRECTORY,
        RouteType::ACCOUNT_REGISTERED_USER => self::ACCOUNT_DIRECTORY,
        RouteType::ACCOUNT_REGISTERED_USER_APPROVE => self::ACCOUNT_DIRECTORY,
        RouteType::ACCOUNT_REGISTERED_USER_CREATE => self::ACCOUNT_DIRECTORY,
        RouteType::ACCOUNT_REGISTERED_USERS => self::ACCOUNT_DIRECTORY,
        RouteType::BANNER => 'Banner',
        RouteType::BASKET => 'Basket',
        RouteType::BLOG => 'Blog',
        RouteType::CHANGE_PASSWORD_ANONYMOUS => '',
        RouteType::CHECKOUT => 'Checkout',
        RouteType::CLOSE_COMMERCE => '',
        RouteType::EXPRESS_CHECKOUT_CANCEL => 'Checkout',
        RouteType::EXPRESS_CHECKOUT_RETURN => 'Checkout',
        RouteType::FEATURED_PRODUCTS => 'Product',
        RouteType::PRODUCT_COMPARISON => 'ProductComparison',
        RouteType::GEOLOCATION => 'Geolocation',
        RouteType::RESOURCES => 'Resources',
        RouteType::NEWS => 'News',
        RouteType::NEWS_LIST => 'News',
        RouteType::NOT_FOUND => '',
        RouteType::LOGIN_REQUIRED => '',
        RouteType::WEBHOOK => '',
        RouteType::WEBHOOK_PATH => '',
        RouteType::PAGE => 'Page',
        RouteType::PHYSICAL_LOCATION => self::PHYSICAL_LOCATION_DIRECTORY,
        RouteType::PHYSICAL_LOCATION_CITIES => self::PHYSICAL_LOCATION_DIRECTORY,
        RouteType::PHYSICAL_LOCATION_COUNTRIES => self::PHYSICAL_LOCATION_DIRECTORY,
        RouteType::PHYSICAL_LOCATION_MAP => self::PHYSICAL_LOCATION_DIRECTORY,
        RouteType::PHYSICAL_LOCATION_STATES => self::PHYSICAL_LOCATION_DIRECTORY,
        RouteType::PHYSICAL_LOCATION_STORES => self::PHYSICAL_LOCATION_DIRECTORY,
        RouteType::PRIVACY_POLICY => 'Page',
        RouteType::PRODUCT => 'Product',
        RouteType::REGISTERED_USER => self::ACCOUNT_DIRECTORY,
        RouteType::REGISTERED_USER_SALES_AGENT => self::ACCOUNT_DIRECTORY,
        RouteType::REGISTERED_USER_SALES_AGENT_CUSTOMERS => self::ACCOUNT_DIRECTORY,
        RouteType::REGISTERED_USER_SALES_AGENT_SALES => self::ACCOUNT_DIRECTORY,
        RouteType::REGISTERED_USER_CHANGE_PASSWORD => self::ACCOUNT_DIRECTORY,
        RouteType::OFFERS => 'Product',
        RouteType::TERMS_OF_USE => 'Page',
        RouteType::USED_ACCOUNT_SWITCH => '',
        RouteType::USER => 'User',
        RouteType::UTIL => self::UTIL_DIRECTORY,
    ];

    private const SHARED_DIRECTORIES = [
        RouteType::ACCOUNT_ADDRESSES => RouteType::USER_ADDRESS_BOOK,
        RouteType::ACCOUNT_ADDRESS_CREATE => RouteType::USER_ADDRESS_BOOK_ADD,
        RouteType::ACCOUNT_ADDRESS => RouteType::USER_ADDRESS_BOOK_EDIT,
        RouteType::REGISTERED_USER_CHANGE_PASSWORD => RouteType::USER_CHANGE_PASSWORD,
        RouteType::ACCOUNT_COMPLETE => RouteType::USER_COMPLETE_ACCOUNT,
        RouteType::ACCOUNT_CREATE => RouteType::USER_CREATE_ACCOUNT,
        RouteType::ACCOUNT_DELETE => RouteType::USER_DELETE_ACCOUNT,
        RouteType::ACCOUNT_VOUCHER_CODES => RouteType::USER_VOUCHER_CODES,
        RouteType::REGISTERED_USER_LOST_PASSWORD => RouteType::USER_LOST_PASSWORD,
        RouteType::REGISTERED_USER_OAUTH => RouteType::USER_OAUTH,
        RouteType::REGISTERED_USER_OAUTH_CALLBACK => RouteType::USER_OAUTH_CALLBACK,
        RouteType::ACCOUNT_ORDER => RouteType::USER_ORDER,
        RouteType::ACCOUNT_REGISTERED_USER_PAYMENT_CARDS => RouteType::USER_PAYMENT_CARDS,
        RouteType::ACCOUNT_REGISTERED_USER_STOCK_ALERTS => RouteType::USER_STOCK_ALERTS,
        RouteType::ACCOUNT_REGISTERED_USER_SUBSCRIPTIONS => RouteType::USER_SUBSCRIPTIONS,
        RouteType::ACCOUNT_WELCOME => RouteType::USER_USER_WELCOME,
        RouteType::ACCOUNT_VERIFY => RouteType::USER_VERIFY_ACCOUNT,
        RouteType::ACCOUNT_RMAS => RouteType::USER_RMAS,
        RouteType::ACCOUNT_REWARD_POINTS => RouteType::USER_REWARD_POINTS,
        RouteType::ACCOUNT_REGISTERED_USER_SHOPPING_LISTS => RouteType::USER_SHOPPING_LISTS,
        RouteType::ACCOUNT_ID => RouteType::ACCOUNT,
        RouteType::USER_SALES_AGENT_CUSTOMERS => RouteType::REGISTERED_USER_SALES_AGENT_CUSTOMERS,
        RouteType::USER_SALES_AGENT_SALES => RouteType::REGISTERED_USER_SALES_AGENT_SALES,
    ];

    /**
     * This method returns the Controller that corresponds to the given Route.
     * It searches the Controller in the locations given by parameter, following the given order in the array (it stops searching when it finds the first occurrence).
     *
     * @param Route $route
     * @param array $locations
     *
     * @return Controller|NULL
     */
    public static function getController(Route $route, array $locations): ?Controller {
        return self::getControllerObject($route, $locations);
    }

    /**
     * This method returns an array containing the internal Controller and the Route of the request.
     * It searches the Controller in the locations given by parameter, following the given order in the array (it stops searching when it finds the first occurrence).
     *
     * @param array $locations
     *
     * @return array|NULL Containing Controller and Route
     */
    public static function getInternalController(array $locations): ?array {

        if (isset(REQUEST_HEADERS['X-SESSION-COOKIES'])) {
            $routeSplit = [
                INTERNAL_PREFIX,
                'resources',
                'get_session'
            ];
        } else {
            $routeSplit = explode('/', preg_replace('/^\/+/', '', filter_input(INPUT_GET, URL_ROUTE, FILTER_UNSAFE_RAW, [FILTER_FLAG_STRIP_BACKTICK, FILTER_FLAG_STRIP_HIGH, FILTER_FLAG_STRIP_LOW])));
        }

        if (($routeSplit[0] . '/') === (INTERNAL_PREFIX . '/') && count($routeSplit) > 2) {
            if (!strlen($routeSplit[1]) || !strlen($routeSplit[2]) || ($routeSplit[2] === 'demo' && !Environment::get('DEVEL'))) {
                return null;
            }
            $routeSplit[1] = ucfirst($routeSplit[1]);
            if ($routeSplit[1] === self::UTIL_DIRECTORY) {
                $route = RouterFactory::routeInternalUtil($routeSplit[2], $routeSplit[1]);
            } else {
                $route = RouterFactory::routeInternal($routeSplit[2], $routeSplit[1]);
            }
            if ($routeSplit[2] == 'plugin_route' && isset($routeSplit[3])) {
                $controller = self::getPluginControllerObject($route, $locations, '', Utils::getCamelFromSnake($routeSplit[3], '.'));
            } else {
                $controller = self::getControllerObject($route, $locations);
            }
            if (!is_null($controller)) {
                return [
                    'controller' => $controller,
                    'route' => $route
                ];
            }
        }
        return null;
    }

    private static function getControllerObject(Route $route, array $locations, string $namespace = ''): ?Controller {
        $controller = self::CONTROLLERS_PATH . '\\' . $namespace . self::getPath($route->getType()) . 'Controller';
        foreach ($locations as $location) {
            $class = $location . $controller;
            if (class_exists($class)) {
                return new $class($route);
            }
        }
        return null;
    }

    private static function getPluginControllerObject(Route $route, array $locations, string $namespace, string $plugin): ?Controller {
        $locations[] = 'Plugins\\' . $plugin . '\\';
        $controller = self::CONTROLLERS_PATH . '\\' . $namespace . self::getPath($route->getType()) . '\\' . $plugin . 'Controller';
        foreach ($locations as $location) {
            $class = $location . $controller;
            if (class_exists($class)) {
                return new $class($route);
            }
        }
        return null;
    }

    /**
     * This method returns the controller path of the route type given by parameter.
     *
     * @param string $type
     *
     * @return string
     */
    public static function getPath(string $type): string {
        $controllerPath = '';
        if (isset(self::SHARED_DIRECTORIES[$type])) {
            $type = self::SHARED_DIRECTORIES[$type];
        }

        if (isset(self::FIXED_DIRECTORIES[$type])) {
            if (strlen(self::FIXED_DIRECTORIES[$type])) {
                $controllerPath .= self::FIXED_DIRECTORIES[$type] . '\\';
            }
            $controllerPath .= Utils::getCamelFromSnake($type);
        } else {
            $parts = explode("_", $type, 3);
            if (isset(self::FIXED_DIRECTORIES[$parts[0]]) && $parts[1] === strtoupper(INTERNAL_FOLDER)) {
                $controllerPath .= self::FIXED_DIRECTORIES[$parts[0]] . '\\' . INTERNAL_FOLDER . '\\' . Utils::getCamelFromSnake($parts[2]);
            } else if (isset($parts[2]) && isset(self::FIXED_DIRECTORIES[$parts[0] . '_' . $parts[1]]) && strpos($parts[2], strtoupper(INTERNAL_FOLDER)) !== false) {
                $parts = explode("_", $type, 4);
                $controllerPath .= self::FIXED_DIRECTORIES[$parts[0] . '_' . $parts[1]] . '\\' . INTERNAL_FOLDER . '\\' . Utils::getCamelFromSnake($parts[3]);
            } else {
                $parts = explode("_", $type, 2);
                $controllerPath = ucwords(strtolower($parts[0]));
                if (count($parts) === 2) {
                    $controllerPath .= '\\' . Utils::getCamelFromSnake($parts[1]);
                }
            }
        }
        return $controllerPath;
    }

    public static function extractIdsFromUrl(Route $route): array {
        $routePath = RoutePaths::getRouteTypePaths()[$route->getType()] ?? '';
        if ($routePath === '') return [];

        $path = parse_url($route->getCanonical(), PHP_URL_PATH) ?? '/';

        // Escapar todo y sustituir {param} -> grupos nombrados
        $pattern = preg_quote($routePath, '#');
        $pattern = preg_replace('/\\\\\{(\w+)\\\\\}/', '(?P<$1>[^/]+)', $pattern);

        // Regex no anclado: encaja en cualquier parte; asegura fin de segmento
        $regex = '#' . $pattern . '(?=/|$)#';

        if (preg_match($regex, $path, $m)) {
            return array_map('urldecode', array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY));
        }
        return [];
    }
}
