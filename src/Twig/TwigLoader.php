<?php

namespace FWK\Twig;

use FWK\Core\Controllers\Controller;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Loader;
use FWK\Core\Theme\Theme;
use FWK\Twig\Environment;
use Twig\Loader\FilesystemLoader;
use SDK\Core\Resources\Timer;
use FWK\Core\Controllers\ControllersFactory;
use FWK\Core\Resources\LcFWK;
use FWK\Core\Resources\Session;
use FWK\Enums\ControllerData;
use FWK\Enums\TwigAutoescape;
use FWK\Core\Resources\Utils;
use FWK\Enums\RouteItems;
use FWK\Enums\TwigContentTypes;
use SDK\Application;

/**
 * This is the TwigLoader class.
 * This class facilitates the creation and initialization of the Twig Environment.
 *
 * @see TwigLoader::load()
 * @see TwigLoader::render()
 * @see TwigLoader::addFunction()
 * @see TwigLoader::getTwigEnvironment()
 *
 * @package FWK\Twig
 */
final class TwigLoader implements TwigLoaderInterface {

    private $twig;

    private $coreTwig;

    private $theme;

    private $themeVersion;

    private $themeFWK;

    private $routeType;

    private $pageLayout;

    private $pageContent;

    private $corePath;

    private $data;

    /**
     * Constructor. Creates the TwigLoader and initializes it with the given Theme. 
     * 
     * @param Theme $theme
     */
    public function __construct(Theme $theme) {
        $this->routeType = $theme->getRouteType();
        $this->theme = SITE_PATH . '/themes/' . $theme->getName();
        $this->themeVersion = $theme->getVersion();

        $this->pageLayout = strlen($theme->getLayout()) ? $theme->getLayout() : DEFAULT_ROUTE[RouteItems::THEME][RouteItems::LAYOUT];
        $this->pageContent = strlen($theme->getContent()) ? $theme->getContent() : DEFAULT_ROUTE[RouteItems::THEME][RouteItems::CONTENT];
        $this->themeFWK = FWK_LOAD_PATH . '/themes/' . $theme->getName();

        $this->corePath = [
            SITE_PATH . '/themes/core',
            FWK_LOAD_PATH . '/themes/core'
        ];

        if (defined('USE_PHARS') && USE_PHARS && ((defined('USE_LC_PLUGINS_PHAR') && USE_LC_PLUGINS_PHAR) || !defined('USE_LC_PLUGINS_PHAR'))) {
            $this->corePath[] = PLUGINS_LOAD_PATH . '/twigCoreTemplates';
        } else {
            $plugins = Application::getInstance()?->getEcommercePlugins() ?? [];
            foreach ($plugins as $plugin) {
                $corePluginPath = PLUGINS_LOAD_PATH . '/' . Utils::getCamelFromSnake($plugin->getModule(), '.') . '/twigCoreTemplates';
                if (is_dir($corePluginPath)) {
                    $this->corePath[] = $corePluginPath;
                }
            }
        }
    }

    /**
     * This method creates and initializes the Twig Environment. 
     * For the initialization it takes into account the Theme specified in the TwigLoader creation 
     * and all the data given by parameters to this method.
     *
     * @param array $data
     *            -> All the needed data to be accesible from Twig templates.
     * @param int $autoescape
     *            -> @See TwigAutoescape::AUTOESCAPE_*. https://twig.symfony.com/doc/2.x/tags/autoescape.html
     * @param bool $loadCore
     *            -> To indicate if it is required to load the Twig Core Macros functionality.
     * 
     * @return void
     */
    public function load(array $data = [], int $autoescape = 0, bool $loadCore = true) {
        if (is_dir($this->getTheme())) {
            $loader = new \Twig\Loader\FilesystemLoader([
                $this->getTheme(),
                $this->getThemeFWK(),
                FWK_LOAD_PATH . '/themes/default'
            ]);
        } else {
            $loader = new \Twig\Loader\FilesystemLoader($this->getThemeFWK());
        }
        $this->data = $data;
        $twigOptions = [];
        $twigOptions['debug'] = LcFWK::getTwigDevel();
        $twigOptions['charset'] = CHARSET;
        $twigOptions['cache'] = $this->getCache();
        $twigOptions['auto_reload'] = LcFWK::getTwigOptionAutoreload();
        $twigOptions['strict_variables'] = LcFWK::getTwigOptionStrictVariables();
        $twigOptions['optimizations'] = LcFWK::getTwigOptionOptimizations();
        $twigOptions['autoescape'] = ($autoescape > 0) ? $this->getAutoescape($autoescape) : false;
        $this->twig = $this->initEnvironment($loader, $twigOptions);
        if ($loadCore) {
            $this->loadCore($twigOptions);
        }
    }

