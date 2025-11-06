<?php

namespace FWK\Core\Resources;

use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Resources\Timer;
use SDK\Dtos\Documents\PDFDocument;
use FWK\Core\FilterInput\FilterInputHandler;
use SDK\Core\Dtos\Element;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Services;
use FWK\Services\LmsService;
use SDK\Application;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Dtos\ErrorField;
use SDK\Core\Enums\Device;
use SDK\Core\Resources\Cookie;
use SDK\Core\Resources\Environment;
use SDK\Core\Resources\RedisCache;
use SDK\Dtos\Basket\Basket;
use SDK\Dtos\Country;
use SDK\Dtos\User\User;
use SDK\Enums\RedisKey;
use SDK\Enums\UserKeyCriteria;
use SDK\Dtos\Basket\BasketRows\Option as BasketRowOption;
use SDK\Enums\AccountType;
use SDK\Enums\BasketWarningCode;
use SDK\Enums\CompanyRoleType;
use SDK\Enums\MasterType;
use SDK\Enums\OptionType;

/**
 * This is the Utils class.
 * This class is the responsible to encapsulate the various useful generic methods.
 *
 * @abstract
 *
 * @see Utils::addParamsToRequest()
 * @see Utils::addTimerDebugFlag()
 * @see Utils::addTimerDebugTag()
 * @see Utils::calculateLocale()
 * @see Utils::cleanHtmlTags()
 * @see Utils::deleteParamsFromRequest()
 * @see Utils::getCamelFromSnake()
 * @see Utils::getCountries()
 * @see Utils::getDirMap()
 * @see Utils::getErrorLabelValue()
 * @see Utils::getPdfContent()
 * @see Utils::getQueryStringParameters()
 * @see Utils::getSnakeFromCamel()
 * @see Utils::getUserName()
 * @see Utils::isSessionLoggedIn()
 * @see Utils::minifyHtml()
 * @see Utils::outputJsonHtmlString()
 * @see Utils::outputTimerDebug()
 * @see Utils::parseArrayToPathParameters()
 * @see Utils::parseBasketRowOptions()
 * @see Utils::removeAccents()
 * @see Utils::setValidateType()
 * @see Utils::sortArrayObjects()
 * @see Utils::stopTimerDebug()
 *
 * @package FWK\Core\Resources
 */
abstract class Utils {

    public const TYPE_ARRAY = 'array';

    public const TYPE_BOOLEAN = 'boolean';

    public const TYPE_FLOAT = 'float';

    public const TYPE_INT = 'int';

    public const TYPE_NUMERIC = 'numeric';

    public const TYPE_OBJECT = 'object';

    public const TYPE_OBJECT_CLASS = 'object class';

    public const TYPE_RESOURCE = 'resource';

    public const TYPE_STRING = 'string';

    public const TYPE_CALLABLE = 'callable';

    public const SORT_ASC = 'asc';

    public const SORT_DESC = 'desc';

    private static $debugTimerFlag = [
        'debugTimer'
    ];

    private static $debugTimerStarted = False;

    /**
     * This method checks that the given value corresponds to the given data type.
     * If the ckeck is ok then it returns the same given value, otherwise it throws a CommerceException of type CommerceException::UTILS_VALIDATE_TYPE_ERROR.
     *
     * Allowed values for type parameter:
     * <ul>
     * <li>Utils::TYPE_ARRAY</li>
     * <li>Utils::TYPE_BOOLEAN</li>
     * <li>Utils::TYPE_FLOAT</li>
     * <li>Utils::TYPE_INT</li>
     * <li>Utils::TYPE_NUMERIC</li>
     * <li>Utils::TYPE_OBJECT</li>
     * <li>Utils::TYPE_OBJECT_CLASS</li>
     * <li>Utils::TYPE_RESOURCE</li>
     * <li>Utils::TYPE_STRING</li>
     * <li>Utils::TYPE_CALLABLE</li>
     * </ul>
     *
     * @param string $type
     * @param Mixed $value
     * @param string $className
     *            It has to be provided only in case of type = Utils::TYPE_OBJECT_CLASS to indicate the name of the class.
     *            
     * @throws CommerceException
     *
     * @return Mixed
     */
    public static function setValidateType(string $type, $value, $className = '') {
        $validateFunction = null;

        switch ($type) {
            case self::TYPE_ARRAY:
                $validateFunction = 'is_array';
                break;
            case self::TYPE_BOOLEAN:
                $validateFunction = 'is_bool';
                break;
            case self::TYPE_CALLABLE:
                $validateFunction = 'is_callable';
                break;
            case self::TYPE_FLOAT:
                $validateFunction = 'is_float';
                break;
            case self::TYPE_INT:
                $validateFunction = 'is_int';
                break;
            case self::TYPE_NUMERIC:
                $validateFunction = 'is_numeric';
                break;
            case self::TYPE_OBJECT:
                $validateFunction = 'is_object';
                break;
            case self::TYPE_OBJECT_CLASS:
                $validateFunction = 'get_class';
                break;
            case self::TYPE_RESOURCE:
                $validateFunction = 'is_resource';
                break;
            case self::TYPE_STRING:
                $validateFunction = 'is_string';
                break;
            default:
                throw new CommerceException('Indefined validate function for: ' . $type . ' type', CommerceException::UTILS_UNDEFINED_VALIDATE_TYPE);
        }

        if ($validateFunction($value) || ($type === self::TYPE_OBJECT_CLASS && $validateFunction($value) === $className)) {
            return $value;
        } else {
            $message = '';
            if ($type === self::TYPE_OBJECT_CLASS) {
                $message = 'Value must be a class "' . $className . '" ' . get_class($value) . ' given';
            } else {
                $message = 'Value must be ' . $type . ' type ' . getType($value) . ' given';
            }
            throw new CommerceException($message, CommerceException::UTILS_VALIDATE_TYPE_ERROR);
        }
    }

