<?php

namespace FWK\Core\Form\Elements\AttributeTraits;

/**
 * This is the event attributes trait.
 * This trait has been created to share common code between form elements classes,
 * in this case the declaration of the event attributes and its set/get methods.
 *
 * @see AttributesEventsTraits::getOnkeydown()
 * @see AttributesEventsTraits::getOnkeypress()
 * @see AttributesEventsTraits::getOnkeyup()
 * @see AttributesEventsTraits::getOnclick()
 * @see AttributesEventsTraits::getOndblclick()
 * @see AttributesEventsTraits::getOnmousedown()
 * @see AttributesEventsTraits::getOnmousemove()
 * @see AttributesEventsTraits::getOnmouseout()
 * @see AttributesEventsTraits::getOnmouseover()
 * @see AttributesEventsTraits::getOnmouseup()
 * @see AttributesEventsTraits::getOnmousewheel()
 * @see AttributesEventsTraits::getOnwheel()
 * @see AttributesEventsTraits::getOndrag()
 * @see AttributesEventsTraits::getOndragend()
 * @see AttributesEventsTraits::getOndragenter()
 * @see AttributesEventsTraits::getOndragleave()
 * @see AttributesEventsTraits::getOndragover()
 * @see AttributesEventsTraits::getOndragstart()
 * @see AttributesEventsTraits::getOndrop()
 * @see AttributesEventsTraits::getOnscroll()
 * @see AttributesEventsTraits::getOncopy()
 * @see AttributesEventsTraits::getOncut()
 * @see AttributesEventsTraits::getOnpaste()
 * @see AttributesEventsTraits::setOnkeydown()
 * @see AttributesEventsTraits::setOnkeypress()
 * @see AttributesEventsTraits::setOnkeyup()
 * @see AttributesEventsTraits::setOnclick()
 * @see AttributesEventsTraits::setOndblclick()
 * @see AttributesEventsTraits::setOnmousedown()
 * @see AttributesEventsTraits::setOnmousemove()
 * @see AttributesEventsTraits::setOnmouseout()
 * @see AttributesEventsTraits::setOnmouseover()
 * @see AttributesEventsTraits::setOnmouseup()
 * @see AttributesEventsTraits::setOnmousewheel()
 * @see AttributesEventsTraits::setOnwheel()
 * @see AttributesEventsTraits::setOndrag()
 * @see AttributesEventsTraits::setOndragend()
 * @see AttributesEventsTraits::setOndragenter()
 * @see AttributesEventsTraits::setOndragleave()
 * @see AttributesEventsTraits::setOndragover()
 * @see AttributesEventsTraits::setOndragstart()
 * @see AttributesEventsTraits::setOndrop()
 * @see AttributesEventsTraits::setOnscroll()
 * @see AttributesEventsTraits::setOncopy()
 * @see AttributesEventsTraits::setOncut()
 * @see AttributesEventsTraits::setOnpaste()
 * 
 * @package FWK\Core\Form\Elements\AttributeTraits
 */
trait AttributesEventsTraits {

    // Keyboard Events
    protected string $onkeydown = '';

    protected string $onkeypress = '';

    protected string $onkeyup = '';

    // Mouse Events
    protected string $onclick = '';

    protected string $ondblclick = '';

    protected string $onmousedown = '';

    protected string $onmousemove = '';

    protected string $onmouseout = '';

    protected string $onmouseover = '';

    protected string $onmouseup = '';

    protected string $onmousewheel = '';

    protected string $onwheel = '';

    // Drag Events
    protected string $ondrag = '';

    protected string $ondragend = '';

    protected string $ondragenter = '';

    protected string $ondragleave = '';

    protected string $ondragover = '';

    protected string $ondragstart = '';

    protected string $ondrop = '';

    protected string $onscroll = '';

    // Clipboard Events
    protected string $oncopy = '';

    protected string $oncut = '';

    protected string $onpaste = '';

    /**
     * This method returns the current value of the 'onkeydown' event attribute.
     * 
     * @return string
     */
    public function getOnkeydown(): string {
        return $this->onkeydown;
    }

    /**
     * This method returns the current value of the 'onkeypress' event attribute.
     * 
     * @return string
     */
    public function getOnkeypress(): string {
        return $this->onkeypress;
    }

    /**
     * This method returns the current value of the 'onkeyup' event attribute.
     * 
     * @return string
     */
    public function getOnkeyup(): string {
        return $this->onkeyup;
    }

    /**
     * This method returns the current value of the 'onclick' event attribute.
     * 
     * @return string
     */
    public function getOnclick(): string {
        return $this->onclick;
    }

