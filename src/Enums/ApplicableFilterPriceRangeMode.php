<?php

namespace FWK\Enums;

use SDK\Core\Enums\Enum;

/**
 * This is the ApplicableFilterPriceRangeMode enumeration class.
 * This class declares enumerations for applicable filter price range mode.
 * <br> This class extends SDK\Core\Enums\Enum, see this class.
 *
 * @abstract
 *
 * @see ApplicableFilterPriceRangeMode::MODE_RANGE_SLIDER
 * @see ApplicableFilterPriceRangeMode::MODE_RANGE_RADIO_LIST 
 *
 * @see Enum
 *
 * @package FWK\Enums
 */
abstract class ApplicableFilterPriceRangeMode extends Enum {

    public const MODE_RANGE_SLIDER = 'modeRangeSlider';

    public const MODE_RANGE_RADIO_LIST = 'modeRangeRadioList';
}