    /**
     * This method minifies the given html text and returns it.
     *
     * @param string $html
     *
     * @return string
     */
    public static function minifyHtml(string $html): string {
        if (Environment::get('DEVEL')) {
            return $html;
        }

        $search = [
            '/(\n|^)(\x20+|\t)/',
            '/(\n|^)\/\/(.*?)(\n|$)/',
            '/\n/',
            '/\<\!--.*?-->/',
            '/(\x20+|\t)/', // Delete multispace (Without \n)
            '/\>\s+\</', // strip whitespaces between tags
            '/(\"|\')\s+\>/', // strip whitespaces between quotation ("') and end tags
            '/=\s+(\"|\')/'
        ]; // strip whitespaces between = "'
        $replace = [
            "\n",
            "\n",
            " ",
            "",
            " ",
            "><",
            "$1>",
            "=$1"
        ];
        return preg_replace($search, $replace, $html);
    }

    /**
     * This method returns the query string parameters.
     *
     * @return array
     */
    public static function getQueryStringParameters(): array {
        $queryParams = [];
        $queryString = str_replace('%26', '{{ampersand}}', $_SERVER['QUERY_STRING']);
        $query = explode('&', urldecode($queryString));
        foreach ($query as $param) {
            $param = str_replace('{{ampersand}}', '&', $param);
            if (strpos($param, '=') === false) {
                $param .= '=';
            }
            list($name, $value) = explode('=', $param, 2);
            if (strlen($value)) {
                $queryParams[$name][] = $value;
            }
        }
        return $queryParams;
    }

    /**
     * This function returns an string with the params of the request concatenating to it the
     * 'newParams' parameters given by parameter as part of the request parameters.
     *
     * @param ?array $newParams
     *
     * @return array
     */
    public static function addParamsToRequest(?array $newParams): array {
        $newParams ??= [];
        $queryParams = Utils::getQueryStringParameters();
        if (isset($queryParams[URL_ROUTE])) {
            unset($queryParams[URL_ROUTE]);
        }
        foreach ($newParams as $param => $newValue) {
            if (strlen($param)) {
                if (strlen($newValue)) {
                    $queryParams[$param] = [];
                    $queryParams[$param][] = $newValue;
                } else {
                    unset($queryParams[$param]);
                }
            }
        }
        return $queryParams;
    }

    /**
     * This function returns an string with the params of the request eliminating
     * from it the parameters given by parameter.
     *
     * @param ?array $parameters
     *
     * @return array
     */
    public static function deleteParamsFromRequest(?array $parameters): array {
        $parameters ??= [];
        $queryParams = Utils::getQueryStringParameters();
        $deteleItems = [];
        foreach (array_keys($queryParams) as $qryParamName) {
            $dinamic = FilterInputHandler::getAvailableDinamicParam($qryParamName);
            if ((in_array($qryParamName, $parameters) && $dinamic === null) || ($dinamic !== null && in_array($dinamic[0], $parameters))) {
                $deteleItems[] = $qryParamName;
            }
        }
        foreach ($deteleItems as $deteleItem) {
            unset($queryParams[$deteleItem]);
        }
        return $queryParams;
    }

    /**
     * This function returns an string containing the given parameters in the format of
     * an url request (Example: ?param1=value1&param2=value2).
     *
     * @param array $queryParams
     *
     * @return string
     */
    public static function parseArrayToPathParameters(?array $queryParams): string {
        $queryParams ??= [];
        $parameters = [];
        foreach ($queryParams as $name => $values) {
            foreach ($values as $value) {
                $parameters[] = urlencode($name) . '=' . urlencode($value);
            }
        }
        $queryResult = implode('&', $parameters);

        return (strlen($queryResult) ? '?' : '') . $queryResult;
    }

    /**
     * This function returns an string containing the given basket row options
     *
     * @param BasketRowOption[] $options
     *
     * @return string
     */
    public static function parseBasketRowOptions(array $options): string {
        $parseOptions = '';
        $language = Language::getInstance();
        foreach ($options as $option) {
            $values = [];
            if ($option->getType() == OptionType::BOOLEAN) {
                $values[] = $option->getValue() ? $language->getLabelValue(LanguageLabels::YES) : $language->getLabelValue(LanguageLabels::NO);
            } elseif ($option->getType() == OptionType::DATE) {
                $values[] = (new DateTimeFormatter())->getFormattedDate($option->getValue());
            } elseif ($option->getType() == OptionType::ATTACHMENT) {
                foreach ($option->getValues() as $value) {
                    $values[] = explode('_', $value, 2)[1];
                }
            } elseif (in_array($option->getType(), [OptionType::SHORT_TEXT, OptionType::LONG_TEXT])) {
                $values[] = $option->getValue();
            } elseif (in_array($option->getType(), [OptionType::SINGLE_SELECTION, OptionType::SINGLE_SELECTION_IMAGE, OptionType::SELECTOR])) {
                $values[] = $option->getValue()->getValue();
            } elseif (in_array($option->getType(), [OptionType::MULTIPLE_SELECTION, OptionType::MULTIPLE_SELECTION_IMAGE])) {
                foreach ($option->getValueList() as $value) {
                    $values[] = $value->getValue();
                }
            }
            $parseOptions .= (empty($parseOptions) ? '' : ', ') . $option->getName() . ' ' . implode(', ', $values);
        }
        return $parseOptions;
    }


    /**
     * This method adds a debug timer flag.
     *
     * @param string $flag
     *            Name of the flag.
     * @param string $sufix
     *            Possible values: Timer::START_SUFFIX, Timer::END_SUFFIX.
     */
    public static function addTimerDebugFlag(string $flag, string $sufix) {
        if (TIMER_DEBUG || LcFWK::getLoggerTimerEnabled()) {
            if (!self::$debugTimerStarted) {
                Timer::getTimer('debugTimer')->start();
                self::$debugTimerStarted = true;
            }
            if ($sufix === Timer::START_SUFFIX) {
                self::$debugTimerFlag[$flag] = $flag;
            }
            Timer::getTimer('debugTimer')->addFlag(implode('_', self::$debugTimerFlag) . $sufix, true);
            if ($sufix === Timer::END_SUFFIX) {
                unset(self::$debugTimerFlag[$flag]);
            }
        }
    }

    /**
     * This method adds a debug timer tag.
     *
     * @param string $tag
     * @param string $value
     */
    public static function addTimerDebugTag(string $tag, string $value) {
        if (TIMER_DEBUG || LcFWK::getLoggerTimerEnabled()) {
            Timer::getTimer('debugTimer')->addTag($tag, $value);
        }
    }

