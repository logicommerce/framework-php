<?php

namespace FWK\ViewHelpers\Page;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\ViewHelpers\Page\Macro\ContactForm;
use FWK\ViewHelpers\Page\Macro\NewsletterForm;
use FWK\ViewHelpers\Page\Macro\PageContent;
use FWK\ViewHelpers\Page\Macro\SendMailForm;
use FWK\ViewHelpers\Page\Macro\Subpages;

/**
 * This is the PageViewHelper class.
 * The purpose of this class is to facilitate to Twig the generation of the Page's view output (providing some useful methods).
 * <br>This class extends ViewHelper, see this class.
 *
 * @see ViewHelper
 * @see PageViewHelper::subpagesMacro()
 * @see PageViewHelper::contactFormMacro()
 * @see PageViewHelper::newsletterFormMacro()
 * @see PageViewHelper::pageContentMacro()
 * @see PageViewHelper::sendMailFormMacro()
 *
 * @package FWK\ViewHelpers\Page
 */
class PageViewHelper extends ViewHelper{
       
    
    /**
     * This method merges the given arguments, calculates and returns the view parameters for the subpages.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>subpages</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function subpagesMacro(array $arguments = []): array {
        return (new Subpages($arguments))->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the contactForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function contactFormMacro(array $arguments = []): array {
        return (new ContactForm($arguments))->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the newsletterForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function newsletterFormMacro(array $arguments = []): array {
        return (new NewsletterForm($arguments))->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the pageContent.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>page</li>
     * <li>data</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function pageContentMacro(array $arguments = []): array {
        return (new PageContent($arguments))->getViewParameters();
    }
    
    /**
     * This method merges the given arguments, calculates and returns the view parameters for the sendMailForm.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function sendMailFormMacro(array $arguments = []): array {
        return (new SendMailForm($arguments))->getViewParameters();
    }
}