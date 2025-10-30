<?php

namespace FWK\Twig\Extensions;

use Twig\Environment;

/**
 * This is the TwigExtensionsJs class.
 * This class extends FWK\Twig\Extensions\TwigExtensions, see this class.
 * <br>This class is in charge to encapsulate usefull js Extensions for Twig and provide a method to add them to the Twig Environment you want.
 *
 * @see TwigExtensions
 *
 * @package FWK\Twig\Extensions
 */
class TwigExtensionsJs extends TwigExtensions {

    /**
     * This method adds the Twig Extensions and filters defined by this class (see them) to the Twig Environment passed by parameter.
     * 
     * @param Environment $twig
     */
    protected static function addLocalExtensions(Environment $twig): void {
        if(!TwigExtensionsRoot::isAdded($twig)) {
            TwigExtensionsRoot::addExtensions($twig);
        }
        
    }
   
}