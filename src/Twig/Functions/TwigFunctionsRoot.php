<?php

namespace FWK\Twig\Functions;

use FWK\Core\Resources\Assets;
use FWK\Core\Resources\Utils;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\Environment as ResourcesEnvironment;
use SDK\Core\Resources\Timer;
use SDK\Dtos\Basket\Basket;
use Twig\Environment;
use Twig\Error\RuntimeError;

/**
 * This is the TwigFunctionsRoot class.
 * This class extends FWK\Twig\Functions\TwigFunctions, see this class.
 * <br>This class is in charge to encapsulate usefull root functions for Twig and provide a method to add them to the Twig Environment you want.
 *
 *
 * @see TwigFunctions
 *
 * @package FWK\Twig\Functions
 */
class TwigFunctionsRoot extends TwigFunctions {

    /**
     * This method adds the Twig functions defined by this class (see them) to the Twig Environment passed by parameter.
     * 
     * @param Environment $twig
     * 
     * Functions
     * @see TwigFunctionsRoot::abort()
     * @see TwigFunctionsRoot::addTimerDebugFlag()
     * @see TwigFunctionsRoot::array_add()
     * @see TwigFunctionsRoot::dump()
     * @see TwigFunctionsRoot::getAssetsFontsPath()
     * @see TwigFunctionsRoot::getAssetsImgPath()
     * @see TwigFunctionsRoot::getAssetsPath()
     * @see TwigFunctionsRoot::getCountryNameByCountryCode()
     * @see TwigFunctionsRoot::getEnvironmentValue()
     * @see TwigFunctionsRoot::getErrorLabelValue()
     * @see TwigFunctionsCore::getFolcsVersion()
     * @see TwigFunctionsRoot::isNumeric()
     * @see TwigFunctionsHtml::isSessionLoggedIn()
     * @see TwigFunctionsHtml::isExpressCheckout()
     * @see TwigFunctionsHtml::isValidEnumKey()
     * @see TwigFunctionsHtml::isValidHexColor()
     * @see TwigFunctionsRoot::ucwords()
     * @see TwigFunctionsRoot::uniqid()
     * Filters
     * @see TwigFunctionsRoot::lcfirst()
     * @see TwigFunctionsRoot::preg_match_all()
     * @see TwigFunctionsRoot::preg_match()
     * @see TwigFunctionsRoot::preg_replace()
     * @see TwigFunctionsRoot::ucfirst()
     * @see TwigFunctionsRoot::usort()
     * @see TwigFunctionsRoot::base64_decode()
     * 
     */
    protected static function addLocalFunctions(Environment $twig): void {
        $twig->addFunction(static::abort());
        $twig->addFunction(static::addTimerDebugFlag());
        $twig->addFunction(static::array_add());
        $twig->addFunction(static::dump());
        $twig->addFunction(static::getAssetsFontsPath());
        $twig->addFunction(static::getAssetsImgPath());
        $twig->addFunction(static::getAssetsPath());
        $twig->addFunction(static::getCountryNameByCountryCode());
        $twig->addFunction(static::getEnvironmentValue());
        $twig->addFunction(static::getErrorLabelValue());
        $twig->addFunction(static::getFolcsVersion());
        $twig->addFunction(static::isNumeric());
        $twig->addFunction(static::isSessionLoggedIn());
        $twig->addFunction(static::isExpressCheckout());
        $twig->addFunction(static::isValidEnumKey());
        $twig->addFunction(static::isValidHexColor());
        $twig->addFunction(static::ucwords());
        $twig->addFunction(static::uniqid());

        $twig->addFilter(static::lcfirst());
        $twig->addFilter(static::preg_match_all());
        $twig->addFilter(static::preg_match());
        $twig->addFilter(static::preg_replace());
        $twig->addFilter(static::ucfirst());
        $twig->addFilter(static::usort());
        $twig->addFilter(static::base64_decode());
    }

    /**
     * This method returns a Twig function named 'dump'. 
     * This Twig function returns the HTML output to dump the given parameter.
     * 
     * @return \Twig\TwigFunction
     */
    private static function dump(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('dump', function ($variable = null, $abort = false) {
            $output = '';
            $output .= "<pre style=\"border: 1px solid #000; overflow: auto; margin: 0.5em;\">";
            $output .= htmlspecialchars(var_export($variable, true));
            $output .= "</pre>\n";
            if ($abort) {
                echo ($output);
                exit();
            } else {
                return new \Twig\Markup($output, CHARSET);
            }
        });
    }

