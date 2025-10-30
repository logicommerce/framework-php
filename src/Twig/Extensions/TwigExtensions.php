<?php

namespace FWK\Twig\Extensions;

use \Twig\Environment;

/**
 * This is the TwigExtensions base class.
 * A TwigExtensions class is in charge to encapsulate usefull Extensions for Twig and provide a method to add them to the Twig Environment you want.
 * 
 * @abstract
 *
 * @see TwigExtensions::addLocalExtensions() 
 * @see TwigExtensions::isAdded()
 * @see TwigExtensions::addExtensions()
 *
 * @package FWK\Twig\Extensions
 */
abstract class TwigExtensions {

    /**
     * This method adds the Twig Extensions defined by this TwigExtensions class to the Twig Environment passed by parameter. 
     * 
     * @param Environment $twig
     */
    abstract protected static function addLocalExtensions(Environment $twig): void;

    /**
     * This method returns true if the Twig Extensions defined in this TwigExtensions 
     * class has been already added to the Twig Environment passed by parameter, it returns False otherwise.
     * 
     * @param Environment $twig
     * 
     * @return bool
     */
    public static function isAdded(Environment $twig): bool {
        $addedExtensions = method_exists($twig, 'getAddedLcExtensions')?$twig->getAddedLcExtensions():[];
        return (isset($addedExtensions[static::getClassName()]) && $addedExtensions[static::getClassName()]);
    }

    /**
     * This method adds, if they were not added before, the Twig Extensions defined by this TwigExtensions class to the Twig environment passed by parameter.
     * 
     * @param Environment $twig
     * 
     * @return void
     */
    public static function addExtensions(Environment $twig): void {
        static::addLocalExtensions($twig);
        if(method_exists($twig, 'addLcExtension')){
            $twig->addLcExtension(static::getClassName(), true);
        }
    }

    private static function getClassName():string{
        return str_replace('\\', '_', static::class);
    }
}
