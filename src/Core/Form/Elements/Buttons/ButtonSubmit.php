<?php

namespace FWK\Core\Form\Elements\Buttons;

use FWK\Core\Form\Elements\Button;
use FWK\Core\Form\Elements\Form;

/**
 * This is the ButtonSubmit class. This class represents a form button of type submit.
 * This class extends Button (FWK\Core\Form\Elements\Button), see this class.
 *
 * @see ButtonSubmit::setFormenctype()
 * @see ButtonSubmit::getFormenctype()
 * @see ButtonSubmit::setFormaction()
 * @see ButtonSubmit::getFormaction()
 * @see ButtonSubmit::setFormmethod()
 * @see ButtonSubmit::getFormmethod()
 * @see ButtonSubmit::setFormtarget()
 * @see ButtonSubmit::getFormtarget()
 * @see ButtonSubmit::setFormnovalidate()
 * @see ButtonSubmit::getFormnovalidate()
 * 
 * @see Button
 *
 * @package FWK\Core\Form\Elements\Buttons
 */
class ButtonSubmit extends Button {

    public const FORMENCTYPE_APPLICATION = 'application/x-www-form-urlencoded';

    public const FORMENCTYPE_MULTIPART = 'multipart/form-data';

    public const FORMENCTYPE_TEXT = 'text/plain';

    public const TYPE = 'submit';

    protected string $formaction = '';

    protected string $formenctype = '';

    protected string $formmethod = '';

    protected string $formtarget = '';

    protected bool $formnovalidate = false;
    
    protected bool $disabled = true;

    /**
     * This method sets the 'formenctype' attribute of the button and returns self.
     * 
     * @param string $formenctype Valid values: ButtonSubmit::FORMENCTYPE_APPLICATION, ButtonSubmit::FORMENCTYPE_MULTIPART, ButtonSubmit::FORMENCTYPE_TEXT
     * 
     * @return self
     */
    public function setFormenctype(string $formenctype): self {
        if($formenctype === self::FORMENCTYPE_APPLICATION || $formenctype === self::FORMENCTYPE_MULTIPART || $formenctype === self::FORMENCTYPE_TEXT) {
            $this->formenctype = $formenctype;
        }
        return $this;
    }

    /**
     * This method returns the current value of the 'formenctype' attribute.
     * 
     * @return string
     */
    public function getFormenctype(): string {
        return $this->formenctype;
    }

    /**
     * This method sets the 'formaction' attribute of the button and returns self.
     * 
     * @param string $formaction
     * 
     * @return self
     */
    public function setFormaction(string $formaction): self {
        $this->formaction = $formaction;
        return $this;
    }

    /**
     * This method returns the current value of the 'formaction' attribute.
     * 
     * @return string
     */
    public function getFormaction(): string {
        return $this->formaction;
    }

    /**
     * This method sets the 'formmethod' attribute of the button and returns self.
     * 
     * @param string $formmethod Valid values: Form::METHOD_GET, Form::METHOD_POST
     * 
     * @return self
     */
    public function setFormmethod(string $formmethod): self {
        if($formmethod === Form::METHOD_GET || $formmethod === Form::METHOD_POST) {
            $this->formmethod = $formmethod;
        }
        return $this;
    }

    /**
     * This method returns the current value of the 'formmethod' attribute.
     * 
     * @return string
     */
    public function getFormmethod(): string {
        return $this->formmethod;
    }

    /**
     * This method sets the 'formtarget' attribute of the button and returns self.
     * 
     * @param string $formtarget
     * 
     * @return self
     */
    public function setFormtarget(string $formtarget): self {
        $this->formtarget = $formtarget;
        return $this;
    }

    /**
     * This method returns the current value of the 'formtarget' attribute.
     * 
     * @return string
     */
    public function getFormtarget(): string {
        return $this->formtarget;
    }

    /**
     * This method sets the 'formnovalidate' attribute of the button and returns self.
     * 
     * @param bool $formnovalidate
     * 
     * @return self
     */
    public function setFormnovalidate(bool $formnovalidate): self {
        $this->formnovalidate = $formnovalidate;
        return $this;
    }

    /**
     * This method returns the current value of the 'formnovalidate' attribute.
     * 
     * @return bool
     */
    public function getFormnovalidate(): bool {
        return $this->formnovalidate;
    }
}