    private function initEnvironment($loader, $twigOptions): Environment {
        $twig = new Environment($loader, $twigOptions);
        $twig->addExtension(new \Twig\Extension\StringLoaderExtension());
        if (LcFWK::getTwigDevel()) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }
        return $twig;
    }

    private function loadCore(array $twigOptions) {
        $coreTwigLoader = new FilesystemLoader($this->corePath);
        $this->coreTwig = $this->initEnvironment($coreTwigLoader, $twigOptions);

        Loader::twigFunctions(TwigContentTypes::CORE)->addFunctions($this->twig);
        Loader::twigFunctions(TwigContentTypes::CORE)->addFunctions($this->coreTwig);
        Loader::twigExtensions(TwigContentTypes::CORE)->addExtensions($this->twig);
        Loader::twigExtensions(TwigContentTypes::CORE)->addExtensions($this->coreTwig);

        foreach (Controller::getGlobalDataKeys() as $globalDataKey) {
            isset($this->data[$globalDataKey]) ? $this->coreTwig->addGlobal($globalDataKey, $this->data[$globalDataKey]) : null;
        }

        $coreMacros = [
            ControllerData::MACROS_CORE_ACCOUNT => $this->coreTwig->load('macros/account.twig'),
            ControllerData::MACROS_CORE_BASKET => $this->coreTwig->load('macros/basket.twig'),
            ControllerData::MACROS_CORE_BLOG => $this->coreTwig->load('macros/blog.twig'),
            ControllerData::MACROS_CORE_CATEGORY => $this->coreTwig->load('macros/category.twig'),
            ControllerData::MACROS_CORE_FORM => $this->coreTwig->load('macros/form.twig'),
            ControllerData::MACROS_CORE_DOCUMENT => $this->coreTwig->load('macros/document.twig'),
            ControllerData::MACROS_CORE_PAGE => $this->coreTwig->load('macros/page.twig'),
            ControllerData::MACROS_CORE_PRODUCT => $this->coreTwig->load('macros/product.twig'),
            ControllerData::MACROS_CORE_THIRD_PARTY => $this->coreTwig->load('macros/thirdParty.twig'),
            ControllerData::MACROS_CORE_USER => $this->coreTwig->load('macros/user.twig'),
            ControllerData::MACROS_CORE_UTIL => $this->coreTwig->load('macros/util.twig')
        ];

        $this->twig->addGlobal(ControllerData::MACROS_CORE, $coreMacros);
    }

    /**
     * This method uses Twig Environment to render the page and returns the renderization. 
     * The renderization is based on the Theme provided in the TwigLoader creation, 
     * the data provided with the load method and the format specified by parameter(by default html).
     *
     * @param string $content
     *            -> If different to null, it overrides the theme content to load in Twig
     * @param string $layout
     *            -> If different to null, it overrides the theme layout to load in Twig
     * @param string $format
     *            -> Default value: 'html'. Set the format to mark the name of the layout. For example, with "json" value, Twig renderization will load default.json.twig
     *            
     * @return string
     */
    public function render(String $content = null, String $layout = null, String $format = 'html') {
        Utils::addTimerDebugFlag('TwigLoader-render', Timer::START_SUFFIX);
        $localVersion = (strlen($this->themeVersion) ? $this->themeVersion : '');
        $localVersion = $localVersion . (strlen($localVersion) ? '/' : '');

        $basicLayout = (is_null($layout) ? 'layouts/' . $this->pageLayout . '.' . $format . '.twig' : $layout);
        $localLayout = $localVersion . $basicLayout;
        $basicContent = (is_null($content) ? 'Content/' . ControllersFactory::getPath($this->routeType) . '/' . $this->pageContent . '.' . $format . '.twig' : $content);
        $localContent = $localVersion . $basicContent;

        $this->twig->addGlobal(ControllerData::VERSION, $localVersion);

        foreach (Controller::getGlobalDataKeys() as $globalDataKey) {
            if (isset($this->data[$globalDataKey])) {
                $this->twig->addGlobal($globalDataKey, $this->data[$globalDataKey]);
                unset($this->data[$globalDataKey]);
            }
        }

        try {
            $this->twig->addGlobal(ControllerData::LAYOUT, $localLayout);
            $this->twig->addGlobal(ControllerData::CONTENT, $localContent);
            $templateWrapper = $this->twig->load($localContent);
        } catch (\Twig\Error\LoaderError $th) {
            $this->twig->addGlobal(ControllerData::LAYOUT, $basicLayout);
            $this->twig->addGlobal(ControllerData::CONTENT, $basicContent);
            $templateWrapper = $this->twig->load($basicContent);
        }
        $renderization = $this->twig->render($templateWrapper, $this->data);

        Utils::addTimerDebugFlag('TwigLoader-render', Timer::END_SUFFIX);
        return $renderization;
    }

    /**
     * This method adds the given function to the Twig Environment. 
     * 
     * @param $fn
     * 
     * @return void
     */
    public function addFunction($fn) {
        $this->twig->addFunction($fn);
    }

    /**
     * This method returns the Twig Environment.
     * 
     * @return Environment
     */
    public function getTwigEnvironment(): Environment {
        return $this->twig;
    }

    private function getAutoescape(int $autoescape): String {
        $response = '';
        switch ($autoescape) {
            case TwigAutoescape::AUTOESCAPE_NAME:
                $response = "name";
                break;
            case TwigAutoescape::AUTOESCAPE_HTML:
                $response = "html";
                break;
            case TwigAutoescape::AUTOESCAPE_JS:
                $response = "js";
                break;
            case TwigAutoescape::AUTOESCAPE_CSS:
                $response = "css";
                break;
            case TwigAutoescape::AUTOESCAPE_URL:
                $response = "url";
                break;
            case TwigAutoescape::AUTOESCAPE_HTML_ATTR:
                $response = "html_attr";
                break;
            case TwigAutoescape::AUTOESCAPE_NEW_STRATEGY:
                $response = "ToDo new escaping strategy";
                break;
            default:
                throw new CommerceException('Undefined autoescape strategy:' . $autoescape, CommerceException::TWIG_LOADER_UNDEFINED_ESCAPING_STRATEGY);
        }

        return $response;
    }

    private function getTheme() {
        return $this->theme;
    }

    private function getThemeFWK() {
        return $this->themeFWK;
    }

    private function getCache() {
        $constUseTwigCache = 'X-TWIG-CACHE';
        if (isset(REQUEST_HEADERS[$constUseTwigCache])) {
            if (boolval(REQUEST_HEADERS[$constUseTwigCache])) {
                return new TwigCache($this->getTheme());
            }
            return false;
        }
        if (LcFWK::getTwigOptionCache()) {
            return new TwigCache($this->getTheme());
        }
        return false;
    }
}