    /**
     * This method returns the current value of the 'ondblclick' event attribute.
     * 
     * @return string
     */
    public function getOndblclick(): string {
        return $this->ondblclick;
    }

    /**
     * This method returns the current value of the 'onmousedown' event attribute.
     * 
     * @return string
     */
    public function getOnmousedown(): string {
        return $this->onmousedown;
    }

    /**
     * This method returns the current value of the 'onmousemove' event attribute.
     * 
     * @return string
     */
    public function getOnmousemove(): string {
        return $this->onmousemove;
    }

    /**
     * This method returns the current value of the 'onmouseout' event attribute.
     * 
     * @return string
     */
    public function getOnmouseout(): string {
        return $this->onmouseout;
    }

    /**
     * This method returns the current value of the 'onmouseover' event attribute.
     * 
     * @return string
     */
    public function getOnmouseover(): string {
        return $this->onmouseover;
    }

    /**
     * This method returns the current value of the 'onmouseup' event attribute.
     * 
     * @return string
     */
    public function getOnmouseup(): string {
        return $this->onmouseup;
    }

    /**
     * This method returns the current value of the 'onmousewheel' event attribute.
     * 
     * @return string
     */
    public function getOnmousewheel(): string {
        return $this->onmousewheel;
    }

    /**
     * This method returns the current value of the 'onwheel' event attribute.
     * 
     * @return string
     */
    public function getOnwheel(): string {
        return $this->onwheel;
    }

    /**
     * This method returns the current value of the 'ondrag' event attribute.
     * 
     * @return string
     */
    public function getOndrag(): string {
        return $this->ondrag;
    }

    /**
     * This method returns the current value of the 'ondragend' event attribute.
     * 
     * @return string
     */
    public function getOndragend(): string {
        return $this->ondragend;
    }

    /**
     * This method returns the current value of the 'ondragenter' event attribute.
     * 
     * @return string
     */
    public function getOndragenter(): string {
        return $this->ondragenter;
    }

    /**
     * This method returns the current value of the 'ondragleave' event attribute.
     * 
     * @return string
     */
    public function getOndragleave(): string {
        return $this->ondragleave;
    }

    /**
     * This method returns the current value of the 'ondragover' event attribute.
     * 
     * @return string
     */
    public function getOndragover(): string {
        return $this->ondragover;
    }

    /**
     * This method returns the current value of the 'ondragstart' event attribute.
     * 
     * @return string
     */
    public function getOndragstart(): string {
        return $this->ondragstart;
    }

    /**
     * This method returns the current value of the 'ondrop' event attribute.
     * 
     * @return string
     */
    public function getOndrop(): string {
        return $this->ondrop;
    }

    /**
     * This method returns the current value of the 'onscroll' event attribute.
     * 
     * @return string
     */
    public function getOnscroll(): string {
        return $this->onscroll;
    }

    /**
     * This method returns the current value of the 'oncopy' event attribute.
     * 
     * @return string
     */
    public function getOncopy(): string {
        return $this->oncopy;
    }

    /**
     * This method returns the current value of the 'oncut' event attribute.
     * 
     * @return string
     */
    public function getOncut(): string {
        return $this->oncut;
    }

    /**
     * This method returns the current value of the 'onpaste' event attribute.
     * 
     * @return string
     */
    public function getOnpaste(): string {
        return $this->onpaste;
    }

    /**
     * This method sets the 'onkeydown' event attribute with the given value and returns self.
     * 
     * @param string $onkeydown
     * 
     * @return self
     */
    public function setOnkeydown(string $onkeydown): self {
        $this->onkeydown = $onkeydown;
    }

    /**
     * This method sets the 'onkeypress' event attribute with the given value and returns self.
     * 
     * @param string $onkeypress
     * 
     * @return self
     */
    public function setOnkeypress(string $onkeypress): self {
        $this->onkeypress = $onkeypress;
    }

    /**
     * This method sets the 'onkeyup' event attribute with the given value and returns self.
     * 
     * @param string $onkeyup
     * 
     * @return self
     */
    public function setOnkeyup(string $onkeyup): self {
        $this->onkeyup = $onkeyup;
    }

    /**
     * This method sets the 'onclick' event attribute with the given value and returns self.
     * 
     * @param string $onclick
     * 
     * @return self
     */
    public function setOnclick(string $onclick): self {
        $this->onclick = $onclick;
    }

    /**
     * This method sets the 'ondblclick' event attribute with the given value and returns self.
     * 
     * @param string $ondblclick
     * 
     * @return self
     */
    public function setOndblclick(string $ondblclick): self {
        $this->ondblclick = $ondblclick;
    }

