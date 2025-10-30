<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use SDK\Dtos\Kimera\KimeraDataRequest;

/**
 * This is the Kimera class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the trackers output.
 *
 * @see Kimera::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class Kimera {

    public ?KimeraDataRequest $kimera = null;

    /**
     * Constructor method for Kimera
     *
     * @see Kimera
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UtilViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        $kimeraJson = "";
        if (!is_null($this->kimera)) {
            $kimeraJson = json_encode($this->kimera);
        }
        return [
            'kimera' => $kimeraJson
        ];
    }
}
