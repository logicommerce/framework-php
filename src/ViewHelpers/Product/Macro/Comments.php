<?php

namespace FWK\ViewHelpers\Product\Macro;

use SDK\Core\Dtos\ProductCommentCollection;
use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the Comments class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's comments.
 *
 * @see Comments::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class Comments {

    public ?ProductCommentCollection $comments = null;

    private float $roundedRating = 0.0;

    private int $roundedIntRating = 0;

    private int $worstRating = 5;

    private int $bestRating = 0;

    /**
     * Constructor method for Comments class.
     * 
     * @see Comments
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->comments)) {
            throw new CommerceException("The value of [comments] argument: '" . $this->comments . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        $this->setRoundedRatings();
        $this->setWorstAndBestRating();

        return $this->getProperties();
    }

    /**
     * Set rounded 2 decimals rating and integer rounded rating
     *
     * @return void
     */
    private function setRoundedRatings(): void {
        $this->roundedRating = round($this->comments->getRating(), 2, PHP_ROUND_HALF_UP);
        $this->roundedIntRating = round($this->comments->getRating(), 0, PHP_ROUND_HALF_UP);
    }

    /**
     * Set worst and best rating of all comments
     *
     * @return void
     */
    private function setWorstAndBestRating(): void {
        foreach ($this->comments->getItems() as $comment) {
            if ($comment->getRating() < $this->worstRating) {
                $this->worstRating = $comment->getRating();
            }
            if ($comment->getRating() > $this->bestRating) {
                $this->bestRating = $comment->getRating();
            }
        }
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'comments' => $this->comments,
            'roundedRating' => $this->roundedRating,
            'roundedIntRating' => $this->roundedIntRating,
            'worstRating' => $this->worstRating,
            'bestRating' => $this->bestRating
        ];
    }
}
