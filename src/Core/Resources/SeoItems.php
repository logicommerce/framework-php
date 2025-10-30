<?php

namespace FWK\Core\Resources;

use SDK\Dtos\Common\Alternate;
use SDK\Core\Interfaces\SEOElementInterface;
use SDK\Dtos\Common\Route;
use SDK\Core\Dtos\Pagination;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;

/**
 * This is the Metatags class.
 * This class has the logic to work with Seo Items
 *
 * @see self::VIEWPORT
 * @see self::DESCRIPTION
 * @see self::KEYWORDS
 * @see self::GENERATOR
 * @see self::ROBOTS
 * @see self::AUTHOR Facebook
 * @see self::FB_PAGE_ID
 * @see self::FB_ADMINS
 * @see self::OG_TYPE
 * @see self::OG_TITLE
 * @see self::OG_URL
 * @see self::OG_DESCRIPTION
 * @see self::OG_IMAGE
 * @see self::OG_LOCALE
 * @see self::OG_SITE_NAME Twitter
 * @see self::TWITTER_CARD
 * @see self::TWITTER_CARD_CONTENT_SUMMARY
 * @see self::TWITTER_CARD_CONTENT_PHOTO
 * @see self::TWITTER_CARD_CONTENT_PLAYER
 * @see self::TWITTER_URL
 * @see self::TWITTER_TITLE
 * @see self::TWITTER_DESCRIPTION
 * @see self::TWITTER_IMAGE
 * @see self::TWITTER_SITE
 *
 * @see self::setSeoSettingsValues()
 * @see self::setPaginationValues()
 * @see self::getMetatags()
 * @see self::getTitle()
 * @see self::getCanonical()
 * @see self::getLinkRelNext()
 * @see self::getLinkRelPrev()
 * @see self::getAlternates()
 * @see self::addMetatag()
 * @see self::getMetatag()
 * @see self::outputMetatags()
 * @see self::outputTitle()
 * @see self::outputCanonical()
 * @see self::outputLinkRelPrevNext()
 * @see self::outputAlternates()
 *
 * @package FWK\Core\Resources
 */
class SeoItems {

    public const VIEWPORT = 'viewport';

    public const DESCRIPTION = 'description';

    public const KEYWORDS = 'keywords';

    public const GENERATOR = 'generator';

    public const ROBOTS = 'robots';

    public const AUTHOR = 'author';

    public const FB_PAGE_ID = 'fb:page';

    public const FB_ADMINS = 'fb:admins';

    public const OG_LOCALE = 'og:locale';

    public const OG_SITE_NAME = 'og:site_name';

    public const OG_TYPE = 'og:type';

    public const OG_TITLE = 'og:title';

    public const OG_URL = 'og:url';

    public const OG_DESCRIPTION = 'og:description';

    public const OG_IMAGE = 'og:image';

    public const TWITTER_CARD = 'twitter:card';

    public const TWITTER_CARD_CONTENT_SUMMARY = "summary";

    public const TWITTER_CARD_CONTENT_PHOTO = "photo";

    public const TWITTER_CARD_CONTENT_PLAYER = "player";

    public const TWITTER_URL = 'twitter:url';

    public const TWITTER_TITLE = 'twitter:title';

    public const TWITTER_DESCRIPTION = 'twitter:description';

    public const TWITTER_IMAGE = 'twitter:image';

    public const TWITTER_SITE = 'twitter:site';

    private ?Language $language = null;

    private ?Route $route = null;

    private ?SEOElementInterface $seoSettings = null;

    private array $metatags = [];

    private string $title = '';

    private string $canonical = '';

    private string $linkRelNext = '';

    private string $linkRelPrev = '';

    private array $alternates = [];

    /**
     * Constructor.
     *
     * @param SEOElementInterface $seoSettings
     */
    public function __construct(Route $route, Language $language, ?SEOElementInterface $seoSettings = null, ?Pagination $pagination = null) {
        $this->language = $language;
        $this->route = $route;
        $this->addMetatag((new Metatag())->setHttpEquiv('content-language')->setContent($this->route->getLanguage()));
        if ($seoSettings !== null) {
            $this->setSeoSettingsValues($seoSettings);
        } elseif (!is_null($this->route->getMetadata())) {
            $this->setSeoSettingsValues($this->route->getMetadata()->getLanguage());
        }
        if ($pagination !== null) {
            $this->setPaginationValues($pagination);
        }
        $this->alternates = $route->getAlternates();
        $this->canonical = $route->getCanonical();
    }

    /**
     * This method sets the SEO values associated to pagination
     *
     * @param SEOElementInterface $seoSettings
     */
    public function setSeoSettingsValues(SEOElementInterface $seoSettings): void {
        $this->seoSettings = $seoSettings;
        $this->addMetatag((new Metatag())->setName(self::DESCRIPTION)->setContent($seoSettings->getMetaDescription()));
        $this->addMetatag((new Metatag())->setName(self::KEYWORDS)->setContent($seoSettings->getKeywords()));
        $this->addMetatag((new Metatag())->setName(self::ROBOTS)->setContent(
            ($this->route->getMetadata()->getIndexable() ? 'index' : 'noindex') . ', ' . ($this->route->getMetadata()->getLinkFollowing() ? 'follow' : 'nofollow')
        ));
        $this->title = $seoSettings->getTitle();
    }

