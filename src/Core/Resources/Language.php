<?php

namespace FWK\Core\Resources;

use FWK\Enums\RouteItems;
use SDK\Application;
use SDK\Core\Resources\Environment;

/**
 * This is the Language class.
 * The purpose of this class is to encapsulate the information of the language sheet of the set language.
 *
 * @see Language::getInstance()
 * @see Language::reloadInstance()
 * @see Language::resetInstance()
 * @see Language::getLanguageSheet()
 * @see Language::getLabelValue()
 * @see Language::defineLanguageSheet()
 * @see Language::getLanguage()
 * @see Language::getLabels()
 * 
 * @package FWK\Core\Resources  
 */
class Language {

    private const DEFAULT_LANGUAGE  = 'en';

    private static ?Language $instance = null;

    private static ?string $language = null;

    protected array $defaultLanguageSheet = [];

    protected array $languageSheet = [];

    protected array $labels = [];

    /**
     * Constructor.
     * It creates a new Language instance and initializes its language to the one set in the session and its corresponding languageSheet.
     * 
     * @param string $language. Language inititials
     */
    final private function __construct(string $language) {
        self::$language = $language;
        $this->defaultLanguageSheet = $this->defineLanguageSheet(true);
        $this->languageSheet = array_merge($this->defineLanguageSheet(true), $this->defineLanguageSheet());
        $this->labels = $this->definedLabels();
    }

    /**
     * This method returns the Language instance (singleton).
     * The singleton instance is created initializating its language to the one set in the session and its corresponding languageSheet.
     * 
     * @param string|NULL $language. Language inititials
     * 
     * @return Language
     */
    final public static function getInstance(string $language = DEFAULT_ROUTE[RouteItems::LANGUAGE]): Language {
        if (self::$instance === null) {
            self::$instance = new Language($language);
        }
        return self::$instance;
    }

    /**
     * This method reloads the Language instance 
     * 
     * @internal
     * It unsets the current instance and generates a new one invoking the Language::resetInstance() and Language::getInstance() methods.
     * 
     * @see Language::resetInstance()
     * @see Language::getInstance()
     * 
     * @param string $language. Language inititials
     * 
     * @return Language
     */
    final public static function reloadInstance(string $language): Language {
        if (self::$instance === null) {
            self::getInstance($language);
        } elseif ($language != self::$language) {
            self::resetInstance();
            self::getInstance($language);
        }
        return self::$instance;
    }

    /**
     * This method unsets the Language instance.
     */
    final public static function resetInstance(): void {
        self::$instance = null;
    }

    /**
     * This method returns the languageSheet.
     * 
     * @return array
     */
    public function getLanguageSheet(): array {
        if (SHOW_LANGUAGE_LABELS && Environment::get('DEVEL')) {
            $develLanguage = [];
            foreach ($this->labels as $key => $value) {
                $develLanguage[$value] = 'LanguageLabels::' . $key;
            }
            return $develLanguage;
        }
        return $this->languageSheet;
    }

    /**
     * This method returns the value of the specified label of the language sheet.
     * 
     * @param string $label Label to get its value from the language sheet.
     * @param string $defaultValue Value to return in case the label is not set in the language sheet. If this param is not specified the considered default value is ''.
     * 
     * @return string
     */
    public function getLabelValue(string $label, string $defaultValue = ''): string {
        if (SHOW_LANGUAGE_LABELS && Environment::get('DEVEL')) {
            return 'LanguageLabels::' . (array_search($label, $this->labels) ?: $label);
        }
        return (isset($this->getLanguageSheet()[$label]) ? $this->getLanguageSheet()[$label] : $defaultValue);
    }

    /**
     * This method returns all the usable labels.
     * 
     * @return array
     */
    public function getLabels(): array {
        return $this->labels;
    }

    /**
     * This method returns the definition of usable labels.
     * 
     * @return array
     */
    protected function definedLabels(): array {
        $labels = [];
        foreach (Loader::LOCATIONS as $location) {
            $class = Loader::getClassFQN('LanguageLabels', $location . 'Enums\\', '');
            if (class_exists($class)) {
                $languageLabelsReflectionClass = new \ReflectionClass($class);
                $labels = $languageLabelsReflectionClass->getConstants();
                break;
            }
        }
        $plugins = Application::getInstance()?->getEcommercePlugins() ?? [];
        foreach ($plugins as $plugin) {
            $class = Loader::getClassFQN('LanguageLabels', 'Plugins\\' . Utils::getCamelFromSnake($plugin->getModule(), '.') . '\\Enums\\', '');
            if (class_exists($class)) {
                $languageLabelsReflectionClass = new \ReflectionClass($class);
                $labels += $languageLabelsReflectionClass->getConstants();
            }
        }
        return $labels;
    }

    /**
     * This method returns the definition of languageSheet.
     * 
     * @param bool $defaultLanguage
     * 
     * @return array
     */
    protected function defineLanguageSheet(bool $defaultLanguage = false): array {
        return array_merge($this->getFWKLanguageSheet($defaultLanguage), $this->getPluginsLanguageSheet($defaultLanguage), $this->getSiteLanguageSheet($defaultLanguage));
    }

    private function getFWKLanguageSheet(bool $defaultLanguage = false): array {
        $languageFile = FWK_LOAD_PATH . '/src/Languages/' . ($defaultLanguage ? $this::DEFAULT_LANGUAGE : self::$language) . '.php';
        if (!is_file($languageFile)) {
            return [];
        }
        return include $languageFile;
    }

    private function getSiteLanguageSheet(bool $defaultLanguage = false): array {
        $languageFile = SITE_PATH . '/src/Languages/' . ($defaultLanguage ? $this::DEFAULT_LANGUAGE : self::$language) . '.php';
        if (!is_file($languageFile)) {
            return [];
        }
        return include $languageFile;
    }

    private function getPluginsLanguageSheet(bool $defaultLanguage = false): array {
        $pluginsLanguageSheet = [];
        $plugins = Application::getInstance()?->getEcommercePlugins() ?? [];
        foreach ($plugins as $plugin) {
            $languageFile = PLUGINS_LOAD_PATH . '/' . Utils::getCamelFromSnake($plugin->getModule(), '.') . '/src/Languages/' . ($defaultLanguage ? $this::DEFAULT_LANGUAGE : self::$language) . '.php';
            if (is_file($languageFile)) {
                $pluginsLanguageSheet = $pluginsLanguageSheet + include $languageFile;
            }
        }
        return $pluginsLanguageSheet;
    }

    /**
     * This method returns the language identifier.
     * 
     * @return string
     */
    public function getLanguage(): string {
        return self::$language;
    }
}
