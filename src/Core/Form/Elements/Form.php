<?php

namespace FWK\Core\Form\Elements;

use FWK\Core\Form\Elements\AttributeTraits\AttributeAutocompleteTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeClassTrait;
use FWK\Core\Form\Elements\AttributeTraits\AttributeDataTrait;

/**
 * This is the Form element class. 
 * This class encapsulates the gets/sets of the attributes and events of a 'form'.
 * <br>This class extends Element (FWK\Core\Form\Elements\Element), see this class.
 *
 * @see Form::outputElement()
 * @see Form::setId()
 * @see Form::setUId()
 * @see Form::setTarget()
 * @see Form::setMethod()
 * @see Form::setAcceptCharset()
 * @see Form::setEnctype()
 * @see Form::setNovalidate()
 * @see Form::getId()
 * @see Form::getUId()
 * @see Form::getTarget()
 * @see Form::getMethod()
 * @see Form::getAcceptCharset()
 * @see Form::getEnctype()
 * @see Form::getNovalidate()
 * @see Form::getName()
 * @see Form::getAction()
 * @see Form::setOnselect()
 * @see Form::getOnselect()
 * @see Form::setOnsearch()
 * @see Form::getOnsearch()
 * @see Form::setOnreset()
 * @see Form::getOnreset()
 * @see Form::setOninvalid()
 * @see Form::getOninvalid()
 * @see Form::setOninput()
 * @see Form::getOninput()
 * @see Form::setOnfocus()
 * @see Form::getOnfocus()
 * @see Form::setOncontextmenu()
 * @see Form::getOncontextmenu()
 * @see Form::setOnchange()
 * @see Form::getOnchange()
 * @see Form::setOnblur()
 * @see Form::getOnblur()
 * @see Form::setOnsubmit()
 * @see Form::getOnsubmit()
 * 
 * @see Element
 * @see AttributeClassTrait
 * @see AttributeDataTrait
 * @see AttributeAutocompleteTrait
 * 
 * @package FWK\Core\Form\Elements
 */
class Form extends Element {

    use AttributeClassTrait, AttributeDataTrait, AttributeAutocompleteTrait;

    public const TYPE = 'form';

    /**
     * Form method, option 'get'.
     */
    public const METHOD_GET = 'get';

    /**
     * Form method, option 'post'.
     */
    public const METHOD_POST = 'post';

    /**
     * Form target, option '_self'.
     */
    public const TARGET_SELF = '_self';

    /**
     * Form target, option '_blank'.
     */
    public const TARGET_BLANK = '_blank';

    /**
     * Form target, option '_parent'.
     */
    public const TARGET_PARENT = '_parent';

    /**
     * Form target, option '_top'.
     */
    public const TARGET_TOP = '_top';

    protected string $id = '';

    protected string $uId = '';

    protected string $action = '';

    protected string $name = '';

    protected string $enctype = '';

    protected string $target = '';

    protected string $method = '';

    protected string $acceptCharset = '';

    protected bool $novalidate = false;

    protected string $onblur = '';

    protected string $onchange = '';

    protected string $oncontextmenu = '';

    protected string $onfocus = '';

    protected string $oninput = '';

    protected string $oninvalid = '';

    protected string $onreset = '';

    protected string $onsearch = '';

    protected string $onselect = '';

    protected string $onsubmit = '';

    /**
     * Constructor.
     * 
     * @param string $action Specifies an address (url) where to submit the form (default: the submitting page).
     * @param string $name Specifies a name used to identify the form (for DOM usage: document.forms.name).
     */
    public function __construct(string $action, string $name) {
        $this->name = $name;
        $this->action = $action;
    }

    /**
     * 
     * 
     * 
     * @see \FWK\Core\Form\Elements\Element::outputElement()
     */
    public function outputElement(string $name = '', array $richFormList = []): string {
        return '<form' . $this->outputAttributes($this->name, $richFormList) . '>';
    }

    /**
     * This method sets the 'id' form attribute.
     * 
     * @param string $id
     * 
     * @return Form
     */
    public function setId(string $id): Form {
        $this->id = $id;
        return $this;
    }

    /**
     * This method sets the given Unique identifier to the form.
     * 
     * @param string $uId
     * 
     * @return Form
     */
    public function setUId(string $uId): Form {
        $this->uId = $uId;
        return $this;
    }

    /**
     * This method sets the 'target' form attribute: specifies the target of the address in the action attribute (default: Form::TARGET_SELF).  
     * Possible values:
     * <ul>
     * <li>Form::TARGET_SELF</li>
     * <li>Form::TARGET_BLANK</li>
     * <li>Form::TARGET_PARENT</li>
     * <li>Form::TARGET_TOP</li>
     * </ul>
     * 
     * @param string $target
     * 
     * @return Form
     */
    public function setTarget(string $target): Form {
        $this->target = $target;
        return $this;
    }

    /**
     * This method sets the 'method' form attribute: specifies the HTTP method used when submitting the form (default: Form::METHOD_GET). 
     * Possible values:
     * <ul>
     * <li>Form::METHOD_GET</li>
     * <li>Form::METHOD_POST</li>
     * </ul>
     * 
     * @param string $method
     * 
     * @return Form
     */
    public function setMethod(string $method): Form {
        $this->method = $method;
        return $this;
    }

    /**
     * This method sets the 'accept-charset' form attribute: specifies the charset used in the submitted form.
     * 
     * @param string $acceptCharse
     * 
     * @return Form
     */
    public function setAcceptCharset(string $acceptCharse): Form {
        $this->acceptCharse = $acceptCharse;
        return $this;
    }

