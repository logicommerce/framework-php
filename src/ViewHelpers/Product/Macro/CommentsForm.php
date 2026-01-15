<?php

namespace FWK\ViewHelpers\Product\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Enums\Parameters;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use FWK\Core\Theme\Dtos\FormComments;

/**
 * This is the CommentsForm class, a macro class for the productViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the product's comments form.
 *
 * @see CommentsForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Product\Macro
 */
class CommentsForm {

    private ?Session $session = null;

    public ?FormComments $configuration = null;

    private bool $output = true;

    public ?Form $form = null;

    /**
     * Constructor method for CommentsForm class.
     *
     * @see CommentsForm
     *
     * @param array $arguments
     */
    public function __construct(array $arguments, ?Session $session) {
        ViewHelper::mergeArguments($this, $arguments);
        $this->session = $session;
    }

    /**
     * This method returns all calculated arguments and new parameters for ProductViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->form)) {
            throw new CommerceException("The value of [form] argument: '" . $this->form . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (is_null($this->configuration)) {
            throw new CommerceException("The value of [configuration] argument: '" . $this->configuration . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        $this->output = $this->setOutput();

        return $this->getProperties();
    }

    /**
     * Remove required contents of comment form input if commentFieldRequired is false
     *
     * @return void
     * @deprecated Use FormFactory to set required attribute
     */
    private function setCommentFieldRequired(): void {
        if ($this->configuration->getCommentFieldRequired() === false) {
            // Remove required from label
            $this->form[Parameters::COMMENT] = str_replace(REQUIRED_FIELD_HTML_FLAG, '', $this->form[Parameters::COMMENT]);
            // Remove required attr input
            $this->form[Parameters::COMMENT] = str_replace('required', '', $this->form[Parameters::COMMENT]);
        }
    }

    /**
     * Return if macro show output.
     * With login always show output, without login only show output if configuration->anonymousRatingEnabled argument is true.
     *
     * @return bool
     */
    private function setOutput(): bool {
        if ($this->session !== null && Utils::isSessionLoggedIn($this->session)) {
            return true;
        } elseif ($this->configuration->getAnonymousRatingEnabled() === true) {
            return true;
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
            'form' => $this->form,
            'configuration' => $this->configuration,
            'output' => $this->output
        ];
    }
}
