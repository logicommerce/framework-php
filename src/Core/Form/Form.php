<?php

namespace FWK\Core\Form;

use FWK\Core\Form\Elements\Form as FormHead;
use FWK\Core\Form\Elements\Inputs\InputHidden;
use FWK\Core\Resources\Loader;
use FWK\Enums\Parameters;
use FWK\Enums\Services;

/**
 * This is the Form class.
 * This class represents an entire 'form' of the commerce, with its attributes, events, inputs, template,...
 * 
 * @see Form::setTemplate()
 * @see Form::setValidatorPId()
 * @see Form::getArrayInputFilterParameters()
 * @see Form::getInputFilterParameters()
 * @see Form::getArrayInputFilterParametersInOneLevel()
 * @see Form::getInputFilterParametersInOneLevel()
 * @see Form::getArrayOutputElement()
 * @see Form::getOutputElements()
 * @see Form::getInputItems()
 * 
 * @package FWK\Core\Form
 */
class Form {

    public const ELEMENT_FORM_START = 'formStart';

    public const ELEMENT_FORM_END = 'formEnd';

    public const SUBMIT = 'submit';

    /**
     * This is the head of the form.
     * 
     * @see FormHead
     */
    protected FormHead $formHead;

    /**
     * Inputs of the form.
     */
    protected array $inputs = [];

    /**
     * Template of the form.
     */
    protected string $template = '';

    /**
     * ValidatorPid of the form.
     */
    protected string $validatorPid = '';

    private static ?bool $enabledCaptcha = null;

    protected static function isEnabledCaptcha(): bool {
        if (is_null(self::$enabledCaptcha)) {
            self::$enabledCaptcha = !is_null(Loader::service(Services::PLUGIN)->getCaptchaPluginProperties());
        }
        return self::$enabledCaptcha;
    }

    /**
     * Constructor. It creates a Form with the given FormHead and inputs.
     * 
     * @param FormHead $formHead
     * @param array $inputs
     */
    public function __construct(FormHead $formHead, array $inputs) {
        $this->formHead = $formHead;
        $this->inputs = $inputs;
    }

    /**
     * This method sets the template of the Form.
     * 
     * @param string $template
     * 
     * @return Form
     */
    public function setTemplate(string $template): Form {
        $this->template = $template;
        return $this;
    }

    /**
     * This method returns the Form's template.
     * 
     * @return string
     */
    public function getTemplate(): string {
        return $this->template;
    }

    /**
     * This method adds captcha to the inputs array
     * 
     * @return Form
     */
    public function addCaptcha(): Form {
        if (self::isEnabledCaptcha()) {
            $this->inputs[] = new FormItem(Parameters::CAPTCHA_TOKEN, (new InputHidden()));
        }
        return $this;
    }

    /**
     * This method sets the validator PId of the form.
     * 
     * @param string $validatorPId
     * 
     * @return Form
     */
    public function setValidatorPId(string $validatorPId): Form {
        $this->validatorPId = $validatorPId;
        return $this;
    }

    /**
     * This method returns an array with all the inputs of the given inputs array and their filters,
     * where the key of each node is the name of the input and the value of the node is the corresponding FilterInput.
     * 
     * @param array $inputs
     * 
     * @return array
     */
    public function getArrayInputFilterParameters(array $inputs): array {
        $result = [];
        foreach ($inputs as $key => $input) {
            if (is_array($input)) {
                $result[$key] = $this->getArrayInputFilterParameters($input);
            } else {
                if (!is_null($input->getFilterInput())) {
                    $result[$input->getName()] ??= $input->getFilterInput();
                }
            }
        }
        return $result;
    }

    /**
     * This method returns an array with all the inputs of the form and their filters,
     * where the key of each node is the name of the input and the value of the node is the corresponding FilterInput.
     * 
     * @return array
     */
    public function getInputFilterParameters(): array {
        return $this->getArrayInputFilterParameters($this->inputs);
    }

    /**
     * This method returns an array with all the inputs items,
     *
     * @return array
     */
    public function getInputItems(): array {
        return $this->inputs;
    }

