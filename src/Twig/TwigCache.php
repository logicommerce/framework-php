<?php

namespace FWK\Twig;

/**
 * This is the TwigCache class.
 * This class extends \Twig\Cache\FilesystemCache, see this class.
 *
 * @see TwigCache::generateKey()
 * 
 * @see \Twig\Cache\FilesystemCache
 *
 * @package FWK\Twig
 */
class TwigCache extends \Twig\Cache\FilesystemCache {

    private $directory;

    private $options;

    private $site;

    /**
     * Constructor. Creates the Twig cache and initializes the cache directory with the specified in the $site parameter.
     * 
     * @param string $site Path for the cache directory.
     */
    public function __construct(string $site = SITE_PATH) {
        $this->directory = rtrim(CACHE_PATH, '\/') . '/twig/';
        $this->options = 0;
        $this->site = $site;
    }

    /**
     * This method overrides FileSystemCache::generateKey() to return a key 
     * based not only in the class name but also in the site path and the provided name. 
     * 
     * 
     * @see \Twig\Cache\FilesystemCache::generateKey()
     */
    public function generateKey(string $name, string $class): string {
        $hash = hash('sha256', $name . $class . $this->site);
        return $this->directory . $hash[0] . $hash[1] . '/' . $hash . '.php';
    }
}
