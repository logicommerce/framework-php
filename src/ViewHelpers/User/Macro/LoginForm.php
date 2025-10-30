<?php

namespace FWK\ViewHelpers\User\Macro;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\Core\Exceptions\CommerceException;
use FWK\Core\Form\Form;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use SDK\Core\Dtos\ElementCollection;
/**
 * This is the LoginForm class, a macro class for the userViewHelper.
 * The purpose of this class is to encapsulate the logic that calculates the view parameters for the user's login form.
 *
 * @see LoginForm::getViewParameters()
 *
 * @package FWK\ViewHelpers\User\Macro
 */
class LoginForm {

    public ?Form $form = null;

    public bool $showLabel = true;

    public bool $showPlaceholder = true;

    public string $redirect = '';

    public string $lostPasswordRedirect = '';

    public string $registerRedirect = '';

    public array $userWarnings = [];

    public bool $showLostPasswordLink = true;

    public bool $showCreateAccountLink = true;

    public ?ElementCollection $oauthPlugins = null;

    /**
     * Constructor method for LoginForm
     * 
     * @see LoginForm
     * 
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        ViewHelper::mergeArguments($this, $arguments);
    }

    /**
     * This method returns all calculated arguments and new parameters for UserViewHelper.php
     *
     * @return array
     */
    public function getViewParameters(): array {
        if (is_null($this->form)) {
            throw new CommerceException("The value of [form] argument: '" . $this->form . "' is required " . self::class, CommerceException::VIEW_HELPER_ARGUMENT_REQUIRED);
        }
        return $this->getProperties();
    }

    /**
     * Return macro use properties
     *
     * @return array
     */
    protected function getProperties(): array {
        $this->oauthPlugins = Loader::service(Services::PLUGIN)->getOauthPlugins();
        return [
            'form' => $this->form,
            'showLabel' => $this->showLabel,
            'showPlaceholder' => $this->showPlaceholder,
            'redirect' => $this->redirect,
            'lostPasswordRedirect' => $this->lostPasswordRedirect,
            'registerRedirect' => $this->registerRedirect,
            'showLostPasswordLink' => $this->showLostPasswordLink,
            'showCreateAccountLink' => $this->showCreateAccountLink,
            'oauthPlugins' => $this->oauthPlugins,
            'userWarnings' => $this->userWarnings
        ];
    }
}
