<?php

namespace FWK\Core\Theme\Dtos;

use SDK\Core\Dtos\Traits\ElementTrait;
use SDK\Core\Dtos\Element;

/**
 * This is the 'FormElementInput' class, a DTO class for the form configuration data.
 * The items stored in this class will remain immutable (only get methods are available).
 * <br>This class extends SDK\Core\Dtos\Element, see this class.
 *
 * @see FormElementInput::getButton()
 * @see FormElementInput::getCheckbox()
 * @see FormElementInput::getColor()
 * @see FormElementInput::getDate()
 * @see FormElementInput::getDatetimeLocal()
 * @see FormElementInput::getEmail()
 * @see FormElementInput::getFile()
 * @see FormElementInput::getHidden()
 * @see FormElementInput::getImage()
 * @see FormElementInput::getMonth()
 * @see FormElementInput::getNumber()
 * @see FormElementInput::getPassword()
 * @see FormElementInput::getRadio()
 * @see FormElementInput::getRange()
 * @see FormElementInput::getReset()
 * @see FormElementInput::getSearch()
 * @see FormElementInput::getSubmit()
 * @see FormElementInput::getTel()
 * @see FormElementInput::getText()
 * @see FormElementInput::getTime()
 * @see FormElementInput::getUrl()
 * @see FormElementInput::getWeek()
 *
 * @see Element
 * 
 * @uses ElementTrait
 *
 * @package FWK\Core\Theme\Dtos
 */
class FormElementInput extends Element {
    use ElementTrait;
        
    public const BUTTON = 'button';

    public const CHECKBOX = 'checkbox';

    public const COLOR = 'color';

    public const DATE = 'date';

    public const DATETIME_LOCAL = 'datetimeLocal';

    public const EMAIL = 'email';

    public const FILE = 'file';

    public const HIDDEN = 'hidden';

    public const IMAGE = 'image';

    public const MONTH = 'month';

    public const NUMBER = 'number';

    public const PASSWORD = 'password';

    public const RADIO = 'radio';

    public const RANGE = 'range';

    public const RESET = 'reset';

    public const SEARCH = 'search';

    public const SUBMIT = 'submit';

    public const TEL = 'tel';

    public const TEXT = 'text';

    public const TIME = 'time';

    public const URL = 'url';

    public const WEEK = 'week';

    private ?FormElement $button = null;

    private ?FormElement $checkbox = null;

    private ?FormElement $color = null;

    private ?FormElement $date = null;

    private ?FormElement $datetimeLocal = null;

    private ?FormElement $email = null;

    private ?FormElement $file = null;

    private ?FormElement $hidden = null;

    private ?FormElement $image = null;

    private ?FormElement $month = null;

    private ?FormElement $number = null;

    private ?FormElement $password = null;

    private ?FormElement $radio = null;

    private ?FormElement $range = null;

    private ?FormElement $reset = null;

    private ?FormElement $search = null;

    private ?FormElement $submit = null;

    private ?FormElement $tel = null;

    private ?FormElement $text = null;

    private ?FormElement $time = null;

    private ?FormElement $url = null;

    private ?FormElement $week = null;

    /**
        * This method returns the button element configuration
        * 
        * @return FormElement|NULL
        */
    public function getButton():?FormElement {
        return $this->button;
    }

    private function setButton(array $button): void {
        $this->button = new FormElement($button);
    }

    /**
        * This method returns the checkbox element configuration
        * 
        * @return FormElement|NULL
        */
    public function getCheckbox(): ?FormElement {
        return $this->checkbox;
    }

    private function setCheckbox(array $checkbox): void {
        $this->checkbox = new FormElement($checkbox);
    }

    /**
        * This method returns the color element configuration
        * 
        * @return FormElement|NULL
        */
    public function getColor():?FormElement {
        return $this->color;
    }

    private function setColor(array $color): void {
        $this->color = new FormElement($color);
    }

    /**
        * This method returns the date element configuration
        * 
        * @return FormElement|NULL
        */
    public function getDate(): ?FormElement {
        return $this->date;
    }

    private function setDate(array $date): void {
        $this->date = new FormElement($date);
    }

    /**
        * This method returns the datetimeLocal element configuration
        * 
        * @return FormElement|NULL
        */
    public function getDatetimeLocal(): ?FormElement {
        return $this->datetimeLocal;
    }

