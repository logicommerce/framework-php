<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

use FWK\Core\Resources\Utils;

/**
 * This is the 'data' attribute trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the 'data' attribute and its set/get methods.
 *
 * @see AttributeDataTrait::setData()
 * @see AttributeDataTrait::getData()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributeDataTrait {

    protected $data = null;

    /**
     * This method sets the 'data' attribute with the given value and returns self.
     * 
     * @param mixed $data
     * 
     * @return self
     */
    public function setData(mixed $data): self {
        $this->data = $data;
        return $this;
    }

    /**
     * This method returns the current value of the 'data' attribute.
     * 
     * @return string
     */
    public function getData(): string {
        return Utils::outputJsonHtmlString($this->data);
    }
}