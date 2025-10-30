<?php

namespace FWK\ViewHelpers\Util\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Resources\Language;
use FWK\Enums\LanguageLabels;
use SDK\Enums\RouteType;
use FWK\Dtos\Common\Breadcrumb as ViewHelperDtoBreadcrumb;

/**
 * This is the Breadcrumb class, a macro class for the utilViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the util's breadcrumb.
 *
 * @see Breadcrumb::getViewParameters()
 *
 * @package FWK\ViewHelpers\Util\Macro
 */
class Breadcrumb {

    public const TAG_UL = 'ul';

    public const TAG_DIV = 'div';

    private const TAG_VALUES = [
        self::TAG_UL,
        self::TAG_DIV
    ];

    public bool $showHome = true;

    public bool $showArea = true;

    public int $maxLevels = 0;

    public string $tag = self::TAG_UL;

    public ?array $data = null;

    private int $itemsToShowCounter = 0;

    /**
     * Constructor method for Breadcrumb
     *
     * @see Breadcrumb
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
        if (is_null($this->data)) {
            throw new CommerceException("The value of [data] argument: '" . $this->data . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (!in_array($this->tag, self::TAG_VALUES, true)) {
            throw new CommerceException("The value of [tag] argument: '" . $this->tag . "' not exists in " . self::class, CommerceException::VIEW_HELPER_UNDEFINED_ARGUMENT);
        }

        $responseData = [];
        $language = Language::getInstance();
        foreach ($this->data as $routeItem) {
            $auxItem = $routeItem->toArray();
            $routeItem = new ViewHelperDtoBreadcrumb($auxItem);
            if (preg_match('/^{{.*}}$/', $routeItem->getName())) {
                $wildCard = substr($routeItem->getName(), 2, strlen($routeItem->getName()) - 4);
                $labelValue = (new \ReflectionClassConstant(LanguageLabels::class, 'BREADCRUMB_' . strtoupper($wildCard)))->getValue();
                $label = $language->getLabelValue($labelValue, $routeItem->getName());
                if (!strlen($label)) {
                    continue;
                }
                $auxName = $routeItem->getName();
                $auxName = str_replace('{{' . $wildCard . '}}', $label, $auxName);
                if ($auxName !== $routeItem->getName()) {
                    $routeItem->setName($auxName);
                }
            }
            $routeItem->setShow($this->getItemShow($routeItem));
            if ($this->maxLevels !== 0 && ($this->itemsToShowCounter > $this->maxLevels)) {
                $routeItem->setShow(false);
            }
            $responseData[] = $routeItem;
        }
        $this->data = $responseData;
        return $this->getProperties();
    }

    /**
     * Return if show a Breadcrumb item and increment itemsToShowCounter if return true.
     *
     * @param Breadcrumb $routeItem
     *
     * @return bool
     */
    private function getItemShow(ViewHelperDtoBreadcrumb $routeItem): bool {
        if ($this->showHome === false && $routeItem->getItemType() == RouteType::HOME) {
            return false;
        }
        if ($this->showArea === false && $routeItem->getItemType() == RouteType::AREA) {
            return false;
        }

        $this->itemsToShowCounter++;
        return true;
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        return [
            'showHome' => $this->showHome,
            'showArea' => $this->showArea,
            'maxLevels' => $this->maxLevels,
            'tag' => $this->tag,
            'data' => $this->data
        ];
    }
}
