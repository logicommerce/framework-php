<?php

namespace FWK\Twig\Functions;

use FWK\Core\Resources\Assets;
use Twig\Environment;
use FWK\Core\Resources\Utils;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\DateTimeFormatter;
use SDK\Application;
use SDK\Core\Dtos\Element;
use SDK\Core\Resources\Environment as ResourcesEnvironment;
use SDK\Dtos\Catalog\Product\Product;
use SDK\Dtos\Catalog\BundleGrouping;

/**
 * This is the TwigFunctionsCore class.
 * This class extends FWK\Twig\Functions\TwigFunctions, see this class.
 * <br>This class is in charge to encapsulate usefull core functions for Twig and provide a method to add them to the Twig Environment you want.
 *
 * @see TwigFunctions
 *
 * @package FWK\Twig\Functions
 */
class TwigFunctionsCore extends TwigFunctions {

    /**
     * This method adds the Twig functions and filters defined by this class (see them) to the Twig Environment passed by parameter.
     * 
     * @param Environment $twig
     * Functions
     * @see TwigFunctionsCore::addParamsToRequest()
     * @see TwigFunctionsCore::deleteParamsFromRequest()
     * @see TwigFunctionsCore::formatDate()
     * @see TwigFunctionsCore::formatDateTime()
     * @see TwigFunctionsCore::formatMomentDateTime()
     * @see TwigFunctionsCore::getBuyPrice()
     * @see TwigFunctionsCore::getCommerceLogo()
     * @see TwigFunctionsCore::getCommerceUrl()
     * @see TwigFunctionsCore::getJSDatePattern()
     * @see TwigFunctionsCore::getTwigDatePattern()
     * @see TwigFunctionsCore::localizedCurrency()
     * @see TwigFunctionsCore::localizedDateTime()
     * @see TwigFunctionsCore::outputHtmlCurrency()
     * @see TwigFunctionsCore::outputHtmlRating()
     * @see TwigFunctionsCore::outputJsonHtmlString()
     */
    protected static function addLocalFunctions(Environment $twig): void {
        if (!TwigFunctionsRoot::isAdded($twig)) {
            TwigFunctionsRoot::addFunctions($twig);
        }
        $twig->addFunction(static::addParamsToRequest());
        $twig->addFunction(static::deleteParamsFromRequest());
        $twig->addFunction(static::formatDate());
        $twig->addFunction(static::formatDateTime());
        $twig->addFunction(static::formatMomentDateTime());
        $twig->addFunction(static::getBuyPrice());
        $twig->addFunction(static::getCommerceLogo());
        $twig->addFunction(static::getCommerceUrl());
        $twig->addFunction(static::getJSDatePattern());
        $twig->addFunction(static::getTwigDatePattern());
        $twig->addFunction(static::localizedCurrency());
        $twig->addFunction(static::localizedDateTime());
        $twig->addFunction(static::outputHtmlCurrency());
        $twig->addFunction(static::outputHtmlRating());
        $twig->addFunction(static::outputJsonHtmlString());
    }