    /**
     * This method sets the 'enctype' form attribute: the encoding of the submitted data (default: is url-encoded).
     *  
     * @param string $enctype
     * 
     * @return Form
     */
    public function setEnctype(string $enctype): Form {
        $this->enctype = $enctype;
        return $this;
    }

    /**
     * This method sets the 'novalidate' form attribute: specifies if the browser should not validate the form.
     * 
     * @param bool $novalidate
     * 
     * @return Form
     */
    public function setNovalidate(bool $novalidate): Form {
        $this->novalidate = $novalidate;
        return $this;
    }

    /**
     * This method returns the 'id' attribute of the form.
     * 
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * This method returns the 'Unique Identifier' of the form.
     * 
     * @return string
     */
    public function getUId(): string {
        return $this->uId;
    }

    /**
     * This method returns the 'target' form attribute.
     * 
     * @return string
     */
    public function getTarget(): string {
        return $this->target;
    }

    /**
     * This method returns the 'method' form attribute.
     * 
     * @return string
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * This method returns the 'accept-charset' form attribute.
     * 
     * @return string
     */
    public function getAcceptCharset(): string {
        return $this->acceptCharset;
    }

    /**
     * This method returns the 'enctype' form attribute.
     * 
     * @return string
     */
    public function getEnctype(): string {
        return $this->enctype;
    }

    /**
     * This method returns the 'novalidate' form attribute.
     * 
     * @return bool
     */
    public function getNovalidate(): bool {
        return $this->novalidate;
    }

    /**
     * This method returns the 'name' form attribute.
     * 
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * This method returns the 'action' form attribute.
     * 
     * @return string
     */
    public function getAction(): string {
        return $this->action;
    }

    /**
     * This method sets the 'onselect' event.
     * 
     * @param string $onselect
     * 
     * @return Form
     */
    public function setOnselect(string $onselect): Form {
        $this->onselect = $onselect;
        return $this;
    }

    /**
     * This method returns the 'onselect' event current value.
     * 
     * @return string
     */
    public function getOnselect(): string {
        return $this->onselect;
    }

    /**
     * This method sets the 'onsearch' event.
     * 
     * @param string $onsearch
     * 
     * @return Form
     */
    public function setOnsearch(string $onsearch): Form {
        $this->onsearch = $onsearch;
        return $this;
    }

    /**
     * This method returns the 'onsearch' event current value.
     * 
     * @return string
     */
    public function getOnsearch(): string {
        return $this->onsearch;
    }

    /**
     * This method sets the 'onreset' event.
     * 
     * @param string $onreset
     * 
     * @return Form
     */
    public function setOnreset(string $onreset): Form {
        $this->onreset = $onreset;
        return $this;
    }

    /**
     * This method returns the 'onreset' event current value.
     * 
     * @return string
     */
    public function getOnreset(): string {
        return $this->onreset;
    }

    /**
     * This method sets the 'oninvalid' event.
     * 
     * @param string $oninvalid
     * 
     * @return Form
     */
    public function setOninvalid(string $oninvalid): Form {
        $this->oninvalid = $oninvalid;
        return $this;
    }

    /**
     * This method returns the 'oninvalid' event current value.
     * 
     * @return string
     */
    public function getOninvalid(): string {
        return $this->oninvalid;
    }

    /**
     * This method sets the 'oninput' event.
     * 
     * @param string $oninput
     * 
     * @return Form
     */
    public function setOninput(string $oninput): Form {
        $this->oninput = $oninput;
        return $this;
    }

    /**
     * This method returns the 'oninput' event current value.
     * 
     * @return string
     */
    public function getOninput(): string {
        return $this->oninput;
    }

    /**
     * This method sets the 'onfocus' event.
     * 
     * @param string $onfocus
     * 
     * @return Form
     */
    public function setOnfocus(string $onfocus): Form {
        $this->onfocus = $onfocus;
        return $this;
    }

    /**
     * This method returns the 'onfocus' event current value.
     * 
     * @return string
     */
    public function getOnfocus(): string {
        return $this->onfocus;
    }

    /**
     * This method sets the 'oncontextmenu' event.
     * 
     * @param string $oncontextmenu
     * 
     * @return Form
     */
    public function setOncontextmenu(string $oncontextmenu): Form {
        $this->oncontextmenu = $oncontextmenu;
        return $this;
    }

    /**
     * This method returns the 'oncontextmenu' event current value.
     * 
     * @return string
     */
    public function getOncontextmenu(): string {
        return $this->oncontextmenu;
    }

    /**
     * This method sets the 'onchange' event.
     * 
     * @param string $onchange
     * 
     * @return Form
     */
    public function setOnchange(string $onchange): Form {
        $this->onchange = $onchange;
        return $this;
    }

    /**
     * This method returns the 'onchange' event current value.
     * 
     * @return string
     */
    public function getOnchange(): string {
        return $this->onchange;
    }

    /**
     * This method sets the 'onblur' event.
     * 
     * @param string $onblur
     * 
     * @return Form
     */
    public function setOnblur(string $onblur): Form {
        $this->onblur = $onblur;
        return $this;
    }

    /**
     * This method returns the 'onblur' event current value.
     * 
     * @return string
     */
    public function getOnblur(): string {
        return $this->onblur;
    }

    /**
     * This method sets the 'onsubmit' event.
     * 
     * @param string $onsubmit
     * 
     * @return Form
     */
    public function setOnsubmit(string $onsubmit): Form {
        $this->onsubmit = $onsubmit;
        return $this;
    }

    /**
     * This method returns the 'onsubmit' event current value.
     * 
     * @return string
     */
    public function getOnsubmit(): string {
        return $this->onsubmit;
    }
}
