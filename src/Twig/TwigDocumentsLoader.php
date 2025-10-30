<?php

namespace FWK\Twig;

use FWK\Twig\Environment;
use FWK\Core\Resources\LcFWK;
use FWK\Enums\TwigAutoescape;
use SDK\Core\Resources\Environment as ResourcesEnvironment;

/**
 * This is the TwigDocumentsLoader class.
 * This class facilitates the creation and initialization of the Twig Environment.
 *
 * @see TwigLoader::load()
 * @see TwigLoader::render()
 *
 * @package FWK\Twig
 */
final class TwigDocumentsLoader implements TwigLoaderInterface {

    private $twig;

    private $data;

    private function getTheme(): string {
        return SITE_PATH . DOCUEMENT_TEMPLATES_PATH;
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
    public function load(array $data = [], int $autoescape = TwigAutoescape::AUTOESCAPE_NEW_STRATEGY, bool $loadCore = true) {
        $loader = new \Twig\Loader\FilesystemLoader([self::getTheme()]);
        $this->data = $data;
        $twigOptions = [];
        $twigOptions['debug'] = ResourcesEnvironment::get('DEVEL');
        $twigOptions['charset'] = CHARSET;
        $twigOptions['cache'] = $this->getCache();
        $twigOptions['auto_reload'] = LcFWK::getTwigOptionAutoreload();
        $twigOptions['strict_variables'] = LcFWK::getTwigOptionStrictVariables();
        $twigOptions['optimizations'] = LcFWK::getTwigOptionOptimizations();
        $this->twig = new Environment($loader, $twigOptions);
        $this->twig->addExtension(new \Twig\Extension\StringLoaderExtension());
    }

    /**
     * This method uses Twig Environment to render the page and returns the renderization. 
     * The renderization is based on the Theme provided in the TwigLoader creation, 
     * the data provided with the load method and the format specified by parameter(by default html).
     *
     * @param string $content
     *            -> If different to null, it overrides the theme content to load in Twig
     *            
     * @return string
     */
    public function render(String $content = null, String $layout = null, String $format = 'html') {
        foreach ($this->data as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }
        $renderization = $this->twig->render($content, []);
        return $renderization;
    }

    private function getCache() {
        if (LcFWK::getTwigOptionCache()) {
            return new TwigCache($this->getTheme());
        }
        return false;
    }
}
