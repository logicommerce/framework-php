<?php

namespace FWK\Controllers\Blog;

use FWK\Core\Controllers\BaseHtmlController;
use SDK\Core\Resources\BatchRequests;
use FWK\Core\Resources\Loader;
use FWK\Enums\Services;
use FWK\Core\Form\FormFactory;
use FWK\Core\Resources\Metatag;
use FWK\Core\Resources\SeoItems;
use FWK\Core\Resources\Utils;

/**
 * This is the blog post controller.
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Blog
 */
class PostController extends BaseHtmlController {

    public const SUBSCRIBE_FORM = 'subscribeForm';

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
        Loader::service(Services::BLOG)->addGetBlogPost($requests, self::CONTROLLER_ITEM, $this->getRoute()->getId());
    }

    /**
     * This method is in charge of defining the basic data necessary for the correct operation of the controller.
     * operation of the controller.
     */
    protected function setControllerBaseData(): void {
        $this->setDataValue(self::SUBSCRIBE_FORM, FormFactory::getBlogSubscribe(FormFactory::BLOG_POST_SUBSCRIBE, $this->getRoute()->getId()));
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $request): void {
    }

    /**
     * This method runs after the batch requests (defined in the setBatchData methods) are resolved,
     * so here you can work with the response of the batch requests and calculate and set more needed data.
     *
     * @param array $additionalData
     *              Set additiona data to the controller data
     * 
     * @return void
     */
    protected function setData(array $additionalData = []): void {
    }


    /**
     *
     * @see \FWK\Core\Controllers\Controller::getSeoItems()
     */
    protected function getSeoItems(): ?SeoItems {
        $post = $this->getControllerData(self::CONTROLLER_ITEM);
        $seoItems = parent::getSeoItems();
        $seoItems->addMetatag((new Metatag())->setName(SeoItems::OG_TITLE)->setProperty(SeoItems::OG_TITLE)->setContent(Utils::cleanHtmlTags($post->getLanguage()->getName())));
        $seoItems->addMetatag((new Metatag())->setName(SeoItems::OG_URL)->setProperty(SeoItems::OG_URL)->setContent($post->getLanguage()->getUrlSeo()));
        if (strlen($post->getLanguage()->getSmallTitleImage())) {
            $seoItems->addMetatag((new Metatag())->setName(SeoItems::OG_IMAGE)->setProperty(SeoItems::OG_IMAGE)->setContent($post->getLanguage()->getSmallTitleImage()));
        }
        $postShortText = Utils::cleanHtmlTags($post->getLanguage()->getShortText());
        if (strlen($postShortText) > 157) {
            $postShortText = substr($postShortText, 0, 157) . '...';
        }
        $seoItems->addMetatag((new Metatag())->setName(SeoItems::OG_DESCRIPTION)->setProperty(SeoItems::OG_DESCRIPTION)->setContent($postShortText));
        return $seoItems;
    }
}
