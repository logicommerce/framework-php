<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Element;
use SDK\Core\Dtos\Traits\ElementTrait;

/**
 * This is the 'BlogPostViewOptionSortItems' class, a DTO class for the theme configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see ViewOption::getAvailableTemplates()
 *
 * @see Element
 * 
 * @uses ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class BlogPostViewOptionSortItems extends Element {
    use ElementTrait;

    public const ID = 'id';

    public const PUBLICATIONDATE = 'publicationdate';

    public const HITS = 'hits';

    public const LIKES = 'likes';

    public const DISLIKES = 'dislikes';

    public const VOTES = 'votes';

    public const RATE = 'rate';

    private ?ViewOptionSortItem $id = null;

    private ?ViewOptionSortItem $publicationdate = null;

    private ?ViewOptionSortItem $hits = null;

    private ?ViewOptionSortItem $likes = null;

    private ?ViewOptionSortItem $dislikes = null;

    private ?ViewOptionSortItem $votes = null;

    private ?ViewOptionSortItem $rate = null;


    /**
     * This method returns id sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getId(): ?ViewOptionSortItem {
        return $this->id;
    }

    private function setId(array $id): void {
        $this->id = new ViewOptionSortItem($id);
    }

    /**
     * This method returns publicationdate sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getPublicationdate(): ?ViewOptionSortItem {
        return $this->publicationdate;
    }

    private function setPublicationdate(array $publicationdate): void {
        $this->publicationdate = new ViewOptionSortItem($publicationdate);
    }

    /**
     * This method returns hits sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getHits(): ?ViewOptionSortItem {
        return $this->hits;
    }

    private function setHits(array $hits): void {
        $this->hits = new ViewOptionSortItem($hits);
    }

    /**
     * This method returns likes sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getLikes(): ?ViewOptionSortItem {
        return $this->likes;
    }

    private function setLikes(array $likes): void {
        $this->likes = new ViewOptionSortItem($likes);
    }

    /**
     * This method returns dislikes sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getDislikes(): ?ViewOptionSortItem {
        return $this->dislikes;
    }

    private function setDislikes(array $dislikes): void {
        $this->dislikes = new ViewOptionSortItem($dislikes);
    }

    /**
     * This method returns votes sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getVotes(): ?ViewOptionSortItem {
        return $this->votes;
    }

    private function setVotes(array $votes): void {
        $this->votes = new ViewOptionSortItem($votes);
    }

    /**
     * This method returns rate sort configuration.
     *
     * @return ViewOptionSortItem|NULL
     */
    public function getRate(): ?ViewOptionSortItem {
        return $this->rate;
    }

    private function setRate(array $rate): void {
        $this->rate = new ViewOptionSortItem($rate);
    }
}