    /**
     * This method returns a Twig function named 'outputHtmlRating'.
     * This Twig function outputs a rating in html stars.
     *
     * @return \Twig\TwigFunction
     */
    private static function outputHtmlRating(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('outputHtmlRating', function (?float $value, bool $editable = false) {
            $output = '<div class="lcRating' . ($editable ? ' editable' : '') . '">';
            for ($i = 1; $i <= 5; $i++) {
                $inactive = round($value, 0, PHP_ROUND_HALF_UP) < $i;
                $output .= '<svg class="starIcon' . ($inactive ? ' inactive' : '') . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M14.796 5.661c-.046-.158-.167-.27-.314-.296l-4.386-.696L8.122.241C8.056.093 7.92 0 7.773 0c-.147 0-.283.093-.349.245L5.476 4.686l-4.386.727c-.147.026-.268.138-.314.296-.047.158-.006.332.101.445l3.182 3.442-.735 4.876c-.026.164.034.329.155.425.067.055.148.084.228.084.064 0 .125-.016.182-.051l3.916-2.316 3.927 2.29c.058.032.118.048.179.048.213 0 .389-.197.389-.435 0-.036-.002-.068-.011-.1l-.759-4.846 3.163-3.462c.112-.116.15-.29.103-.448z"/></svg>';
            }
            $output .= '</div>';
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'localizedDateTime'.
     * This Twig function gets the locale date time.
     *
     * @return \Twig\TwigFunction
     */
    private static function localizedDateTime(): ?\Twig\TwigFunction {
        return new \Twig\TwigFunction('localizedDateTime', function ($date = null, int $dateFormatter = null, int $timeFormatter = null, int $calendarFormatter = null, string $format = null) {
            return new \Twig\Markup((new DateTimeFormatter($dateFormatter, $timeFormatter, $calendarFormatter))->getFormattedDateTime($date, $format), CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'formatDate'.
     * This Twig function gets the locale date time.
     *
     * @return \Twig\TwigFunction
     */
    private static function formatDate(): ?\Twig\TwigFunction {
        return new \Twig\TwigFunction('formatDate', function ($date = null, string $format = null) {
            return new \Twig\Markup((new DateTimeFormatter())->getFormattedDate($date, $format), CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'formatDateTime'.
     * This Twig function gets the locale date time.
     *
     * @return \Twig\TwigFunction
     */
    private static function formatDateTime(): ?\Twig\TwigFunction {
        return new \Twig\TwigFunction('formatDateTime', function ($date = null, string $format = null) {
            return new \Twig\Markup((new DateTimeFormatter())->getFormattedDateTime($date, $format), CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'formatJsDateTime'.
     * This Twig function gets the locale date time.
     *
     * @return \Twig\TwigFunction
     */
    private static function formatMomentDateTime(): ?\Twig\TwigFunction {
        return new \Twig\TwigFunction('formatMomentDateTime', function ($date = null) {
            return new \Twig\Markup((new DateTimeFormatter())->getFormattedDateTime($date,  "Y-MM-dd HH:mm:ss"), CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'localizedCurrency'. 
     * This Twig function gets the locale currency.
     * 
     * @return \Twig\TwigFunction
     */
    private static function localizedCurrency(): ?\Twig\TwigFunction {
        return new \Twig\TwigFunction('localizedCurrency', function (?float $value) {
            $value ??= 0;
            $currencyCode = Session::getInstance()->getGeneralSettings()->getCurrency();
            $fmt = numfmt_create(Session::getInstance()->getGeneralSettings()->getLocale(), \NumberFormatter::CURRENCY);
            $output = numfmt_format_currency($fmt, $value, Session::getInstance()->getGeneralSettings()->getCurrency());
            $currencySymbol = numfmt_get_symbol($fmt, \NumberFormatter::CURRENCY_SYMBOL);
            foreach (Application::getInstance()->getCurrenciesSettings() as $currency) {
                if ($currency->getCode() === $currencyCode) {
                    $output = str_replace($currencySymbol, $currency->getSymbol(), $output);
                    break;
                }
            }
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'getBuyPrice'. 
     * This Twig function return the buy price, base or offer.
     * 
     * @return \Twig\TwigFunction
     */
    private static function getBuyPrice(): ?\Twig\TwigFunction {
        return new \Twig\TwigFunction('getBuyPrice', function (?Element $item, bool $base = false, bool $taxIncluded = false) {
            $price = 0;
            $fncGetPrices = 'getPrices';
            if ($taxIncluded) {
                $fncGetPrices .= 'WithTaxes';
            }

            if ($item instanceof Product) {
                $itemPrices = $item->$fncGetPrices()->getPrices();
                $optPrices = $item->getDefaultOptionsPrices()->$fncGetPrices();
                $price = $itemPrices->getBasePrice() + $optPrices->getBasePrice();
                if (!$base && $item->getDefinition()->getOffer()) {
                    $price = $itemPrices->getRetailPrice() + $optPrices->getRetailPrice();
                }
            } elseif ($item instanceof BundleGrouping) {
                $itemPrices = $item->getCombinationData()->$fncGetPrices();
                if (!$base) {
                    $price = $itemPrices->getRetailPrice();
                } else {
                    $price = $itemPrices->getBasePrice();
                }
            } else {
                $itemPrices = $item->$fncGetPrices();
                if (!$base) {
                    $price = $itemPrices->getRetailPrice();
                } else {
                    $price = $itemPrices->getBasePrice();
                }
            }

            return $price;
        });
    }

    /**
     * This method returns a Twig function named 'outputHtmlCurrency'.  
     * This Twig function outputs the currency in html.
     * 
     * @return \Twig\TwigFunction
     */
    private static function outputHtmlCurrency(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('outputHtmlCurrency', function (?float $value) {
            $decimalsMaxLength = CURRENCY_DECIMALS_MAX_LENGTH;
            $decimalsMinLength = CURRENCY_DECIMALS_MIN_LENGTH;
            $session = Session::getInstance();
            $value ??= 0;
            $output = '';
            $value = round($value, $decimalsMaxLength); // round to decimals lenght to avoid errors with numfmt_set_pattern($fmtAttr, '#0.' . str_repeat('0', $decimalsMaxLength));
            $currencyCode = $session->getGeneralSettings()->getCurrency();

            $fmt = numfmt_create($session->getGeneralSettings()->getLocale() . "@currency=" . $session->getGeneralSettings()->getCurrency(), \NumberFormatter::CURRENCY);
            numfmt_set_attribute($fmt, \NumberFormatter::MAX_FRACTION_DIGITS, $decimalsMaxLength);
            numfmt_set_attribute($fmt, \NumberFormatter::MIN_FRACTION_DIGITS, $decimalsMinLength);
            $currency = numfmt_format_currency($fmt, $value, $currencyCode);

            $pattern = numfmt_get_pattern($fmt);
            $decimalSymbol = numfmt_get_symbol($fmt, \NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
            $currencySymbol = numfmt_get_symbol($fmt, \NumberFormatter::CURRENCY_SYMBOL);

            $currencyFirst = false;
            if (substr($pattern, 0, 2) === '¤') {
                $currencyFirst = true;
            }

            $currencyValue = trim(preg_replace('/\xA0/u', ' ', str_replace($currencySymbol, '', $currency)));
            $currencyValueArr = explode($decimalSymbol, $currencyValue);

            $integerValue = null;
            $decimalValue = null;
            if (count($currencyValueArr) === 1) {
                $integerValue = $currencyValueArr[0];
            } else if (count($currencyValueArr) === 2) {
                $integerValue = $currencyValueArr[0];
                $decimalValue = $currencyValueArr[1];
                for ($i = $decimalsMinLength; $i < $decimalsMaxLength; $i++) {
                    while (strlen($decimalValue) > $decimalsMinLength && substr($decimalValue, -1) === '0') {
                        $decimalValue = substr($decimalValue, 0, -1);
                    }
                }
            }

            foreach (Application::getInstance()->getCurrenciesSettings() as $currency) {
                if ($currency->getCode() === $currencyCode) {
                    $currencySymbol = $currency->getSymbol();
                    break;
                }
            }

            if ($integerValue !== null) {
                $output .= '<span class="price">';
                if ($currencyFirst) {
                    $output .= '<span class="currencySymbol">' . $currencySymbol . '</span>';
                }
                $fmtAttr = numfmt_create('EN', \NumberFormatter::PATTERN_DECIMAL);
                numfmt_set_pattern($fmtAttr, '#0.' . str_repeat('0', $decimalsMaxLength));
                $output .= '<span class="integerPrice" content="' . numfmt_format($fmtAttr, $value) . '">' . $integerValue . '</span>';
                if ($decimalValue !== null && !empty($decimalValue)) {
                    $output .= '<span class="decimalPrice">' . $decimalSymbol . $decimalValue . '</span>';
                }
                if (!$currencyFirst) {
                    $output .= '<span class="currencySymbol">' . $currencySymbol . '</span>';
                }
                $output .= '</span>';
            }

            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'outputJsonHtmlString'. 
     * This Twig function returns the given value in json format.
     *
     * @return \Twig\TwigFunction
     */
    private static function outputJsonHtmlString(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('outputJsonHtmlString', function ($value = null) {
            return new \Twig\Markup(Utils::outputJsonHtmlString($value), CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'addParamsToRequest'. 
     * This Twig function returns an string with the params of the request concatenating to it the 
     * 'newParams' parameters given by parameter as part of the request parameters. 
     *
     * @return \Twig\TwigFunction
     */
    private static function addParamsToRequest(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('addParamsToRequest', function (?array $newParams) {
            return new \Twig\Markup(Utils::parseArrayToPathParameters(Utils::addParamsToRequest($newParams)), CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'deleteParamsFromRequest'.  
     * This Twig function returns an string with the params of the request eliminating 
     * from it the parameters given by parameter. 
     * 
     * @return \Twig\TwigFunction
     */
    private static function deleteParamsFromRequest(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('deleteParamsFromRequest', function (?array $parameters) {
            return new \Twig\Markup(Utils::parseArrayToPathParameters(Utils::deleteParamsFromRequest($parameters)), CHARSET);
        });
    }

    /**
     * This method return the JS Date Pattern by session locale.
     *
     * @return \Twig\TwigFunction
     */
    private static function getJSDatePattern(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getJSDatePattern', function () {
            return Utils::getJSDatePatternByLocale(Session::getInstance()->getGeneralSettings()->getLocale());
        });
    }

    /**
     * This method return the JS Date Pattern by session locale.
     *
     * @return \Twig\TwigFunction
     */
    private static function getTwigDatePattern(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getTwigDatePattern', function () {
            $pattern = Utils::getJSDatePatternByLocale(Session::getInstance()->getGeneralSettings()->getLocale());
            $pattern = strtolower($pattern);
            $pattern = str_replace('dd', 'd', $pattern);
            $pattern = str_replace('mm', 'm', $pattern);
            $pattern = str_replace('yyyy', 'Y', $pattern);
            $pattern = str_replace('yy', 'Y', $pattern);
            return $pattern;
        });
    }

    /**
     * This method returns a Twig function named 'getCommerceLogo'.
     * This Twig function returns commerceLogo by language.
     *
     * @return \Twig\TwigFunction
     */
    private static function getCommerceLogo(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getCommerceLogo', function () {
            $ecommerceSettings = Application::getInstance()->getEcommerceSettings();
            if (strpos($ecommerceSettings->getGeneralSettings()->getLogo(), '://') != false) {
                $output = $ecommerceSettings->getGeneralSettings()->getLogo();
                $languageCode = Session::getInstance()->getGeneralSettings()->getLanguage();
                $settingslanguages = $ecommerceSettings->getGeneralSettings()->getLanguages();
                foreach ($settingslanguages as $settingslanguage) {
                    if ($settingslanguage->getLanguageCode() === $languageCode) {
                        $output = $settingslanguage->getLogo();
                        break;
                    }
                }
            } else {
                $output = Assets::getInstance()->getAssetsImagesPath(Assets::ENVIRONMENT_IMAGES) . '/' . $ecommerceSettings->getGeneralSettings()->getLogo();
            };
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'getCommerceUrl'.
     * This Twig function returns getCommerceUrl by language.
     *
     * @return \Twig\TwigFunction
     */
    private static function getCommerceUrl(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getCommerceUrl', function () {
            $commerceStoreUrl = ResourcesEnvironment::get('COMMERCE_STORE_URL');
            $storeURL = Session::getInstance()->getGeneralSettings()->getStoreURL();
            if (isset($commerceStoreUrl)) {
                $path = parse_url($storeURL, PHP_URL_PATH);
                $output = rtrim($commerceStoreUrl, '/') . $path;
            } else {
                $output = $storeURL;
            }
            return new \Twig\Markup($output, CHARSET);
        });
    }
}