    /**
     * This method returns an array with all the inputs of the given inputs array and their filters,
     * where the key of each node is the parentKey concatenated with the name of the input, and the value of the node is the corresponding FilterInput.
     * Only one level array is returned, so there is no nodes containing an array as a value.
     *
     * @param string $parentKey
     * @param array $inputs
     *
     * @return array
     */
    public function getArrayInputFilterParametersInOneLevel(string $parentKey, array $inputs): array {
        $result = [];
        foreach ($inputs as $key => $input) {
            if (is_array($input)) {
                $result = array_merge($result, $this->getArrayInputFilterParametersInOneLevel($key, $input));
            } else {
                if (!is_null($input->getFilterInput())) {
                    $result[$parentKey . (strlen($parentKey) ? '_' : '') . $input->getName()] ??= $input->getFilterInput();
                }
            }
        }
        return $result;
    }

    /**
     * This method returns an array with all the inputs of the form and their filters,
     * where the key of each node is the parentKey concatenated with the name of the input, and the value of the node is the corresponding FilterInput.
     * Only one level array is returned, so there is no nodes containing an array as a value.
     * 
     * @return array
     */
    public function getInputFilterParametersInOneLevel(): array {
        return $this->getArrayInputFilterParametersInOneLevel('', $this->inputs);
    }

    /**
     * This method returns an array with all the outputs of the form inputs,
     * where the key of each node is the name of the input and the value of the node is the corresponding output of the input.
     * 
     * @param array $inputs
     * @param array $richFormList. Only recognizes RichFormItems enum elements
     * 
     * @return array
     */
    public function getArrayOutputElement(array $inputs, array $richFormList = []): array {
        $result = [];
        foreach ($inputs as $key => $input) {
            if (is_array($input)) {
                $result[$key] = $this->getArrayOutputElement($input, $richFormList);
            } else {
                $result[$input->getName()] = $input->getElement()->outputElement($input->getName(), $richFormList);
            }
        }
        return $result;
    }

    public function getArrayDefinitionElement(array $inputs): array {
        $result = [];
        foreach ($inputs as $key => $input) {
            if (is_array($input)) {
                $result[$key] = $this->getArrayDefinitionElement($input);
            } else {
                $result[$input->getName()] = $input->getElement();
            }
        }
        return $result;
    }

    /**
     * This method returns an array with the output of all the elements of the form:
     * <ul>
     * <li>self::ELEMENT_FORM_START -> This key position contains an array with the form header outputs (attributes and events of the form).</li>
     * <li>self::ELEMENT_FORM_END -> This key position contains the output of the end of the form ('</form>').</li>
     * <li>The rest of positions are the outputs of each of the form inputs.</li>
     * </ul>
     * 
     * @param array $richFormList. Only recognizes RichFormItems enum elements
     * 
     * @return array
     */
    public function getOutputElements(array $richFormList = []): array {
        $outputElements = [];
        $outputElements[self::ELEMENT_FORM_START] = $this->formHead->outputElement('', $richFormList);
        $outputElements += $this->getArrayOutputElement($this->inputs, $richFormList);
        $outputElements[self::ELEMENT_FORM_END] = '</form>';
        return $outputElements;
    }

    /**
     * This method returns an array with the definition of all the elements of the form:
     * <ul>
     * <li>self::ELEMENT_FORM_START -> This key position contains an array with the form header outputs (attributes and events of the form).</li>
     * <li>self::ELEMENT_FORM_END -> This key position contains the output of the end of the form (null).</li>
     * <li>The rest of positions are the outputs of each of the form inputs.</li>
     * </ul>
     *
     * @return array
     */
    public function getDefinitionElements(): array {
        $outputElements = [];
        $outputElements[self::ELEMENT_FORM_START] = $this->formHead;
        $outputElements += $this->getArrayDefinitionElement($this->inputs);
        $outputElements[self::ELEMENT_FORM_END] = null;
        return $outputElements;
    }

    /**
     * This method remove the element by the given name
     * @param string $name
     * @return void
     */
    public function removeInputElement(string $name): void {
        $this->removeElement($name, $this->inputs);
    }

    private function removeElement(string $name, array &$inputs): void {
        foreach ($inputs as $key => &$input) {
            if (is_array($input)) {
                $this->removeElement($name, $input);
            } else {
                if ($input->getName() === $name) {
                    unset($inputs[$key]);
                }
            }
        }
    }
}