    /**
     * This method returns a Twig function named 'abort'. 
     * This Twig function is usefull to execute an exit from Twig.
     * 
     * @return \Twig\TwigFunction
     */
    private static function abort(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('abort', function () {
            exit();
            return new \Twig\Markup('', CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'getAssetsPath'. 
     * This Twig function returns the assets path for the given environment.
     * 
     * @return \Twig\TwigFunction
     */
    private static function getAssetsPath(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getAssetsPath', function (string $environment = '') {
            return new \Twig\Markup(Assets::getInstance()->getAssetsPath($environment), CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'getAssetsImgPath'. 
     * This Twig function returns the image assets path for the given environment.
     * 
     * @return \Twig\TwigFunction
     */
    private static function getAssetsImgPath(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getAssetsImgPath', function (string $environment = '') {
            return new \Twig\Markup((Assets::getInstance()->getAssetsImagesPath($environment)), CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'getAssetsFontsPath'. 
     * This Twig function returns the image assets path for the given environment.
     * 
     * @return \Twig\TwigFunction
     */
    private static function getAssetsFontsPath(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getAssetsFontsPath', function (string $environment = '') {
            return new \Twig\Markup((Assets::getInstance()->getAssetsFontsPath($environment)), CHARSET);
        });
    }

    /**
     * This method checks if the given parameter is a number or not.
     * 
     * @return \Twig\TwigFunction
     */
    private static function isNumeric(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('isNumeric', function ($str): bool {
            return is_numeric($str);
        });
    }

    /**
     * This method returns a Twig function named 'getCountryNameByCountryCode'. 
     * That Twig function returns the country name for the given country code.
     * 
     * @return \Twig\TwigFunction
     */
    private static function getCountryNameByCountryCode(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getCountryNameByCountryCode', function ($countryCode): string {
            return is_null($countryCode) ? '' : Utils::getCountryNameByCountryCode($countryCode);
        });
    }

    /**
     * This method returns a Twig function named 'getErrorLabelValue'. 
     * That Twig function returns the label related to a element with error.
     * 
     * @return \Twig\TwigFunction
     */
    private static function getErrorLabelValue(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getErrorLabelValue', function (Element $element): string {
            return  Utils::getErrorLabelValue($element);
        });
    }

    /**
     * This method returns a Twig function named 'getFolcsVersion'.  
     * This Twig function returns an string with the FolcsVersion value depending given cacheable value.
     * from it the parameters given by parameter. 
     * 
     * @return \Twig\TwigFunction
     */
    private static function getFolcsVersion(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getFolcsVersion', function (bool $cacheable = false) {
            return new \Twig\Markup(Utils::getFolcsVersion($cacheable), CHARSET);
        });
    }

    /**
     * Perform a global regular expression match.
     *
     * @return \Twig\TwigFilter
     */
    private static function preg_match_all(): \Twig\TwigFilter {
        return new \Twig\TwigFilter('preg_match_all', function (string $subject, string $pattern): array {
            $result = [];
            preg_match_all($pattern, $subject, $result);
            return $result[0];
        });
    }

    /**
     * This method performs a regular expression search and replacement.
     *
     * @return \Twig\TwigFilter
     */
    private static function preg_replace(): \Twig\TwigFilter {
        return new \Twig\TwigFilter('preg_replace', function ($subject, $pattern, $replacement) {
            return preg_replace($pattern, $replacement, $subject);
        });
    }

    /**
     * This method performs a regular expression match.
     *
     * @return \Twig\TwigFilter
     */
    private static function preg_match(): \Twig\TwigFilter {
        return new \Twig\TwigFilter('preg_match', function ($subject, $pattern) {
            $matches = array();
            preg_match($pattern, $subject, $matches);
            return $matches;
        });
    }

    /**
     * This method capitalizes the first character of a string.
     *
     * @return \Twig\TwigFilter
     */
    private static function ucfirst(): \Twig\TwigFilter {
        return new \Twig\TwigFilter('ucfirst', function ($string) {
            return ucfirst($string);
        });
    }

