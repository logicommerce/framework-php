<?php

namespace FWK\Dtos\Basket;

use SDK\Dtos\Basket\BasketRow;
use SDK\Dtos\Basket\BasketRowData as SDKBasketRowData;

/**
 * This is the BasketRowData class
 *
 * @see BasketRowData::getBasketRow()
 * @see BasketRowData::setMessage()
 *
 * @package FWK\Dtos\Basket
 */
class BasketRowData extends SDKBasketRowData{

    protected ?BasketRow $basketRow = null;
    
    /**
     * Returns the basketRow
     *
     * @return null|BasketRow
     */
    public function getBasketRow(): ?BasketRow {
        return $this->basketRow;
    }

    /**
     * Sets the basketRow
     *
     * @param array|BasketRow 
     */
    public function setBasketRow(array|BasketRow $basketRow): void {
        if($basketRow instanceof BasketRow){
            $this->basketRow = $basketRow;
        }else{
            $this->basketRow = new BasketRow($basketRow);
        }
    }

}