    private function setDatetimeLocal(array $datetimeLocal): void {
        $this->datetimeLocal = new FormElement($datetimeLocal);
    }

    /**
        * This method returns the email element configuration
        * 
        * @return FormElement|NULL
        */
    public function getEmail():?FormElement {
        return $this->email;
    }

    private function setEmail(array $email): void {
        $this->email = new FormElement($email);
    }

    /**
        * This method returns the file element configuration
        * 
        * @return FormElement|NULL
        */
    public function getFile(): ?FormElement {
        return $this->file;
    }

    private function setFile(array $file): void {
        $this->file = new FormElement($file);
    }

    /**
        * This method returns the hidden element configuration
        * 
        * @return FormElement|NULL
        */
    public function getHidden():?FormElement {
        return $this->hidden;
    }

    private function setHidden(array $hidden): void {
        $this->hidden = new FormElement($hidden);
    }

    /**
        * This method returns the image element configuration
        * 
        * @return FormElement|NULL
        */
    public function getImage():?FormElement {
        return $this->image;
    }

    private function setImage(array $image): void {
        $this->image = new FormElement($image);
    }

    /**
        * This method returns the month element configuration
        * 
        * @return FormElement|NULL
        */
    public function getMonth():?FormElement {
        return $this->month;
    }

    private function setMonth(array $month): void {
        $this->month = new FormElement($month);
    }

    /**
        * This method returns the number element configuration
        * 
        * @return FormElement|NULL
        */
    public function getNumber():?FormElement {
        return $this->number;
    }

    private function setNumber(array $number): void {
        $this->number = new FormElement($number);
    }

    /**
        * This method returns the password element configuration
        * 
        * @return FormElement|NULL
        */
    public function getPassword(): ?FormElement {
        return $this->password;
    }

    private function setPassword(array $password): void {
        $this->password = new FormElement($password);
    }

    /**
        * This method returns the radio element configuration
        * 
        * @return FormElement|NULL
        */
    public function getRadio():?FormElement {
        return $this->radio;
    }

    private function setRadio(array $radio): void {
        $this->radio = new FormElement($radio);
    }

    /**
        * This method returns the range element configuration
        * 
        * @return FormElement|NULL
        */
    public function getRange():?FormElement {
        return $this->range;
    }

    private function setRange(array $range): void {
        $this->range = new FormElement($range);
    }

    /**
        * This method returns the reset element configuration
        * 
        * @return FormElement|NULL
        */
    public function getReset():?FormElement {
        return $this->reset;
    }

    private function setReset(array $reset): void {
        $this->reset = new FormElement($reset);
    }

    /**
        * This method returns the search element configuration
        * 
        * @return FormElement|NULL
        */
    public function getSearch():?FormElement {
        return $this->search;
    }

    private function setSearch(array $search): void {
        $this->search = new FormElement($search);
    }

    /**
        * This method returns the submit element configuration
        * 
        * @return FormElement|NULL
        */
    public function getSubmit():?FormElement {
        return $this->submit;
    }

    private function setSubmit(array $submit): void {
        $this->submit = new FormElement($submit);
    }

    /**
        * This method returns the tel element configuration
        * 
        * @return FormElement|NULL
        */
    public function getTel(): ?FormElement {
        return $this->tel;
    }

    private function setTel(array $tel): void {
        $this->tel = new FormElement($tel);
    }

    /**
        * This method returns the text element configuration
        * 
        * @return FormElement|NULL
        */
    public function getText(): ?FormElement {
        return $this->text;
    }

    private function setText(array $text): void {
        $this->text = new FormElement($text);
    }

    /**
        * This method returns the time element configuration
        * 
        * @return FormElement|NULL
        */
    public function getTime(): ?FormElement {
        return $this->time;
    }

    private function setTime(array $time): void {
        $this->time = new FormElement($time);
    }

    /**
        * This method returns the url element configuration
        * 
        * @return FormElement|NULL
        */
    public function getUrl(): ?FormElement {
        return $this->url;
    }

    private function setUrl(array $url): void {
        $this->url = new FormElement($url);
    }

    /**
        * This method returns the week element configuration
        * 
        * @return FormElement|NULL
        */
    public function getWeek(): ?FormElement {
        return $this->week;
    }

    private function setWeek(array $week): void {
        $this->week = new FormElement($week);
    }

}
