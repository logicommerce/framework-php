<?php

namespace FWK\Services\Traits;

use FWK\Core\Dtos\ElementCollection as DtosElementCollection;
use FWK\Core\Exceptions\CommerceException;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Services\BatchService;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\Parameters\Groups\RelatedItemsParametersGroup;

/**
 * This is the SetRelatedItemsTrait trait.
 * This trait has been created to share common method SetRelatedItemsTrait between FWK services.
 *
 * @see SetRelatedItemsTrait::setRelatedItems()
 *
 * @package FWK\Services\Traits
 */
trait SetRelatedItemsTrait {

    /**
     * This method sets the related items of each of the given ElementCollection.
     * 
     * @internal 
     * It launches a batch request to get the related items of each category in a single request to the API.
     * 
     * @param ElementCollection $elements
     * @param string $type
     *            Optional. This param will set the kind of related we want to retrieve. Valid values are inside RelatedItemsType class.
     *            If not given all the related items will be returned inside the RelatedItems object.
     * @param RelatedItemsParametersGroup $params
     *            object with the needed filters to send to the API resource
     * 
     * @return void
     */
    public function setRelatedItems(ElementCollection &$elements, $type = '', RelatedItemsParametersGroup $params = null): void {
        $batchRequest = new BatchRequests();
        $withRelatedClass = '';
        foreach ($elements as $key => $element) {
            if (in_array(get_class($element), self::RELATED_ITEM_CLASS)) {
                $this->addGetRelatedItems($batchRequest, $key, $element->getId(), $type, $params);
                $withRelatedClass = str_replace('SDK', 'FWK', $element::class);
            } else {
                throw new CommerceException('Invalid element class. ' . get_class($element) . ' is not valid RELATED_ITEM_CLASS', CommerceException::SET_RELATED_ITEMS_TRAIT_INVALID_CLASS);
            }
        }
        $batchResult = BatchService::getInstance()->send($batchRequest);
        $elements = DtosElementCollection::fillFromParentCollection($elements, $withRelatedClass);
        foreach ($elements as $key => $element) {
            $element->setRelatedItems($batchResult[$key]);
        }
    }
}
