<?php

namespace FWK\Twig\Functions;

use \Twig\Environment;

/**
 * This is the TwigFunctions base class.
 * A TwigFunctions class is in charge to encapsulate usefull functions for Twig and provide a method to add them to the Twig Environment you want.
 * 
 * @abstract
 *
 * @see TwigFunctions::addLocalFunctions() 
 * @see TwigFunctions::isAdded()
 * @see TwigFunctions::addFunctions()
 *
 * @package FWK\Twig\Functions
 */
abstract class TwigFunctions {

    /**
     * This method adds the Twig functions defined by this TwigFunctions class to the Twig Environment passed by parameter. 
     * 
     * @param Environment $twig
     */
    abstract protected static function addLocalFunctions(Environment $twig): void;

    /**
     * This method returns true if the Twig functions defined in this TwigFunctions 
     * class has been already added to the Twig Environment passed by parameter, it returns False otherwise.
     * 
     * @param Environment $twig
     * 
     * @return bool
     */
    public static function isAdded(Environment $twig): bool {
        $addedFunctions = method_exists($twig, 'getAddedLcFunctions')?$twig->getAddedLcFunctions():[];
        return (isset($addedFunctions[static::getClassName()]) && $addedFunctions[static::getClassName()]);
    }

    /**
     * This method adds, if they were not added before, the Twig functions defined by this TwigFunctions class to the Twig environment passed by parameter.
     * 
     * @param Environment $twig
     * 
     * @return void
     */
    public static function addFunctions(Environment $twig): void {
        static::addLocalFunctions($twig);
        if(method_exists($twig, 'addLcFunction')){
            $twig->addLcFunction(static::getClassName(), true);
        }
    }

    private static function getClassName():string{
        return str_replace('\\', '_', static::class);
    }
}