    /**
     * This method stops the debug timer.
     */
    public static function stopTimerDebug() {
        if ((TIMER_DEBUG || LcFWK::getLoggerTimerEnabled()) && self::$debugTimerStarted) {
            Timer::getTimer('debugTimer')->stop(LcFWK::getLoggerTimerEnabled());
            self::$debugTimerStarted = false;
        }
    }

    /**
     * This method outputs the debug timer.
     */
    public static function outputTimerDebug() {
        if (Response::getType() == Response::TYPE_JS) {
            echo 'var debugTimer_TimeBetweenFlags = ' . json_encode(Timer::getTimer('debugTimer')->getTimeBetweenFlags()) . ';';
            echo 'var debugTimer_Total = ' . json_encode(Timer::getTimer('debugTimer')->getLoggedTime()) . ';';
        } else if (Response::getType() == Response::TYPE_JSON) {
            echo ',"debugTimer":{';
            echo '"TimeBetweenFlags" : ' . json_encode(Timer::getTimer('debugTimer')->getTimeBetweenFlags()) . ', ';
            echo '"Total" : ' . json_encode(Timer::getTimer('debugTimer')->getLoggedTime()) . ' ';
            echo '}';
            echo '}'; // add an extra "}" removed in Response.php
        } else {
            echo '<br>debugTimer_TimeBetweenFlags<br>';
            echo '<pre>';
            var_dump(Timer::getTimer('debugTimer')->getTimeBetweenFlags());
            echo '</pre>';
            echo '<br>debugTimer Total<br>';
            echo '<pre>';
            var_dump(Timer::getTimer('debugTimer')->getLoggedTime());
            echo '</pre>';
        }
    }

    /**
     * This method returns a mapping of the given directory path.
     *
     * @param string $dir
     *            Path to analyze.
     *            
     * @return array
     */
    public static function getDirMap(string $dir): array {
        $folder = opendir($dir);
        $dirTree = [];
        while ($file = readdir($folder)) {
            if ($file != "." && $file != "..") {
                $pathfile = $dir . '/' . $file;
                if (is_dir($pathfile)) {
                    $dirTree[$file] = self::getDirMap($pathfile);
                } else {
                    $dirTree[] = $file;
                }
            }
        }
        closedir($folder);
        return $dirTree;
    }

    /**
     * This method returns a string in camel format from a string in snake format .
     *
     * @param string $snakeName
     *            string in snake mode.
     *            
     * @return string
     */
    public static function getCamelFromSnake(string $snakeName, $separator = "_") {
        return str_replace($separator, '', ucwords(strtolower($snakeName), $separator));
    }

    /**
     * This method returns a string in snake format from a string in camel format .
     *
     * @param string $CamelName
     *            string in snake mode.
     *            
     * @return array
     */
    public static function getSnakeFromCamel(string $CamelName, $separator = "_") {
        return implode($separator, array_map('strtoupper', preg_split('/(?=[A-Z])/', $CamelName)));
    }

    /**
     * This method returns the given value in json format with html compatibility.
     *
     * @return string
     */
    public static function outputJsonHtmlString($value = null): string {
        $output = '';
        if ($value != null) {
            $output = json_encode($value, JSON_ENCODE_FILTER);
            $output = preg_replace('/\'/', '&#39;', $output);
            $output = preg_replace('/\n/m', '\n', $output);
            $output = preg_replace('/&#xD;/m', '\n', $output);
            $output = preg_replace('/\r/m', '\r', $output);
            $output = preg_replace('/&quot;/m', '\"', $output);

            $unicode = "\u{2028}"; // LSEP (char)
            $output = str_replace($unicode, ' ', $output);
        }
        return $output;
    }

    /**
     * This method returns the string given without html tags.
     *
     * @return string
     */
    public static function cleanHtmlTags(string $text): string {
        return preg_replace('(<([^>]+)>)', '', $text);
    }

    /**
     * This method returns true if the session user is logged, false otherwise.
     *
     * @param ?Session $session
     *
     * @return bool
     */
    public static function isSessionLoggedIn(?Session $session = null): bool {
        if (is_null($session)) {
            $session = Session::getInstance();
        }
        return self::isUserLoggedIn($session->getUser());
    }

    /**
     * This method returns true if the user is logged, false otherwise.
     *
     * @param ?User $user
     *
     * @return bool
     */
    public static function isUserLoggedIn(?User $user = null): bool {
        if (is_null($user)) {
            $user = Session::getInstance()->getUser();
        }
        if ($user->getId() > 0 && $user->getActive() && $user->getVerified()) {
            return true;
        }
        return false;
    }

    /**
     * This method returns true if the session user is sales agent, false otherwise.
     *
     * @param ?Session $session
     *
     * @return bool
     */
    public static function isSalesAgent(?Session $session): bool {
        if (!is_null($session) && $session->getUser()->getSalesAgent()) {
            return true;
        }
        return false;
    }

    /**
     * This method returns true if the session user is company accounts, false otherwise.
     *
     * @return bool
     */
    public static function isCompanyAccounts(): bool {
        return LmsService::getAdvcaLicense();
    }

    /**
     * This method returns true if the account update is blocked, false otherwise.
     *
     * @param bool $thisAccountUpdatePermissions
     *
     * @return bool
     */
    public static function isAccountUpdateBlocked(bool $thisAccountUpdatePermissions): bool {
        $accountRegisteredUser = Session::getInstance()?->getBasket()?->getAccountRegisteredUser();
        $account = Session::getInstance()?->getBasket()?->getAccount();
        return (
            $account != null &&
            in_array($account->getType(), AccountType::getCompanyTypes(), true) &&
            $accountRegisteredUser != null &&
            !$accountRegisteredUser->isMaster() &&
            (
                $accountRegisteredUser?->getType() == MasterType::EMPLOYEE &&
                $accountRegisteredUser?->getRole()?->getType() == CompanyRoleType::CUSTOM
            ) &&
            !$thisAccountUpdatePermissions &&
            LmsService::getAdvcaLicense());
    }

    /**
     * This method returns true if the basket is express checkout, false otherwise.
     *
     * @param ?Session $session
     *
     * @return bool
     */
    public static function isExpressCheckout(?Basket $basket): bool {
        if (!is_null($basket) && !is_null($basket->getExpressCheckout())) {
            return true;
        }
        return false;
    }

