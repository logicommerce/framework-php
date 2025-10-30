<?php

namespace FWK\Core\Resources;

use FWK\Core\Controllers\ControllersFactory;
use FWK\Core\Controllers\Controller;
use SDK\Core\Services\Service;
use SDK\Dtos\Common\Route as SDKRoute;
use FWK\Twig\Functions\TwigFunctions;
use FWK\Core\Theme\Theme;
use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Core\Resources\Timer;
use FWK\Core\Exceptions\CommerceException;
use FWK\Twig\Extensions\TwigExtensions;

/**
 * This is the Loader class, a helper to build controllers models and services.
 * 
 * Controllers, models and services should be generated in SITE and FWK using this class as a general rule 
 * because it implements the logical mechanism that allows to overwrite the FWK classes in the SITE layer.<br>
 * So, the Loader tries first to find the class in the SITE layer and instanciate it, and if it does not find it then the Loader instanciates the FWK class.   
 *
 * @see Loader::service()
 * @see Loader::viewHelper()
 * @see Loader::twigFunctions()
 * @see Loader::twigExtensions()
 * @see Loader::controller()
 * @see Loader::EnumInternalRouteType()
 * @see Loader::getClassFQN()
 * @see Loader::getInternalController()
 *
 * @package FWK\Resources
 */
class Loader {
    private const RE_VALID_NAME = "/^[a-zA-Z][a-zA-Z0-9]+$/";
    public const LOCATIONS = [SITE_NAMESPACE, FWK_NAMESPACE];

    /**
     * This method returns the service that corresponds to the given service key.
     * It first searches the service in the site and if it does not exist then it is searched in the framework.
     *
     * @param string $key of the service.
     * @param string $namespace where to find the service.
     *
     * @return Service
     * 
     * @throws CommerceException if no service class is found for the given service key in the given namespace
     */
    public static function service(string $key, string $namespace = 'Services\\'): Service {
        foreach (self::LOCATIONS as $location) {
            $class = self::getClassFQN(trim($key), $location . $namespace, 'Service');
            if (class_exists($class)) {
                $service = $class::getInstance();
                if ($service instanceof Service) {
                    return $service;
                }
            }
        }
        throw new CommerceException("ERROR: Service [{$class}] is invalid.", CommerceException::LOADER_SERVICE_NOT_FOUND);
    }

    /**
     * This method returns the ViewHelper that corresponds to the given viewHelperName and path, and for the given languageSheet and theme.
     * It first searches the ViewHelper in the site and if it does not exist then it is searched in the framework.
     * 
     * @param string $path is the path of the ViewHelper
     * @param string $viewHelperName is the name of the ViewHelper
     * @param Language $languageSheet
     * @param Theme $theme
     * 
     * @return ViewHelper from ViewHelpers path
     * 
     * @throws CommerceException if no ViewHelper class is found for the given path and viewHelperName.
     */
    public static function viewHelper(string $path, string $viewHelperName, Language $languageSheet, Theme $theme, ?Session $session): ViewHelper {
        $namespace = 'ViewHelpers\\';
        foreach (self::LOCATIONS as $location) {
            $class = self::getClassFQN(trim($viewHelperName), $location . $namespace . $path . '\\');
            if (class_exists($class)) {
                $viewHelper = new $class($languageSheet, $theme, $session);
                if ($viewHelper instanceof ViewHelper) {
                    return $viewHelper;
                }
            }
        }
        throw new CommerceException("ERROR: ViewHelpers [{$class}] is invalid.", CommerceException::LOADER_VIEW_HELPER_NOT_FOUND);
    }

