<?php

namespace FWK\ViewHelpers\Blog;

use FWK\Core\ViewHelpers\ViewHelper;
use FWK\ViewHelpers\Blog\Macro\CommentsForm;
use FWK\ViewHelpers\Blog\Macro\SubscribeForm;
use FWK\ViewHelpers\Blog\Macro\TagsCloud;
use FWK\ViewHelpers\Blog\Macro\Comments;
use FWK\ViewHelpers\Blog\Macro\BloggerInformation;

/**
 * This is the BlogViewHelper class.
 * The purpose of this class is to facilitate to Twig the generation of the blog's view output (providing some useful methods).
 * <br>This class extends ViewHelper, see this class.
 *
 * @see ViewHelper
 * @see UserViewHelper::subscribeMacro()
 *
 * @package FWK\ViewHelpers\Blog
 */
class BlogViewHelper extends ViewHelper {


    /**
     * This method merges the given arguments, calculates and returns the view parameters for the subscribe form.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>showLabel</li>
     * <li>showPlaceholder</li>
     * <li>disableValidationMessages</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function subscribeFormMacro(array $arguments = []): array {
        return (new SubscribeForm($arguments))->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the tags cloud output.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>tags</li>
     * <li>showLabel</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function tagsCloudMacro(array $arguments = []): array {
        return (new TagsCloud($arguments))->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the blogger information output.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>tags</li>
     * <li>showLabel</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *
     * @return array
     */
    public function bloggerInformationMacro(array $arguments = []): array {
        return (new BloggerInformation($arguments))->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the blog post comments form.
     * The array keys of the returned parameters are:
     * <ul>
     * <li>form</li>
     * <li>blogSettings</li>
     * <li>output</li>
     * <li>showTitle</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function commentsFormMacro(array $arguments = []): array {
        $commentsForm = new CommentsForm($arguments, $this->session);
        return $commentsForm->getViewParameters();
    }

    /**
     * This method merges the given arguments, calculates and returns the view parameters for the comments.
     * The array keys of the returned parameters are:
     * <ul>
     *      <li>comments</li>
     * </ul>
     *
     * @param array $arguments
     *            Twig macro arguments
     *            
     * @return array
     */
    public function commentsMacro(array $arguments = []): array {
        $comments = new Comments($arguments);
        return $comments->getViewParameters();
    }
}
