<?php

namespace FWK\Twig\Functions;

use Twig\Environment;
use FWK\Core\Resources\Assets;
use FWK\Core\Resources\SeoItems;
use SDK\Core\Application;

/**
 * This is the TwigFunctionsHtml class.
 * This class extends FWK\Twig\Functions\TwigFunctions, see this class.
 * <br>This class is in charge to encapsulate usefull html functions for Twig and provide a method to add them to the Twig Environment you
 * want.
 *
 * @see TwigFunctions
 *
 * @package FWK\Twig\Functions
 */
class TwigFunctionsHtml extends TwigFunctions {

    /**
     * This method adds the Twig functions defined by this class (see them) to the Twig Environment passed by parameter.
     *
     * @param Environment $twig
     *
     * Functions
     * @see TwigFunctionsHtml::alternates()
     * @see TwigFunctionsHtml::canonical()
     * @see TwigFunctionsHtml::getPluginMoment()
     * @see TwigFunctionsHtml::includeCssAssets()
     * @see TwigFunctionsHtml::includeDnsPrefetch()
     * @see TwigFunctionsHtml::includeFontAssets()
     * @see TwigFunctionsHtml::includeJsAssets()
     * @see TwigFunctionsHtml::metatags()
     * @see TwigFunctionsHtml::title()
     * Filters
     * @see TwigFunctionsHtml::rewriteHtml()
     */
    protected static function addLocalFunctions(Environment $twig): void {
        if (!TwigFunctionsRoot::isAdded($twig)) {
            TwigFunctionsRoot::addFunctions($twig);
        }
        $twig->addFunction(static::alternates());
        $twig->addFunction(static::canonical());
        $twig->addFunction(static::getPluginMoment());
        $twig->addFunction(static::includeCssAssets());
        $twig->addFunction(static::includeDnsPrefetch());
        $twig->addFunction(static::includeFontAssets());
        $twig->addFunction(static::includeJsAssets());
        $twig->addFunction(static::metatags());
        $twig->addFunction(static::title());
        $twig->addFilter(static::rewriteHtml());
        $twig->addFilter(static::localizedDateFilter());
    }

