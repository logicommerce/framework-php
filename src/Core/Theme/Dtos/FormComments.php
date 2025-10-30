<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormComments' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormComments::getMinRatingAmount()
 * @see FormComments::getMaxRatingAmount()
 * @see FormComments::getUserFields()
 * @see FormComments::getDefaultRatingAmount()
 * @see FormComments::getCommentFieldRequired()
 * @see FormComments::getRatingsAllowed()
 * @see FormComments::getFormClass()
 * @see FormComments::getAnonymousRatingEnabled()
 * @see FormComments::getIpStrictEnabled()
 *
 * @see Element
 * @see ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormComments extends Element {
    use ElementTrait;

    public const MIN_RATING_AMOUNT = 'minRatingAmount';

    public const MAX_RATING_AMOUNT = 'maxRatingAmount';

    public const DEFAULT_RATING_AMOUNT = 'defaultRatingAmount';

    public const COMMENT_FIELD_REQUIRED = 'commentFieldRequired';

    public const RATINGS_ALLOWED = 'ratingsAllowed';

    public const FORM_CLASS = 'formClass';

    public const ANONYMOUS_RATING_ENABLED = 'anonymousRatingEnabled';

    public const IP_STRICT_ENABLED = 'ipStrictEnabled';

    private int $minRatingAmount = 1;

    private int $maxRatingAmount = 5;

    private int $defaultRatingAmount = 5;

    private bool $commentFieldRequired = false;

    private int $ratingsAllowed = 10;

    private string $formClass = '';

    private bool $anonymousRatingEnabled = false;

    private bool $ipStrictEnabled = false;

    /**
     * This method returns the minimum rating amount.
     *
     * @return int
     */
    public function getMinRatingAmount(): int {
        return $this->minRatingAmount;
    }

    /**
     * This method returns the maximum rating amount.
     *
     * @return int
     */
    public function getMaxRatingAmount(): int {
        return $this->maxRatingAmount;
    }

    /**
     * This method returns the default rating amount.
     *
     * @return int
     */
    public function getDefaultRatingAmount(): int {
        if ($this->defaultRatingAmount < $this->minRatingAmount) {
            return $this->minRatingAmount;
        } elseif ($this->defaultRatingAmount > $this->maxRatingAmount) {
            return $this->maxRatingAmount;
        }
        return $this->defaultRatingAmount;
    }

    /**
     * This method returns if the comment filed is required.
     *
     * @return bool
     */
    public function getCommentFieldRequired(): bool {
        return $this->commentFieldRequired;
    }

    /**
     * This method returns the number of ratings allowed by user.
     *
     * @return int
     */
    public function getRatingsAllowed(): int {
        return $this->ratingsAllowed;
    }

    /**
     * This method returns the class to set in the comment form.
     *
     * @return string
     */
    public function getFormClass(): string {
        return $this->formClass;
    }

    /**
     * This method returns if the rating is enabled for anonymous users .
     *
     * @return bool
     */
    public function getAnonymousRatingEnabled(): bool {
        return $this->anonymousRatingEnabled;
    }

    /**
     * This method returns if the ip strict is enabled.
     *
     * @return bool
     */
    public function getIpStrictEnabled(): bool {
        return $this->ipStrictEnabled;
    }
}
