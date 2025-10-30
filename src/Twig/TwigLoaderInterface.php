<?php

namespace FWK\Twig;

use FWK\Enums\TwigAutoescape;

/**
 * This is the TwigLoaderInterface.
 * This interface defines the methods to be defined by those
 * 'Twig loader classes' that implement this interface.
 *
 * @see TcInterface::routeTypeBatchRequestsFilter()

 *
 * @package FWK\Twig
 */
interface TwigLoaderInterface {

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
    public function load(array $data = [], int $autoescape = 0, bool $loadCore = true);

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
    public function render(String $content = null, String $layout = null, String $format = 'html');
}
