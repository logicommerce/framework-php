<?php

namespace FWK\Twig;

/**
 * This is the Environment class.
 * This class extends \Twig\Environment, see this class.
 *
 * @see Environment::generateKey()
 * 
 * @see \Twig\Environment
 *
 * @package FWK\Twig
 */
class Environment extends \Twig\Environment {

    private array $addedLcFunctions = [];

    private array $addedLcExtensions = [];

    /**
     * Returns the Lc functions added to the environment
     * 
     * @param string $site Path for the cache directory.
     */
    public function getAddedLcFunctions(): array {
        return $this->addedLcFunctions;
    }

    /**
     * Adds the functions key and value to added Lc functions registry
     * 
     * @param string $clsFunctions 
     * 
     * @return bool
     */
    public function addLcFunction(string $clsFunctionsKey, mixed $value): void {
        $this->addedLcFunctions[$clsFunctionsKey] = $value;
    }

    /**
     * Returns the Lc extensions added to the environment
     * 
     * @param string $site Path for the cache directory.
     */
    public function getAddedLcExtensions(): array {
        return $this->addedLcExtensions;
    }

    /**
     * Adds the extensions key and value to added Lc extensions registry
     * 
     * @param string $clsextensions 
     * 
     * @return bool
     */
    public function addLcExtension(string $clsExtensionsKey, mixed $value): void {
        $this->addedLcExtensions[$clsExtensionsKey] = $value;
    }

}

