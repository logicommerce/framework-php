<?php

namespace FWK\ViewHelpers\Util\Macro;

use SDK\Core\Enums\Device;
use SDK\Enums\TrackerAmbience;
use SDK\Enums\TrackerPageType;
use SDK\Enums\TrackerPosition;
use SDK\Enums\TrackerScopeType;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Route;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the Trackers class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the trackers output.
 *
 * @see Trackers::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class Trackers {

    public array $trackers = [];

    public string $ambience = '';

    public string $position = '';

    public string $type = '';

    /**
     * Constructor method for Trackers
     *
     * @see Trackers
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
        if (!strlen($this->ambience)) {
            if ($this->hasDeviceSpecificAmbience()) {
                $this->ambience = in_array(Route::getDevice(), [Device::MOBILE, Device::TABLET], true)
                    ? TrackerAmbience::MOBILE
                    : TrackerAmbience::DESKTOP;
            } else {
                $this->ambience = TrackerAmbience::ALL;
            }
        }
        if (!TrackerAmbience::isValid($this->ambience)) {
            throw new CommerceException("The value of [ambience] argument: '" . $this->ambience . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        } elseif (strlen($this->position) && !TrackerPosition::isValid($this->position)) {
            throw new CommerceException("The value of [position] argument: '" . $this->position . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        } elseif (strlen($this->type) && !TrackerScopeType::isValid($this->type)) {
            throw new CommerceException("The value of [type] argument: '" . $this->type . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }
        return $this->getProperties();
    }

    /**
     * Returns true when at least one script in the collection carries an
     * ambience other than ALL, i.e. the backend has configured per-device
     * filtering for this shop.
     */
    private function hasDeviceSpecificAmbience(): bool {
        foreach ($this->trackers as $tracker) {
            foreach ($tracker->getScripts() as $script) {
                if ($script->getAmbience() !== TrackerAmbience::ALL) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'trackers' => $this->trackers,
            'ambience' => $this->ambience,
            'position' => $this->position,
            'type' => $this->type
        ];
    }
}