    /**
     * This method returns a Twig function named 'includeCssAssets'.
     * This Twig function returns the HTML output (link tags) to include the required CSS assets (Return example: <link rel="stylesheet" 
     * href="xxx" >).
     * <br>If the parameters are provided then it considers the paths indicated in the parameters instead of the ones defined by the Assets
     * object (Assets::getAssetsPaths()).
     *
     * @return \Twig\TwigFunction
     *
     * @see Assets::getAssetsPaths()
     */
    private static function includeCssAssets(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('includeCssAssets', function ($include = [], string $group = 'default') {
            $cssPaths = [];
            if (is_array($include)) {
                $cssPaths = $include;
            } elseif (is_string($include)) {
                $cssPaths = Assets::getInstance()->getAssetsPaths($include, Assets::TYPE_CSS, $group);
            }
            $output = '';
            foreach ($cssPaths as $path) {
                $output .= '<link rel="stylesheet" href="' . $path . '">';
            }
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'includeJsAssets'.
     * This Twig function returns the JS output (script tags) to include the required JS assets (Return example: < script src="xxx" ><
     * /script >).
     * <br>If the parameters are provided then it considers the paths indicated in the parameters instead of the ones defined by the Assets
     * object (Assets::getAssetsPaths()).
     *
     * @return \Twig\TwigFunction
     */
    private static function includeJsAssets(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('includeJsAssets', function ($include = [], string $group = 'default', bool $defer = false, string $type = '') {
            $jsPaths = [];
            if (is_array($include)) {
                $jsPaths = $include;
            } elseif (is_string($include)) {
                $jsPaths = Assets::getInstance()->getAssetsPaths($include, Assets::TYPE_JS, $group);
            }
            $output = '';
            foreach ($jsPaths as $path) {
                $output .= '<script' . (strlen($type) > 0 ? ' type="' . $type . '"' : '') . ' src="' . $path . '"' . ($defer ? ' defer' : '') . '></script>';
            }
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'includeFontAssets'.
     * This Twig function returns the font output (inside style tags) to include the required fonts.
     * <br>If the parameters are provided then it considers the paths indicated in the parameters instead of the ones defined by the Assets
     * object (Assets::getAssetsPaths()).
     *
     * @return \Twig\TwigFunction
     */
    private static function includeFontAssets(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('includeFontAssets', function (array $fonts = []) {
            $basePath = Assets::getInstance()->getAssetsFontsPath(Assets::ENVIRONMENT_COMMERCE) . '/';
            $output = '';
            if (!empty($fonts)) {
                $output = '<style>';
                foreach ($fonts as $font) {
                    $family = $font['family'];
                    $file = $font['file'];
                    $weight = $font['weight'];
                    $output .= '@font-face {';
                    $output .= " font-family: '$family'; ";
                    $output .= " src: url('$basePath$file.woff2') format('woff2'),";
                    $output .= " url('$basePath$file.woff') format('woff');";
                    $output .= " font-weight: $weight;";
                    $output .= " font-display: swap;";
                    $output .= "} ";
                }
                $output .= '</style>';
            }
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'includeDnsPrefetch'.
     * This Twig function returns the dns-prefetch output (link tags) for the dns given by parameter (Return example: < link
     * rel="dns-prefetch" href="xxx" >< /script >).
     *
     * @return \Twig\TwigFunction
     */
    private static function includeDnsPrefetch(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('includeDnsPrefetch', function (array $dnsPrefetchs = []) {
            $output = '';
            foreach ($dnsPrefetchs as $dns) {
                $output .= '<link rel="dns-prefetch" href="' . $dns . '">';
            }
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'metatags'.
     * This Twig function returns the metatags output.
     *
     * @return \Twig\TwigFunction
     */
    private static function metatags(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('metatags', function (?SeoItems $seoItems) {
            $output = '';
            if (!is_null($seoItems)) {
                $output = $seoItems->outputMetatags();
            }
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'title'.
     * This Twig function returns the title output.
     *
     * @return \Twig\TwigFunction
     */
    private static function title(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('title', function (?SeoItems $seoItems) {
            $output = '';
            if (!is_null($seoItems)) {
                $output = $seoItems->outputTitle();
            }
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'canonical'.
     * This Twig function returns the canonical output.
     *
     * @return \Twig\TwigFunction
     */
    private static function canonical(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('canonical', function (?SeoItems $seoItems) {
            $output = '';
            if (!is_null($seoItems)) {
                $output = $seoItems->outputCanonical();
            }
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method returns a Twig function named 'alternates'.
     * This Twig function returns the alternates output.
     *
     * @return \Twig\TwigFunction
     */
    private static function alternates(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('alternates', function (?SeoItems $seoItems) {
            $output = '';
            if (!is_null($seoItems)) {
                $output = $seoItems->outputAlternates();
            }
            return new \Twig\Markup($output, CHARSET);
        });
    }

    /**
     * This method cleans an HTML structure correcting bad-formed tags. Returns empty string on error.
     *
     * @return \Twig\TwigFilter
     */
    private static function rewriteHtml(): \Twig\TwigFilter {
        return new \Twig\TwigFilter('rewriteHtml', function (string $string = ''): string {
            $dom = new \DOMDocument();
            $dom->loadHTML(utf8_decode($string), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $result = $dom->saveHTML();
            if ($result === false) {
                return '';
            }
            return $result;
        });
    }

    private static function localizedDateFilter(): \Twig\TwigFilter {
        return new \Twig\TwigFilter('date', function (
            $date,
            string $format = null,
            string $timezone = null
        ) {
            if ($date === null || $date === '') {
                return '';
            }

            if (!$date instanceof \DateTimeInterface) {
                try {
                    $date = new \DateTime($date);
                } catch (\Throwable $e) {
                    return '';
                }
            }

            if ($timezone === null) {
                $timezone = Application::getInstance()->getEcommerceSettings()->getGeneralSettings()->getTimeZone() ?? 'UTC';
            }

            $date->setTimezone(new \DateTimeZone($timezone));

            $format = $format ?? 'F j, Y H:i';
            return $date->format($format);
        });
    }

    /**
     * This method returns momentjs plugin files
     *
     * @return \Twig\TwigFunction
     */
    private static function getPluginMoment(): \Twig\TwigFunction {
        return new \Twig\TwigFunction('getPluginMoment', function (string $language): array {
            // Add or look more languages see: FWK\assets\tools\plugins\moment\locale
            // Moment js locale dont follow the ISO_639-1, if any language does not work correctly fix the code
            $languages = [
                'af',
                'ar',
                'az',
                'be',
                'bg',
                'bm',
                'bn',
                'bo',
                'br',
                'bs',
                'ca',
                'cs',
                'cv',
                'cy',
                'da',
                'de',
                'dv',
                'el',
                'eo',
                'es',
                'et',
                'eu',
                'fa',
                'fi',
                'fil',
                'fo',
                'fr',
                'fy',
                'ga',
                'gd',
                'gl',
                'gu',
                'he',
                'hi',
                'hr',
                'hu',
                'id',
                'is',
                'it',
                'ja',
                'jv',
                'ka',
                'kk',
                'km',
                'kn',
                'ko',
                'ku',
                'ky',
                'lb',
                'lo',
                'lt',
                'lv',
                'me',
                'mi',
                'mk',
                'ml',
                'mn',
                'mr',
                'ms',
                'mt',
                'my',
                'nb',
                'ne',
                'nl',
                'nn',
                'pl',
                'pt',
                'ro',
                'ru',
                'sd',
                'se',
                'si',
                'sk',
                'sl',
                'sq',
                'sr',
                'ss',
                'sv',
                'sw',
                'ta',
                'te',
                'tet',
                'tg',
                'th',
                'tk',
                'tlh',
                'tr',
                'tzl',
                'tzm',
                'uk',
                'ur',
                'uz',
                'vi',
                'yo',
                'zh',
            ];
            $path = Assets::getInstance()->getAssetsPath(Assets::ENVIRONMENT_CORE) . '/tools/plugins/moment/';
            $scripts = [
                $path . 'moment.min.js'
            ];
            if (in_array($language, $languages) === true) {
                $scripts[] = $path . 'locale/' . $language . '.js';
            }
            return $scripts;
        });
    }
}
