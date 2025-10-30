<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;

/**
 * This is the AgencyLogo class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the util's agency logo.
 *
 * @see AgencyLogo::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class AgencyLogo {

    public string $linkRel = 'nofollow noreferrer';

    public string $color = 'default';

    public string $folder = 'agencyLogo';

    public bool $showOnlyText = false;

    public string $name = '';

    public string $id = '';

    public string $class = '';

    public string $classLink = '';

    public string $link = '';

    public string $logo = '';

    public string $extension = '.png';

    private string $logoImgSrc = '';

    /**
     * Constructor method for AgencyLogo
     *
     * @see AgencyLogo
     *
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
        $this->logoImgSrc = $this->folder . '/' . $this->logo . '_' . $this->color . $this->extension;
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
        return [
            'color' => $this->color,
            'showOnlyText' => $this->showOnlyText,
            'linkRel' => $this->linkRel,
            'name' => $this->name,
            'id' => $this->id,
            'class' => $this->class,
            'classLink' => $this->classLink,
            'link' => $this->link,
            'logo' => $this->logo,
            'folder' => $this->folder,
            'logoImgSrc' => $this->logoImgSrc,
        ];
    }
}
