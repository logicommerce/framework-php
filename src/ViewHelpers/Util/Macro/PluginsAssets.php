<?php

namespace FWK\ViewHelpers\Util\Macro;

use SDK\Enums\AssetAmbience;
use SDK\Enums\AssetPageType;
use SDK\Enums\AssetPosition;
use SDK\Enums\AssetScopeType;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\ViewHelpers\ViewHelper;

/**
 * This is the PluginsAssets class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the assets output.
 *
 * @see PluginsAssets::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class PluginsAssets {

    public array $assets = [];

    public string $ambience = '';

    public string $position = '';

    public string $type = '';

    /**
     * Constructor method for PluginsAssets
     *
     * @see PluginsAssets
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
            $this->ambience = AssetAmbience::ALL;
        }
        if (!AssetAmbience::isValid($this->ambience)) {
            throw new CommerceException("The value of [ambience] argument: '" . $this->ambience . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        } elseif (strlen($this->position) && !AssetPosition::isValid($this->position)) {
            throw new CommerceException("The value of [position] argument: '" . $this->position . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        } elseif (strlen($this->type) && !AssetScopeType::isValid($this->type)) {
            throw new CommerceException("The value of [type] argument: '" . $this->type . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'assets' => $this->assets,
            'ambience' => $this->ambience,
            'position' => $this->position,
            'type' => $this->type
        ];
    }
}
