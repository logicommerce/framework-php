<?php

namespace FWK\Core\Form\Elements\Inputs;
                
use FWK\Core\Form\Elements\Input;

/**
 * This is the InputSubmit class. This class represents a form input of type 'submit'.
 * <br>This class extends Input (FWK\Core\Form\Elements\Input), see this class.
 *
 * @see InputSubmit::setFormenctype()
 * @see InputSubmit::setFormmethod()
 * @see InputSubmit::setFormtarget()
 * @see InputSubmit::setFormnovalidate()
 * @see InputSubmit::getFormenctype()
 * @see InputSubmit::getFormmethod()
 * @see InputSubmit::getFormtarget()
 * @see InputSubmit::getFormnovalidate()
 * 
 * @see Input
 *
 * @package FWK\Core\Form\Elements\Inputs
 */
class InputSubmit extends Input {

    public const TYPE = 'submit';
    
    // <input type="submit" formenctype="multipart/form-data" value="Submit as Multipart/form-data">
    protected string $formenctype = '';
    
    // <input type="submit" formmethod="post" value="Submit using POST">
    protected string $formmethod = '';
    
    // <input type="submit" formtarget="_blank" value="Submit to a new window/tab">
    protected string $formtarget = '';
    
    // <input type="submit" formnovalidate="formnovalidate" value="Submit without validation">
    protected string $formnovalidate = '';
    
    /**
     * Constructor of the InputSubmit.
     * 
     * @param string $value
     */
    public function __construct(string $value = '') {
        $this->value = $value;
        $this->setFilterInput();
    }
    
    /**
     * This method sets the 'formenctype' attribute of the submit input and returns self.
     * 
     * @param string $formenctype
     * 
     * @return self
     */
    public function setFormenctype(string $formenctype): self {
        $this->formenctype = $formenctype;
        return $this;
    }
    
    /**
     * This method sets the 'formmethod' attribute of the submit input and returns self.
     * 
     * @param string $formmethod
     * 
     * @return self
     */
    public function setFormmethod(string $formmethod): self {
        $this->formmethod = $formmethod;
        return $this;
    }
    
    /**
     * This method sets the 'formtarget' attribute of the submit input and returns self.
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
     * This method sets the 'formnovalidate' attribute of the submit input and returns self.
     * 
     * @param string $formnovalidate
     * 
     * @return self
     */
    public function setFormnovalidate(string $formnovalidate): self {
        $this->formnovalidate = $formnovalidate;
        return $this;
    }
    
    /**
     * This method returns the current value of the 'formenctype' attribute.
     * 
     * @return string
     */
    public function getFormenctype():string{
        return $this->formenctype;
    }
    
    /**
     * This method returns the current value of the 'formmethod' attribute.
     * 
     * @return string
     */
    public function getFormmethod():string{
        return $this->formmethod;
    }
    
    /**
     * This method returns the current value of the 'formtarget' attribute.
     * 
     * @return string
     */
    public function getFormtarget():string{
        return $this->formtarget;
    }
    
    /**
     * This method returns the current value of the 'formnovalidate' attribute.
     * 
     * @return string
     */
    public function getFormnovalidate():string{
        return $this->formnovalidate;
    }
    
}