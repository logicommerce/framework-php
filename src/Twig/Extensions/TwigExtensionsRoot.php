<?php

namespace FWK\Twig\Extensions;

use Twig\Environment;

/**
 * This is the TwigExtensionsRoot class.
 * This class extends FWK\Twig\Extensions\TwigExtensions, see this class.
 * <br>This class is in charge to encapsulate usefull root Extensions for Twig and provide a method to add them to the Twig Environment you want.
 *
 * @see TwigExtensions
 *
 * @package FWK\Twig\Extensions
 */
class TwigExtensionsRoot extends TwigExtensions {

    /**
     * This method adds the Twig Extensions and filters defined by this class (see them) to the Twig Environment passed by parameter.
     * 
     * @param Environment $twig
     */
    protected static function addLocalExtensions(Environment $twig): void {
    }
}