    /**
     * This method returns the TwigFunctions that corresponds to the given key.
     * It first searches the TwigFunctions in the site and if it does not exist then it is searched in the framework.
     *
     * @param string $key
     *
     * @return TwigFunctions
     *
     * @throws CommerceException if a TwigFunctions referenced by the given key does not exist.
     */
    public static function twigFunctions(string $key): TwigFunctions {
        $functionClass = 'TwigFunctions' . ucfirst($key);
        foreach (self::LOCATIONS as $location) {
            $class = self::getClassFQN($functionClass, $location . 'Twig\\Functions\\', '');
            if (class_exists($class)) {
                return new $class();
            }
        }
        throw new CommerceException("ERROR: Undefined TwigFunctions class: " . $functionClass, CommerceException::LOADER_TWIG_FUNCTIONS_NOT_FOUND);
    }

    /**
     * This method returns the TwigExtensions that corresponds to the given key.
     * It first searches the TwigExtensions in the site and if it does not exist then it is searched in the framework.
     *
     * @param string $key
     *
     * @return TwigExtensions
     *
     * @throws CommerceException if a TwigExtensions referenced by the given key does not exist.
     */
    public static function twigExtensions(string $key): TwigExtensions {
        $extensionClass = 'TwigExtensions' . ucfirst($key);
        foreach (self::LOCATIONS as $location) {
            $class = self::getClassFQN($extensionClass, $location . 'Twig\\Extensions\\', '');
            if (class_exists($class)) {
                return new $class();
            }
        }
        throw new CommerceException("ERROR: Undefined TwigExtensions class:" . $extensionClass, CommerceException::LOADER_TWIG_EXTENSIONS_NOT_FOUND);
    }

    /**
     * This method returns the Controller that corresponds to the given Route.
     * It first searches the Controller in the site and if it does not exist then it is searched in the framework.
     *
     * @param SDKRoute $route
     * 
     * @return Controller
     * 
     * @throws CommerceException if the controller is not found.
     */
    public static function controller(SDKRoute $route): Controller {
        Utils::addTimerDebugFlag('Loader-controller', Timer::START_SUFFIX);
        $controller = ControllersFactory::getController($route, self::LOCATIONS);
        if (is_null($controller)) {
            $className = ControllersFactory::getPath($route->getType());
            throw new CommerceException("ERROR: Unexisting Controller for [{$className}].", CommerceException::LOADER_CONTROLLER_NOT_FOUND);
        }
        Utils::addTimerDebugFlag('Loader-controller', Timer::END_SUFFIX);
        return $controller;
    }


    /**
     * This method returns the path of the Enum that corresponds to the given internal route type.
     * It first searches the Enum in the site and if it does not exist then it is searched in the framework.
     *
     * @param string $type
     *
     * @return string
     *
     * @throws CommerceException if the type specified does not exist.
     */
    public static function EnumInternalRouteType(string $type): string {
        foreach (self::LOCATIONS as $location) {
            $class = self::getClassFQN('Internal' . $type, $location . 'Enums\\RouteTypes\\', '');
            if (class_exists($class)) {
                return $class;
            }
        }
        throw new CommerceException("ERROR: Undefined Enum Internal RouteType: Internal$type class. $class", CommerceException::LOADER_ENUM_INTERNAL_NOT_FOUND);
    }


    /**
     * This method validates the key and returns the concatenation of $namespace, $key and $suffix
     * 
     * @param string $key
     * @param string $namespace
     * @param string $suffix
     *
     * @return string
     *
     * @throws CommerceException if the key is not valid.
     */
    public static function getClassFQN(string $key, string $namespace, string $suffix = ''): string {
        if (!preg_match(self::RE_VALID_NAME, $key)) {
            throw new CommerceException("ERROR: [{$key}] is invalid $suffix name.", CommerceException::LOADER_INVALID_CLASS_NAME);
        }
        return $namespace . ucfirst($key) . $suffix;
    }

    /**
     * This method returns an array containing the internal Controller and the Route of the request.
     * It first searches the controller in the site and if it does not exist then it is searched in the framework.
     *
     * @return array containing Controller and Route
     */
    public static function getInternalController(): ?array {
        return ControllersFactory::getInternalController(self::LOCATIONS);
    }
}