    /**
     * This method returns true if the session user is sales agent, false otherwise.
     *
     * @param ?Session $session
     *
     * @return bool
     */
    public static function isSimulatedUser(?Session $session): bool {
        if ($session !== null && $session->getUser()->getUserAdditionalInformation() !== null && $session->getUser()->getUserAdditionalInformation()->getSimulatedUser()) {
            return true;
        }
        return false;
    }

    /**
     * This method returns the pdf content from a PDFDocument.
     *
     * @param PDFDocument $pdfDocument
     *
     * @return string
     */
    public static function getPdfContent(PDFDocument $pdfDocument): string {
        $bin = base64_decode($pdfDocument->getContent(), true);
        if (strpos($bin, '%PDF') !== 0) {
            throw new CommerceException(self::class . '. Missing the PDF file signature', CommerceException::UTILS_MISSING_PDF_SIGNATURE);
        }
        return $bin;
    }

    public static function removeAccents(string $string) {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A',
            chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A',
            chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A',
            chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C',
            chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E',
            chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E',
            chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I',
            chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I',
            chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O',
            chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O',
            chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O',
            chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U',
            chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U',
            chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's',
            chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a',
            chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a',
            chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a',
            chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e',
            chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e',
            chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i',
            chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i',
            chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n',
            chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o',
            chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o',
            chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o',
            chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u',
            chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u',
            chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A',
            chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A',
            chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A',
            chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C',
            chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C',
            chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C',
            chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C',
            chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D',
            chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D',
            chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E',
            chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E',
            chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E',
            chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E',
            chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E',
            chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G',
            chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G',
            chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G',
            chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G',
            chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H',
            chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H',
            chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I',
            chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I',
            chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I',
            chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I',
            chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I',
            chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ',
            chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J',
            chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K',
            chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k',
            chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l',
            chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l',
            chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l',
            chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l',
            chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l',
            chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n',
            chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n',
            chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n',
            chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n',
            chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O',
            chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O',
            chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O',
            chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE',
            chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R',
            chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R',
            chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R',
            chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S',
            chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S',
            chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S',
            chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S',
            chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T',
            chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T',
            chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T',
            chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U',
            chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U',
            chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U',
            chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U',
            chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U',
            chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U',
            chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W',
            chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y',
            chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y',
            chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z',
            chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z',
            chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z',
            chr(197) . chr(191) => 's'
        );