    /**
     * This method make a string's first character lowercase.
     *
     * @return \Twig\TwigFilter
     */
    private static function lcfirst(): \Twig\TwigFilter {
        return new \Twig\TwigFilter('lcfirst', function ($string) {
            return lcfirst($string);
        });
    }

    /**
     * This method sorts an array by values using a user-defined comparison function.
     *
     * @return \Twig\TwigFilter
     */
    private static function usort(): \Twig\TwigFilter {
        return new \Twig\TwigFilter('usort', function ($array, $fn = null) {
            usort($array, is_null($fn) ? function ($a, $b) {
                return Utils::removeAccents($a) <=> Utils::removeAccents($b);
            } : $fn);
            return $array;
        });
    }

    /**
     * This method returns a Twig function named 'base64_decode'. 
     * This Twig function decodes the given string in base64 format.
     *
     * @return \Twig\TwigFilter
     */
    private static function base64_decode(): \Twig\TwigFilter {
        return new \Twig\TwigFilter('base64_decode', function ($string) {
            return base64_decode($string);
        });
    }

    /**
     * This method returns a Twig function named 'ucwords'. 
     * This Twig function uppercases the first character of each word of the given string 
     * (words delimited by the character 'cammelPrefix') and returns the uppercased string eliminating the delimiters.
     * 
     * @return \Twig\TwigFunction
     */
    private static function ucwords(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('ucwords', function (?string $string, string $cammelPrefix = '_') {
            $string ??= '';
            $output = ucwords($string, $cammelPrefix);
            $output = str_replace($cammelPrefix, '', $output);
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'uniqid'. 
     * The uniqid() function generates a unique ID based on the microtime (the current time in microseconds).
     * 
     * @return \Twig\TwigFunction
     */
    private static function uniqid(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('uniqid', function () {
            return new \Twig\Markup(uniqid(), CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'getEnvironmentVariable'. 
     * That function returns the environment variable value fron de given variable name given
     *
     * @return \Twig\TwigFunction
     */
    private static function getEnvironmentValue(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getEnvironmentValue', function (string $variableName) {
            return ResourcesEnvironment::get($variableName);
        });
    }

    /**
     * This method returns a Twig function named 'array_add'. 
     * That function returns the array given after add the value with a specific key if it is given
     *
     * @return \Twig\TwigFunction
     */
    private static function array_add(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('array_add', function (array $array, $value, string $key = '') {
            if (strlen($key)) {
                $array[$key] = $value;
            } else {
                $array[] = $value;
            }
            return $array;
        });
    }

    /**
     * This method returns a Twig function named 'addTimerDebugFlag'. 
     * That function add a new flag to timerDebug
     *
     * @return \Twig\TwigFunction
     */
    private static function addTimerDebugFlag(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('addTimerDebugFlag', function (string $flag, bool $start) {
            $sufix = $start ? Timer::START_SUFFIX : Timer::END_SUFFIX;
            Utils::addTimerDebugFlag($flag, $sufix);
        });
    }

    /**
     * This method returns if the current session is logged in.
     *
     * @return \Twig\TwigFunction
     */
    private static function isSessionLoggedIn(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('isSessionLoggedIn', function (): bool {
            return Utils::isSessionLoggedIn();
        });
    }

    /**
     * This method returns if the given basket is express checkout.
     *
     * @return \Twig\TwigFunction
     */
    private static function isExpressCheckout(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('isExpressCheckout', function (?Basket $basket): bool {
            return Utils::isExpressCheckout($basket);
        });
    }

    /**
     * This method returns the possible values of the given enumerate class.
     *
     * @return \Twig\TwigFunction
     */
    private static function isValidEnumKey(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('isValidEnumKey', function (string $enumClass, ?string $value): bool {
            if (!class_exists($enumClass)) throw new RuntimeError(sprintf('isValidEnumKey error, "%s" class not exists.', $enumClass));
            if ($value === null) throw new RuntimeError(sprintf('isValidEnumKey error, value passed to class "%s" is null.', $enumClass));
            return $enumClass::isValid($value);
        });
    }

    /**
     * This method returns if the given string is a valid RGB hexadecimal color.
     *
     * @return \Twig\TwigFunction
     */
    private static function isValidHexColor(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('isValidHexColor', function (string $hex): bool {
            return preg_match('/^#(([a-f0-9]{3}){1,2})$/i', $hex) === 1;
        });
    }
}
