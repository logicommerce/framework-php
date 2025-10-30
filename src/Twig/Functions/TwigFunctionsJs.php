<?php

namespace FWK\Twig\Functions;

use Twig\Environment;

/**
 * This is the TwigFunctionsJs class.
 * This class extends FWK\Twig\Functions\TwigFunctions, see this class.
 * <br>This class is in charge to encapsulate usefull js functions for Twig and provide a method to add them to the Twig Environment you want.
 *
 * @see TwigFunctions
 *
 * @package FWK\Twig\Functions
 */
class TwigFunctionsJs extends TwigFunctions {

    /**
     * This method adds the Twig functions and filters defined by this class (see them) to the Twig Environment passed by parameter.
     * 
     * @param Environment $twig
     */
    protected static function addLocalFunctions(Environment $twig): void {
        if (!TwigFunctionsRoot::isAdded($twig)) {
            TwigFunctionsRoot::addFunctions($twig);
        }
    }
}
