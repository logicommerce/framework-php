<?php

namespace FWK\Core\Dtos\Traits;

use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Dtos\ElementCollection as SDKElementCollection;
use FWK\Core\Dtos\ElementCollection;
use SDK\Core\Dtos\Factory;

/**
 * This is the Fill From Parent Trait
 *
 * @see FillFromParentTrait::fillFromParent()
 * @see FillFromParentTrait::fillFromParentCollection()
 * 
 * @package FWK\Core\Dtos\Traits
 */
trait FillFromParentTrait {

    /**
     * Returns self object fill with parent properties
     * 
     * @throws CommerceException Object parent parameter must use "SDK\Core\Dtos\Traits\ElementTrait"
     * 
     * @return self
     */
    public static function fillFromParent(object $parent): self {
        if (property_exists($parent::class, 'useElementTrait')) {
            return new self($parent->toArray());
        } else {
            throw new CommerceException('Object parent(' . $parent::class . ') parameter must use "SDK\Core\Dtos\Traits\ElementTrait"', CommerceException::FILL_FROM_PARENT_TRAIT_REQUIRED_ELEMENT_TRAIT);
        }
    }

    /**
     * Returns ElementCollection of the given $class, filling each with the $parent properties.
     * 
     * @return SDKElementCollection
     */
    public static function fillFromParentCollection(SDKElementCollection $parent, string $class): SDKElementCollection {
        $data = $parent->toArray();
        $elementCollectionClass = ElementCollection::getCollectionClass($data, $class);
        $items = [];
        foreach ($parent->getItems() as $key => $item) {
            $itemClass = get_class($item);
            if (is_subclass_of($class, Factory::class, true)) {
                $items[] = $class::getElement($item->toArray());
            } else if (!is_subclass_of($class, $itemClass, true) && !($item instanceof $class)) {
                $newClass = str_replace('\\', '_', $itemClass);
                if (!class_exists($newClass)) {
                    eval('class ' . $newClass . ' extends ' . strip_tags($itemClass) . ' { 
                        use ' . str_replace('FWK\\Dtos', 'FWK\\Core\\Dtos\\Traits', $class) . 'Trait; 
                    }');
                }
                $items[] = new $newClass($item->toArray());
            } else {
                $items[] = new $class($item->toArray());
            }
        }
        $data['items'] = $items;
        return new $elementCollectionClass($data);
    }
}
