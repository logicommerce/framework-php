<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use SDK\Enums\RecommendItemType;

/**
 * This is the ButtonRecommend class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's recommend button.
 *
 * @see ButtonRecommend::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class ButtonRecommend {

    public ?int $id = null;

    public string $class = '';

    public ?string $type = null;

    private ?string $classType = null;

    /**
     * Constructor method for ButtonRecommend class.
     * 
     * @see ButtonRecommend
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
        if (is_null($this->id)) {
            throw new CommerceException("The value of [id] argument: '" . $this->id . "' is required " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }
        if (is_null($this->type)) {
            $this->type = RecommendItemType::PRODUCT;
        }

        $this->classType = lcfirst(str_replace('_', '', ucwords(strtolower(ucfirst($this->type)), '_')));

        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'id' => $this->id,
            'class' => $this->class,
            'type' => $this->type,
            'classType' => $this->classType,
        ];
    }
}