    /**
     * This method sets the SEO values associated to pagination
     *
     * @param Pagination $pagination
     */
    public function setPaginationValues(Pagination $pagination): void {
        /* Disable add description METATAGS_PAGE_NUMBER
            $addToDescription = '';
            if ($pagination->getPage() !== $pagination->getTotalPages()) {
                $patterns = array();
                $patterns[0] = '/{{page}}/';
                $patterns[1] = '/{{pages}}/';
                $replacements = array();
                $replacements[1] = $pagination->getPage();
                $replacements[0] = $pagination->getTotalPages();
                $addToDescription = preg_replace($patterns, $replacements, $this->language->getLabelValue(LanguageLabels::METATAGS_PAGE_NUMBER));
            } else {
                $addToDescription = $this->language->getLabelValue(LanguageLabels::METATAGS_VIEWING_ALL);
            }
            $metaDescription = $this->getMetatag(self::DESCRIPTION);
            $metaDescription->addContent('| ' . $addToDescription);
            $this->addMetatag($metaDescription);
        */

        if ($pagination->getPage() == 2) {
            $this->linkRelPrev = $this->route->getCanonical();
        } elseif ($pagination->getPage() > 1) {
            $this->linkRelPrev = $this->route->getCanonical() . Utils::parseArrayToPathParameters(Utils::addParamsToRequest([
                Parameters::PAGE => $pagination->getPage() - 1
            ]));
        }
        if ($pagination->getPage() < $pagination->getTotalPages()) {
            $this->linkRelNext = $this->route->getCanonical() . Utils::parseArrayToPathParameters(Utils::addParamsToRequest([
                Parameters::PAGE => $pagination->getPage() + 1
            ]));
        }
    }

    /**
     * This method sets the metatags
     *
     * @return Metatag[]
     */
    public function getMetatags(): array {
        return $this->metatags;
    }

    /**
     * This method sets the title
     *
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * This method sets the canonical
     *
     * @return string
     */
    public function getCanonical(): string {
        return $this->canonical;
    }

    /**
     * This method sets the linkRelNext
     *
     * @return string
     */
    public function getLinkRelNext(): string {
        return $this->linkRelNext;
    }

    /**
     * This method sets the linkRelPrev
     *
     * @return string
     */
    public function getLinkRelPrev(): string {
        return $this->linkRelPrev;
    }

    /**
     * This method sets the alternates
     *
     * @return Alternate[]
     */
    public function getAlternates(): array {
        return $this->alternates;
    }

    /**
     * This Method returns add a metatag
     *
     * @param string $name
     *
     * @return Metatag|NULL
     */
    public function addMetatag(Metatag $value) {
        if (strlen($value->getName())) {
            $this->metatags[$value->getName()] = $value;
        } elseif (strlen($value->getHttpEquiv())) {
            $this->metatags[$value->getHttpEquiv()] = $value;
        }
    }

    /**
     * This Method returns the named metatag
     *
     * @param string $name
     *
     * @return Metatag|NULL
     */
    public function getMetatag(string $name): ?Metatag {
        if (isset($this->metatags[$name])) {
            return $this->metatags[$name];
        } else {
            return null;
        }
    }

    /**
     * This method returns the full output of the metatags.
     *
     * @return string
     */
    public function outputMetatags(): string {
        $output = '<meta charset="' . CHARSET . '">';

        foreach ($this->metatags as $metatag) {
            $output .= $metatag->output();
        }
        return $output;
    }

    /**
     * This method returns the title output
     *
     * @return string
     */
    public function outputTitle(): string {
        return '<title>' . $this->title . '</title>';
    }

    /**
     * This method returns the canonical output.
     *
     * @return string
     */
    public function outputCanonical(): string {
        return '<link rel="canonical" href="' . $this->canonical . '">' . $this->outputLinkRelPrevNext();
    }

    /**
     * This method returns the canonical output.
     *
     * @return string
     */
    public function outputLinkRelPrevNext(): string {
        $output = '';
        if (strlen($this->linkRelPrev)) {
            $output .= '<link rel="prev" href="' . $this->linkRelPrev . '">';
        }
        if (strlen($this->linkRelNext)) {
            $output .= '<link rel="next" href="' . $this->linkRelNext . '">';
        }
        return $output;
    }

    /**
     * This method returns the output of alternates.
     *
     * @return string
     */
    public function outputAlternates(): string {
        $output = '';
        foreach ($this->alternates as $alternate) {
            if ($alternate instanceof Alternate) {
                $output .= '<link rel="alternate" hreflang="' . $alternate->getHreflang() . '" href="' . $alternate->getHref() . '">';
            }
        }
        return $output;
    }
}
