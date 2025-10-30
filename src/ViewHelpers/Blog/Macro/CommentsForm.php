<?php

namespace FWK\ViewHelpers\Blog\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Core\Resources\Session;
use FWK\Core\Resources\Utils;
use SDK\Dtos\Settings\BlogSettings;
use SDK\Enums\BlogPostCommentMode;

/**
 * This is the CommentsForm class, a macro class for the blogViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the blog's comments form.
 *
 * @see CommentsForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\Blog\Macro
 */
class CommentsForm {

    private ?Session $session = null;

    private bool $output = true;

    public ?BlogSettings $blogSettings = null;

    public ?Form $form = null;

    public bool $showTitle = true;

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
     * This method returns all calculated arguments and new parameters for BlogViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->form)) {
            throw new CommerceException("The value of [form] argument: '" . $this->form . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        if (is_null($this->blogSettings)) {
            throw new CommerceException("The value of [blogSettings] argument: '" . $this->blogSettings . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }

        $this->output = $this->setOutput();

        return $this->getProperties();
    }

    /**
     * Return if macro show output.
     * With login always show output, without login only show output if blogSettings->getCommentsMode argument is BlogPostCommentMode::ANONYMOUS_AND_REGISTERED_USERS.
     *
     * @return bool
     */
    private function setOutput(): bool {
        if ($this->session !== null && Utils::isSessionLoggedIn($this->session)) {
            return true;
        } elseif ($this->blogSettings->getCommentsMode() === BlogPostCommentMode::ANONYMOUS_AND_REGISTERED_USERS) {
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
            'blogSettings' => $this->blogSettings,
            'output' => $this->output,
            'showTitle' => $this->showTitle,
        ];
    }
}