        return strtr($string, $chars);
    }

    /**
     * This method finds the language label value from the language label key specified.
     * 
     * @param string $languageLabel
     * @param bool $title
     * @param string $errorCode
     * @param Language $language
     * @param int $fieldsInErrorMessage
     * @param string $errorMessage
     */
    private static function addLabelToErrorMessage(string $languageLabel, bool $title, string $errorCode, $language, string &$fieldsInErrorMessage, string &$errorMessage) {
        $labels = $language->getLabels();
        if (!array_key_exists($languageLabel, $labels)) {
            throw new CommerceException('Undefined error code: errorCode ' . $errorCode, CommerceException::UTILS_UNDEFINED_ERROR_CODE_LABEL);
        } else {
            $labelForError = $language->getLabelValue($labels[$languageLabel]);
            if (strlen($labelForError)) {
                if ($title) {
                    $errorMessage = $labelForError;
                } else {
                    if ($fieldsInErrorMessage == 0) {
                        $errorMessage .= '<ul>';
                    }
                    $errorMessage .= '<li>' . $labelForError . '</li>';
                    $fieldsInErrorMessage++;
                }
            }
        }
    }

    /**
     * This method returns a string with all the error labels concatenated.
     * 
     * @param Element|ElementCollection $response
     * @param string $fieldPrefix
     * @return string
     */
    public static function getErrorLabelValue(Element|ElementCollection $response, string $fieldPrefix = ''): string {
        $language = Language::getInstance();
        $errorMessage = $language->getLabelValue(LanguageLabels::ERROR);
        $errorMessageLabels = '';
        $error = $response->getError();

        if (!is_null($error)) {
            $errorMessage = $error->getMessage();
            if (!strpos($error->getCode(), '-')) {
                $errorCode = $error->getCode();
            } else {
                $errorCode = substr($error->getCode(), strpos($error->getCode(), '-') + 1);
            }
            $errorCode = strtoupper(str_replace('.', '_', $errorCode));
            if ($errorCode === 'USER_EXISTS') {
                $errorCode .= '_' . Application::getInstance()->getEcommerceSettings()->getUserAccountsSettings()->getUserKeyCriteria();
            }
            $fieldsInErrorMessage = 0;
            $languageLabel = 'ERROR_CODE_' . $errorCode;
            self::addLabelToErrorMessage($languageLabel, true, $errorCode, $language, $fieldsInErrorMessage, $errorMessage);
            self::generateErrorMessages($error->getFields(), $fieldPrefix, $errorCode, $fieldsInErrorMessage, $errorMessageLabels);
            $errorMessageLabels .= ($fieldsInErrorMessage > 0) ? '</ul>' : '';
        }

        return $errorMessage . $errorMessageLabels;
    }

    /**
     * This method receives an array of ErrorField objects and calls the function addLabelToErrorMessage
     *
     * @param ErrorField[] $fields
     * @param string $fieldPrefix
     * @param string $errorCode
     * @param string $fieldsInErrorMessage
     * @param string $errorMessage
     *
     */
    public static function generateErrorMessages($fields, string $fieldPrefix, string $errorCode,  string &$fieldsInErrorMessage, string &$errorMessage) {
        $languageLabel = 'ERROR_CODE_' . $errorCode;
        $language = Language::getInstance();
        foreach ($fields as $field) {
            if (!($field instanceof ErrorField)) {
                throw new CommerceException('Each element of fields must be a instance of ' . ErrorField::class . '. ' . ' Instance of ' . get_class($field) . ' sended.', CommerceException::VIEW_HELPER_INVALID_ARGUMENT);
            }
            $languageLabelField = $languageLabel . (strlen($fieldPrefix) ? '_' : '') . $fieldPrefix;
            $languageLabelField .=  '_' . str_replace('.', '_', self::getSnakeFromCamel($field->getName()));
            self::addLabelToErrorMessage($languageLabelField, false, $errorCode, $language, $fieldsInErrorMessage, $errorMessage);
            $languageLabelField .= '_' . $field->getType();
            self::addLabelToErrorMessage($languageLabelField, false, $errorCode, $language, $fieldsInErrorMessage, $errorMessage);
        }
    }

    /**
     * This method returns the locale string if it is found (based on the given country and language) in the list of locales supported by the bundle (see ResourceBundle::getLocales()), otherwise it returns $country.
     *
     * @param string $country
     * @param string $language
     *
     * @return string
     *
     * @see \ResourceBundle::getLocales()
     */
    public static function calculateLocale(string $country, string $language) {
        $locale = $language . '_' . $country;
        if (array_search($locale, \ResourceBundle::getLocales('')) !== false) {
            return $locale;
        }
        return $language;
    }

    /**
     * This method returns the pattern for the given locale.
     *
     * @param string $locale
     *
     * @return string
     *
     * @see \ResourceBundle::getLocales()
     */
    public static function getJSDatePatternByLocale(string $locale) {
        $formats = [
            'af_ZA' => 'YYYY/MM/DD',
            'am_ET' => 'D/M/YYYY',
            'ar_AE' => 'DD/MM/YYYY',
            'ar_BH' => 'DD/MM/YYYY',
            'ar_DZ' => 'DD-MM-YYYY',
            'ar_EG' => 'DD/MM/YYYY',
            'ar_IQ' => 'DD/MM/YYYY',
            'ar_JO' => 'DD/MM/YYYY',
            'ar_KW' => 'DD/MM/YYYY',
            'ar_LB' => 'DD/MM/YYYY',
            'ar_LY' => 'DD/MM/YYYY',
            'ar_MA' => 'DD-MM-YYYY',
            'ar_OM' => 'DD/MM/YYYY',
            'ar_QA' => 'DD/MM/YYYY',
            'ar_SA' => 'DD/MM/YY',
            'ar_SY' => 'DD/MM/YYYY',
            'ar_TN' => 'DD-MM-YYYY',
            'ar_YE' => 'DD/MM/YYYY',
            'ar' => 'DD/MM/YY',
            'arn_CL' => 'DD-MM-YYYY',
            'as_IN' => 'DD-MM-YYYY',
            'az_AZ' => 'DD.MM.YYYY',
            'az_Cyrl_AZ' => 'DD.MM.YYYY',
            'az_Latn_AZ' => 'DD.MM.YYYY',
            'ba_RU' => 'DD.MM.YY',
            'be_BY' => 'DD.MM.YYYY',
            'bg_BG' => 'DD.M.YYYY',
            'bg' => 'DD.M.YYYY',
            'bn_BD' => 'DD-MM-YY',
            'bn_IN' => 'DD-MM-YY',
            'bo_CN' => 'YYYY/M/D',
            'br_FR' => 'DD/MM/YYYY',
            'bs_BA' => 'D.M.YYYY',
            'bs_Cyrl_BA' => 'D.M.YYYY',
            'bs_Latn_BA' => 'D.M.YYYY',
            'bs' => 'D.M.YYYY',
            'ca_ES_EURO' => 'DD/MM/YYYY',
            'ca_ES' => 'DD/MM/YYYY',
            'ca' => 'DD/MM/YYYY',
            'co_FR' => 'DD/MM/YYYY',
            'cs_CZ' => 'D.M.YYYY',
            'cs' => 'D.M.YYYY',
            'cy_GB' => 'DD/MM/YYYY',
            'cy' => 'DD/MM/YYYY',
            'da_DK' => 'DD-MM-YYYY',
            'da' => 'DD-MM-YYYY',
            'de_AT_EURO' => 'DD.MM.YYYY',
            'de_AT' => 'DD.MM.YYYY',
            'de_CH' => 'DD.MM.YYYY',
            'de_DE_EURO' => 'DD.MM.YYYY',
            'de_DE' => 'DD.MM.YYYY',
            'de_LI' => 'DD.MM.YYYY',
            'de_LU_EURO' => 'DD.MM.YYYY',
            'de_LU' => 'DD.MM.YYYY',
            'de' => 'DD.MM.YYYY',
            'dsb_DE' => 'D. M. YYYY',
            'dv_mV' => 'DD/MM/YY',
            'el_GR' => 'D/M/YYYY',
            'el' => 'D/M/YYYY',
            'en_029' => 'MM/DD/YYYY',
            'en_AU' => 'd/MM/YYYY',
            'en_BB' => 'M/D/YYYY',
            'en_BM' => 'M/D/YYYY',
            'en_BZ' => 'DD/MM/YYYY',
            'en_CA' => 'DD/MM/YYYY',
            'en_GB' => 'DD/MM/YYYY',
            'en_GH' => 'M/D/YYYY',
            'en_ID' => 'M/D/YYYY',
            'en_IE_EURO' => 'M/D/YYYY',
            'en_IE' => 'DD/MM/YYYY',
            'en_IN' => 'DD-MM-YYYY',
            'en_Jm' => 'DD/MM/YYYY',
            'en_mY' => 'D/M/YYYY',
            'en_NG' => 'M/D/YYYY',
            'en_NZ' => 'd/MM/YYYY',
            'en_PH' => 'M/D/YYYY',
            'en_PK' => 'M/D/YYYY',
            'en_SG' => 'D/M/YYYY',
            'en_TT' => 'DD/MM/YYYY',
            'en_US' => 'M/D/YYYY',
            'en_ZA' => 'YYYY/MM/DD',
            'en_ZW' => 'M/D/YYYY',
            'es_AR' => 'DD/MM/YYYY',
            'es_BO' => 'DD/MM/YYYY',
            'es_CL' => 'DD-MM-YYYY',
            'es_CO' => 'DD/MM/YYYY',
            'es_CR' => 'DD/MM/YYYY',
            'es_DO' => 'DD/MM/YYYY',
            'es_EC' => 'DD/MM/YYYY',
            'es_ES_EURO' => 'DD/MM/YYYY',
            'es_ES' => 'DD/MM/YYYY',
            'es_GT' => 'DD/MM/YYYY',
            'es_HN' => 'DD/MM/YYYY',
            'es_mX' => 'DD/MM/YYYY',
            'es_NI' => 'DD/MM/YYYY',
            'es_PA' => 'MM/DD/YYYY',
            'es_PE' => 'DD/MM/YYYY',
            'es_PR' => 'DD/MM/YYYY',
            'es_PY' => 'DD/MM/YYYY',
            'es_SV' => 'DD/MM/YYYY',
            'es_US' => 'M/D/YYYY',
            'es_UY' => 'DD/MM/YYYY',
            'es_VE' => 'DD/MM/YYYY',
            'es' => 'DD/MM/YYYY',
            'et_EE' => 'd.mm.yyyy',
            'et' => 'd.mm.yyyy',
            'eu_ES' => 'YYYY/MM/DD',
            'eu' => 'YYYY/MM/DD',
            'fa_IR' => 'MM/DD/YYYY',
            'fi_FI_EURO' => 'D.M.YYYY',
            'fi_FI' => 'D.M.YYYY',
            'fi' => 'D.M.YYYY',
            'fil_PH' => 'M/D/YYYY',
            'fo_FO' => 'DD-MM-YYYY',
            'fr_BE' => 'd/MM/YYYY',
            'fr_CA' => 'YYYY-MM-DD',
            'fr_CH' => 'DD.MM.YYYY',
            'fr_FR_EURO' => 'DD/MM/YYYY',
            'fr_FR' => 'DD/MM/YYYY',
            'fr_LU' => 'DD/MM/YYYY',
            'fr_mC' => 'DD/MM/YYYY',
            'fr' => 'DD/MM/YYYY',
            'fy_NL' => 'D-M-YYYY',
            'ga_IE' => 'DD/MM/YYYY',
            'ga' => 'DD/MM/YYYY',
            'gd_GB' => 'DD/MM/YYYY',
            'gl_ES' => 'DD/MM/YY',
            'gsw_FR' => 'DD/MM/YYYY',
            'gu_IN' => 'DD-MM-YY',
            'ha_Latn_NG' => 'D/M/YYYY',
            'he_IL' => 'DD/MM/YYYY',
            'hi_IN' => 'DD-MM-YYYY',
            'hi' => 'DD-MM-YYYY',
            'hr_BA' => 'D.M.YYYY.',
            'hr_HR' => 'D.M.YYYY',
            'hr' => 'D.M.YYYY',
            'hsb_DE' => 'D. M. YYYY',
            'hu_HU' => 'YYYY. MM. DD.',
            'hu' => 'YYYY. MM. DD.',
            'hy_Am' => 'DD.MM.YYYY',
            'hy' => 'DD.MM.YYYY',
            'id_ID' => 'DD/MM/YYYY',
            'ig_NG' => 'D/M/YYYY',
            'ii_CN' => 'YYYY/M/D',
            'in_ID' => 'DD/MM/YYYY',
            'in' => 'DD/MM/YYYY',
            'is_IS' => 'D.M.YYYY',
            'is' => 'D.M.YYYY',
            'it_CH' => 'DD.MM.YYYY',
            'it_IT' => 'DD/MM/YYYY',
            'it' => 'DD/MM/YYYY',
            'iu_Cans_CA' => 'D/M/YYYY',
            'iu_Latn_CA' => 'd/MM/YYYY',
            'iw_IL' => 'DD/MM/YYYY',
            'iw' => 'DD/MM/YYYY',
            'ja_JP' => 'YYYY/MM/DD',
            'ja' => 'YYYY/MM/DD',
            'ka_GE' => 'DD.MM.YYYY',
            'ka' => 'DD.MM.YYYY',
            'kk_KZ' => 'DD.MM.YYYY',
            'kl_GL' => 'DD-MM-YYYY',
            'km_KH' => 'YYYY-MM-DD',
            'kn_IN' => 'DD-MM-YY',
            'ko_KR' => 'YYYY-MM-DD',
            'ko' => 'YYYY-MM-DD',
            'kok_IN' => 'DD-MM-YYYY',
            'ky_KG' => 'DD.MM.YY',
            'lb_LU' => 'DD/MM/YYYY',
            'lb' => 'DD/MM/YYYY',
            'lo_LA' => 'DD/MM/YYYY',
            'lt_LT' => 'YYYY.MM.DD',
            'lt' => 'YYYY.MM.DD',
            'lv_LV' => 'YYYY.MM.DD.',
            'lv' => 'YYYY.MM.DD.',
            'mi_NZ' => 'DD/MM/YYYY',
            'mk_mK' => 'DD.MM.YYYY',
            'mk' => 'DD.MM.YYYY',
            'ml_IN' => 'DD-MM-YY',
            'mn_mN' => 'YY.MM.DD',
            'mn_mong_CN' => 'YYYY/M/D',
            'moh_CA' => 'M/D/YYYY',
            'mr_IN' => 'DD-MM-YYYY',
            'ms_BN' => 'DD/MM/YYYY',
            'ms_mY' => 'DD/MM/YYYY',
            'ms' => 'DD/MM/YYYY',
            'mt_mT' => 'DD/MM/YYYY',
            'mt' => 'DD/MM/YYYY',
            'nb_NO' => 'DD.MM.YYYY',
            'ne_NP' => 'M/D/YYYY',
            'nl_BE' => 'd/MM/YYYY',
            'nl_NL' => 'D-M-YYYY',
            'nl_SR' => 'D-M-YYYY',
            'nl' => 'D-M-YYYY',
            'nn_NO' => 'DD.MM.YYYY',
            'no_NO' => 'DD.MM.YYYY',
            'no' => 'DD.MM.YYYY',
            'nso_ZA' => 'YYYY/MM/DD',
            'oc_FR' => 'DD/MM/YYYY',
            'or_IN' => 'DD-MM-YY',
            'pa_IN' => 'DD-MM-YY',
            'pl_PL' => 'YYYY-MM-DD',
            'pl' => 'YYYY-MM-DD',
            'prs_AF' => 'DD/MM/YY',
            'ps_AF' => 'DD/MM/YY',
            'pt_AO' => 'DD/MM/YYYY',
            'pt_BR' => 'DD/MM/YYYY',
            'pt_PT' => 'DD-MM-YYYY',
            'pt' => 'DD/MM/YYYY',
            'qut_GT' => 'DD/MM/YYYY',
            'quz_BO' => 'DD/MM/YYYY',
            'quz_EC' => 'DD/MM/YYYY',
            'quz_PE' => 'DD/MM/YYYY',
            'rm_CH' => 'DD/MM/YYYY',
            'rm' => 'DD/MM/YYYY',
            'ro_MD' => 'DD.MM.YYYY',
            'ro_RO' => 'DD.MM.YYYY',
            'ro' => 'DD.MM.YYYY',
            'ru_RU' => 'DD.MM.YYYY',
            'ru' => 'DD.MM.YYYY',
            'rw_RW' => 'M/D/YYYY',
            'sa_IN' => 'DD-MM-YYYY',
            'sah_RU' => 'MM.DD.YYYY',
            'se_FI' => 'D.M.YYYY',
            'se_NO' => 'DD.MM.YYYY',
            'se_SE' => 'YYYY-MM-DD',
            'sh_BA' => 'DD.MM.YYYY',
            'sh_CS' => 'DD.MM.YYYY',
            'sh_ME' => 'DD.MM.YYYY',
            'sh' => 'DD.MM.YYYY',
            'si_LK' => 'YYYY-MM-DD',
            'sk_SK' => 'D. M. YYYY',
            'sk' => 'D. M. YYYY',
            'sl_SI' => 'D.M.YYYY',
            'sl' => 'D.M.YYYY',
            'sma_NO' => 'DD.MM.YYYY',
            'sma_SE' => 'YYYY-MM-DD',
            'smj_NO' => 'DD.MM.YYYY',
            'smj_SE' => 'YYYY-MM-DD',
            'smn_FI' => 'D.M.YYYY',
            'sms_FI' => 'D.M.YYYY',
            'sq_AL' => 'YYYY-MM-DD',
            'sr_BA' => 'D.M.YYYY',
            'sr_CS' => 'D.M.YYYY',
            'sr_Cyrl_BA' => 'D.M.YYYY',
            'sr_Cyrl_CS' => 'D.M.YYYY',
            'sr_Cyrl_mE' => 'D.M.YYYY',
            'sr_Cyrl_RS' => 'D.M.YYYY',
            'sr_Latn_BA' => 'D.M.YYYY',
            'sr_Latn_CS' => 'D.M.YYYY',
            'sr_Latn_mE' => 'D.M.YYYY',
            'sr_Latn_RS' => 'D.M.YYYY',
            'sr' => 'D.M.YYYY',
            'sv_FI' => 'D.M.YYYY',
            'sv_SE' => 'YYYY-MM-DD',
            'sv' => 'YYYY-MM-DD',
            'sw_KE' => 'M/D/YYYY',
            'syr_SY' => 'DD/MM/YYYY',
            'ta_IN' => 'DD-MM-YYYY',
            'te_IN' => 'DD-MM-YY',
            'tg_Cyrl_TJ' => 'DD.MM.YY',
            'tg_TJ' => 'DD.MM.YY',
            'th_TH' => 'D/M/YYYY',
            'th' => 'D/M/YYYY',
            'tk_Tm' => 'DD.MM.YY',
            'tl_PH' => 'DD-MM-YYYY',
            'tl' => 'DD-MM-YYYY',
            'tn_ZA' => 'YYYY/MM/DD',
            'tr_TR' => 'DD.MM.YYYY',
            'tr' => 'DD.MM.YYYY',
            'tt_RU' => 'DD.MM.YYYY',
            'tzm_Latn_DZ' => 'DD-MM-YYYY',
            'ug_CN' => 'yyyy-m-d',
            'uk_UA' => 'DD.MM.YYYY',
            'uk' => 'DD.MM.YYYY',
            'ur_PK' => 'DD/MM/YYYY',
            'ur' => 'DD/MM/YYYY',
            'uz_Cyrl_UZ' => 'DD.MM.YYYY',
            'uz_Latn_UZ' => 'DD/MM YYYY',
            'vi_VN' => 'DD/MM/YYYY',
            'vi' => 'DD/MM/YYYY',
            'wo_SN' => 'DD/MM/YYYY',
            'xh_ZA' => 'YYYY/MM/DD',
            'yo_NG' => 'D/M/YYYY',
            'zh_CN_PINYIN' => 'YYYY/M/D',
            'zh_CN_STROKE' => 'YYYY/M/D',
            'zh_CN' => 'YYYY/M/D',
            'zh_HK_STROKE' => 'YYYY/M/D',
            'zh_HK' => 'D/M/YYYY',
            'zh_mO' => 'D/M/YYYY',
            'zh_SG' => 'D/M/YYYY',
            'zh_TW_STROKE' => 'YYYY/M/D',
            'zh_TW' => 'YYYY/M/D',
            'zh' => 'YYYY/M/D',
            'zu_ZA' => 'YYYY/MM/DD'
        ];

        if (array_key_exists($locale, $formats)) {
            return $formats[$locale];
        }
        return 'YYYY-MM-DD';
    }

    /**
     * This method returns the country name for the given country code.
     *
     * @param string $countryCode
     *
     * @return string
     *
     * @see \ResourceBundle::getLocales()
     */
    private static array $countriesNames = [];
    public static function getCountryNameByCountryCode(string $countryCode): string {
        $countryName = '';
        if (isset(self::$countriesNames[$countryCode])) {
            $countryName = self::$countriesNames[$countryCode];
        } else {
            $countries = self::getCountries()->getItems();
            foreach ($countries as $country) {
                if ($country->getCode() === $countryCode) {
                    $countryName = $country->getName();
                    self::$countriesNames[$countryCode] = $countryName;
                    break;
                }
            }
        }
        return $countryName;
    }

    /**
     * This method returns all countries or countries from given countries codes.
     *
     * @param array $countries
     *
     * @return ElementCollection
     *
     * @see \ResourceBundle::getLocales()
     */
    public static function getCountries(array $countryCodes = []): ElementCollection {
        $languageCode = Language::getInstance()->getLanguage();
        $countries = RedisCache::getData(
            RedisKey::OBJECT . ':Countries_' . $languageCode,
            function () use ($languageCode) {
                $languageCode = Language::getInstance()->getLanguage();
                $countries = Loader::service(Services::GEOLOCATION)->getCountries($languageCode);
                if (empty($countries->getItems())) {
                    throw new CommerceException('No countries in response getCountries by languageCode: ' . $languageCode, CommerceException::UTILS_NO_COUNTRIES_IN_RESPONSE);
                }
                return $countries->toArray();
            },
            LIFE_TIME_CACHE_OBJECTS
        );
        if (!empty($countryCodes)) {
            foreach ($countries['items'] as $idx => $value) {
                if (!in_array($value['code'], $countryCodes)) {
                    unset($countries['items'][$idx]);
                }
            }
        }
        return Loader::service(Services::GEOLOCATION)->buildElementCollection($countries, Country::class);
    }

    /**
     * This method returns the user name from the given User using the configured user key criteria.
     * 
     * @param string $userName
     *
     * @return string
     *
     */
    public static function getUserName(?User $user = null): string {
        $registeredUser = Session::getInstance()?->getBasket()?->getRegisteredUser();
        $userName = '';
        $userKeyCriteria = Application::getInstance()->getEcommerceSettings()->getUserAccountsSettings()->getUserKeyCriteria();
        switch ($userKeyCriteria) {
            case UserKeyCriteria::PID:
                $userName = $user?->getPId() ?? $registeredUser?->getPId() ?? "";
                break;
            case UserKeyCriteria::EMAIL:
                $userName = $user?->getEmail() ?? $registeredUser?->getEmail() ?? "";
                break;
            case UserKeyCriteria::USERNAME:
                $userName = Session::getInstance()?->getBasket()?->getRegisteredUser()?->getUsername() ?? "";
                break;
            default:
                throw new CommerceException(self::class . 'Undefined user key criteria: ' . $userKeyCriteria, CommerceException::FORM_FACTORY_UNDEFINED_USER_KEY_CRITERIA);
        }
        return $userName;
    }


    /**
     * This method returns the user device.
     *
     * @return string
     *
     */
    public static function getDevice(): string {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/(Android 3\.\d|Tablet|iPad|Android.*(?:Tab|Pad))/i', $useragent)) {
            return Device::TABLET;
        } elseif (preg_match('/(Symbian|\bS60(Version|V\d)|\bS60\b|(Series 60|Windows Mobile|Palm OS|Bada); Opera Mini|Windows CE|Opera Mobi|BREW|Brew|Mobile; .+Firefox|iPhone|Android|MobileSafari|Windows *Phone|\(webOS|PalmOS)/i', $useragent)) {
            return Device::MOBILE;
        } else {
            return Device::DESKTOP;
        }
    }

    /**
     * This method sorts an array of abjects by the property sended
     *
     * @param string $arrayObj
     *                  Array of objetcs
     * @param string $propertySort
     *                  Name of property to sort
     * @param string $order 
     *                  Order to apply, must be Utils::SORT_ASC or Utils::SORT_DESC
     * 
     * @return array
     *
     */
    public static function sortArrayObjects($arrayObj, $propertySort, $order = self::SORT_ASC): array {
        return usort($objDatos, function ($propertySort, $order) {
            return function ($a, $b) use ($propertySort, $order) {
                $result = ($order == "DESC") ? strnatcmp($b->$propertySort, $a->$propertySort) :  strnatcmp($a->$propertySort, $b->$propertySort);
                return $result;
            };
        });
    }

    /**
     * This method returns the FolcsVersion value depending given cacheable value.
     *
     * @param bool $cacheable
     *
     * @return string
     */
    public static function getFolcsVersion(bool $cacheable = false): string {
        $session = Session::getInstance();
        $folcsVersion = '';
        if (!$cacheable) {
            $folcsVersion = (is_null($session->getUpdatedAt()) ? '' : (new DateTimeFormatter())->getFormattedDateTime($session->getUpdatedAt(), 'YMMddHHmmss')) . '-';
        }
        $folcsVersion .= (is_null(Cookie::get('cache-hash')) ? '' : Cookie::get('cache-hash'));
        return $folcsVersion;
    }


    /**
     * This method returns the session user warnings values
     *
     * @return array
     */
    public static function getUserWarnings(bool $cacheable = false): array {
        $userWarnings = [];
        $warnings = Session::getInstance()->getBasket()->getBasketWarnings();
        foreach ($warnings as $warning) {
            if ($warning->getCode() == BasketWarningCode::USER_NOT_VERIFIED || $warning->getCode() == BasketWarningCode::USER_NOT_ACTIVED) {
                $userWarnings[] = $warning->getCode();
            }
        }
        return $userWarnings;
    }

    public static function interceptURL(string $url): string {
        $urlIntercepted = $url;
        if (Environment::get('DEV_CONTAINER')) {
            $commerceHost = Environment::get('COMMERCE_HOST') ?: '';
            $commerceProtocol = Environment::get('COMMERCE_PROTOCOL') ?: '';
            $commerceStoreUrl = Environment::get('COMMERCE_STORE_URL') ?: '';
            if (!empty($commerceHost) && !empty($commerceProtocol) && !empty($commerceStoreUrl)) {
                $urlIntercepted = str_replace($commerceProtocol . '://' . $commerceHost, $commerceStoreUrl, $urlIntercepted);
            }
        }
        return $urlIntercepted;
    }

    public static function getSelectedCountryId(): ?string {
        $session = Session::getInstance();
        if ($session === null) {
            return null;
        }

        $sessionGeneralSettings = $session->getGeneralSettings();

        $user = $session->getUser();
        $billingAddressId = $user?->getSelectedBillingAddressId();
        $selectedAddress = $user?->getAddress($billingAddressId);

        if (self::isSessionLoggedIn($session)) {
            $countryCode = $selectedAddress?->getLocation()?->getGeographicalZone()?->getCountryCode();
            if ($countryCode !== null && $countryCode !== '') {
                return $countryCode;
            }
        }

        $fallback = $sessionGeneralSettings?->getCountry();
        return $fallback !== '' ? $fallback : null;
    }
}