    /**
     * This method sets the 'onmousedown' event attribute with the given value and returns self.
     * 
     * @param string $onmousedown
     * 
     * @return self
     */
    public function setOnmousedown(string $onmousedown): self {
        $this->onmousedown = $onmousedown;
    }

    /**
     * This method sets the 'onmousemove' event attribute with the given value and returns self.
     * 
     * @param string $onmousemove
     * 
     * @return self
     */
    public function setOnmousemove(string $onmousemove): self {
        $this->onmousemove = $onmousemove;
    }

    /**
     * This method sets the 'onmouseout' event attribute with the given value and returns self.
     * 
     * @param string $onmouseout
     * 
     * @return self
     */
    public function setOnmouseout(string $onmouseout): self {
        $this->onmouseout = $onmouseout;
    }

    /**
     * This method sets the 'onmouseover' event attribute with the given value and returns self.
     * 
     * @param string $onmouseover
     * 
     * @return self
     */
    public function setOnmouseover(string $onmouseover): self {
        $this->onmouseover = $onmouseover;
    }

    /**
     * This method sets the 'onmouseup' event attribute with the given value and returns self.
     * 
     * @param string $onmouseup
     * 
     * @return self
     */
    public function setOnmouseup(string $onmouseup): self {
        $this->onmouseup = $onmouseup;
    }

    /**
     * This method sets the 'onmousewheel' event attribute with the given value and returns self.
     * 
     * @param string $onmousewheel
     * 
     * @return self
     */
    public function setOnmousewheel(string $onmousewheel): self {
        $this->onmousewheel = $onmousewheel;
    }

    /**
     * This method sets the 'onwheel' event attribute with the given value and returns self.
     * 
     * @param string $onwheel
     * 
     * @return self
     */
    public function setOnwheel(string $onwheel): self {
        $this->onwheel = $onwheel;
    }

    /**
     * This method sets the 'ondrag' event attribute with the given value and returns self.
     * 
     * @param string $ondrag
     * 
     * @return self
     */
    public function setOndrag(string $ondrag): self {
        $this->ondrag = $ondrag;
    }

    /**
     * This method sets the 'ondragend' event attribute with the given value and returns self.
     * 
     * @param string $ondragend
     * 
     * @return self
     */
    public function setOndragend(string $ondragend): self {
        $this->ondragend = $ondragend;
    }

    /**
     * This method sets the 'ondragenter' event attribute with the given value and returns self.
     * 
     * @param string $ondragenter
     * 
     * @return self
     */
    public function setOndragenter(string $ondragenter): self {
        $this->ondragenter = $ondragenter;
    }

    /**
     * This method sets the 'ondragleave' event attribute with the given value and returns self.
     * 
     * @param string $ondragleave
     * 
     * @return self
     */
    public function setOndragleave(string $ondragleave): self {
        $this->ondragleave = $ondragleave;
    }

    /**
     * This method sets the 'ondragover' event attribute with the given value and returns self.
     * 
     * @param string $ondragover
     * 
     * @return self
     */
    public function setOndragover(string $ondragover): self {
        $this->ondragover = $ondragover;
    }

    /**
     * This method sets the 'ondragstart' event attribute with the given value and returns self.
     * 
     * @param string $ondragstart
     * 
     * @return self
     */
    public function setOndragstart(string $ondragstart): self {
        $this->ondragstart = $ondragstart;
    }

    /**
     * This method sets the 'ondrop' event attribute with the given value and returns self.
     * 
     * @param string $ondrop
     * 
     * @return self
     */
    public function setOndrop(string $ondrop): self {
        $this->ondrop = $ondrop;
    }

    /**
     * This method sets the 'onscroll' event attribute with the given value and returns self.
     * 
     * @param string $onscroll
     * 
     * @return self
     */
    public function setOnscroll(string $onscroll): self {
        $this->onscroll = $onscroll;
    }

    /**
     * This method sets the 'oncopy' event attribute with the given value and returns self.
     * 
     * @param string $oncopy
     * 
     * @return self
     */
    public function setOncopy(string $oncopy): self {
        $this->oncopy = $oncopy;
    }

    /**
     * This method sets the 'oncut' event attribute with the given value and returns self.
     * 
     * @param string $oncut
     * 
     * @return self
     */
    public function setOncut(string $oncut): self {
        $this->oncut = $oncut;
    }

    /**
     * This method sets the 'onpaste' event attribute with the given value and returns self.
     * 
     * @param string $onpaste
     * 
     * @return self
     */
    public function setOnpaste(string $onpaste): self {
        $this->onpaste = $onpaste;
    }
}

