<?php

namespace FWK\Services;

use FWK\Core\Resources\Registries\RegistryService;
use SDK\Core\Dtos\ElementCollection;
use SDK\Core\Resources\BatchRequests;
use SDK\Services\BlogService as BlogServiceSDK;
use SDK\Services\Parameters\Groups\Blog\BlogPostParametersGroup;
use SDK\Dtos\Blog\BlogPost;
use SDK\Dtos\Blog\BlogCategory;
use FWK\Services\Traits\ServiceTrait;
use FWK\Services\Traits\SetRelatedItemsTrait;
use SDK\Dtos\Blog\BlogTag;
use SDK\Services\Parameters\Groups\Blog\BlogTagParametersGroup;

/**
 * This is the BlogService class.
 * Remember that a service is an extension of a SDK model that allows to add additional
 * actions to a model request or create new methods to simplify some common requests.
 * In this case, the BlogService extends the SDK\Services\BlogService.
 *
 * @see BlogService::getBlogPostById()
 * @see BlogService::getBlogPostsByIdList()
 * @see BlogService::getBlogPostByPId()
 * @see BlogService::addGetBlogPostById()
 * @see BlogService::addGetBlogPostsByIdList()
 * @see BlogService::addGetBlogPostByPId()
 *
 * @see BlogService
 * @see ServiceTrait
 * @see SetRelatedItemsTrait
 *
 * @package FWK\Services
 */
class BlogService extends BlogServiceSDK {
    use ServiceTrait, SetRelatedItemsTrait;

    private const REGISTRY_KEY = RegistryService::BLOG_SERVICE;

    private const ADD_FILTER_INTERVAL_PARAMETERS = [];

    private const ADD_FILTER_ID_VALUE_PARAMETERS = [];

    private const RELATED_ITEM_CLASS = [BlogPost::class];

    /**
     * This method returns the Dto of the blog post whose id matches the given one.
     * 
     * @param int $id
     * 
     * @return BlogPost|NULL
     */
    public function getBlogPostById(int $id): ?BlogPost {
        return $this->getBlogPost($id);
    }

    /**
     * This method returns the Dtos of those blog posts whose id matches any of the given list.
     * 
     * @param string $idList
     * 
     * @return ElementCollection|NULL
     */
    public function getBlogPostsByIdList(string $idList): ?ElementCollection {
        return $this->getBlogPosts($this->getParametersByIdList($idList));
    }

    /**
     * This method returns the Dto of the blog post whose pId matches the given one.
     * 
     * @param string $pId
     * 
     * @return BlogPost|NULL
     */
    public function getBlogPostByPId(string $pId): ?BlogPost {
        $result = $this->getBlogPosts($this->getParametersByPId($pId));
        if (!empty($result)) {
            return $result->getItems()[0];
        }
        return new BlogPost();
    }

    /**
     * This method returns the Dto of the blog category whose Id matches the given one.
     *
     * @param int $id
     *
     * @return BlogCategory|NULL
     */
    public function getBlogCategoryById(int $pId): ?BlogCategory {
        return $this->getBlogCategory($this->getParametersByPId($pId));
    }

    /**
     * This method adds the batch request to get the blog post whose id matches the given one.
     *
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $id
     */
    public function addGetBlogCategoryById(BatchRequests $batchRequests, string $batchName, int $id): void {
        $this->addGetBlogCategory($batchRequests, $batchName, $id);
    }

    /**
     * This method adds the batch request to get the blog post whose id matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param int $id
     */
    public function addGetBlogPostById(BatchRequests $batchRequests, string $batchName, int $id): void {
        $this->addGetBlogPost($batchRequests, $batchName, $id);
    }

    /**
     * This method adds the batch request to get those blog spots whose id matches any of the given idList.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $idList
     */
    public function addGetBlogPostsByIdList(BatchRequests $batchRequests, string $batchName, string $idList): void {
        $this->addGetBlogPosts($batchRequests, $batchName, $this->getParametersByIdList($idList));
    }

    /**
     * This method adds the batch request to get the blog post whose pId matches the given one.
     * 
     * @param BatchRequests $batchRequests
     * @param string $batchName
     *          the name that will identify the request on the batch return.
     * @param string $pId
     */
    public function addGetBlogPostByPId(BatchRequests $batchRequests, string $batchName, string $pId): void {
        $this->addGetBlogPosts($batchRequests, $batchName, $this->getParametersByPId($pId));
    }

    private function getParametersByIdList(string $idList): BlogPostParametersGroup {
        $BlogPostParametersGroup = new BlogPostParametersGroup();
        $BlogPostParametersGroup->setIdList($idList);
        return $BlogPostParametersGroup;
    }

    private function getParametersByPId(string $pId): BlogPostParametersGroup {
        $BlogPostParametersGroup = new BlogPostParametersGroup();
        $BlogPostParametersGroup->setPId($pId);
        return $BlogPostParametersGroup;
    }

    /**
     * Returns all blog tags filtered with the given parameters
     *
     * @param BlogTagParametersGroup $params
     *            object with the needed filters to send to the API blogs resource
     *
     * @return ElementCollection|NULL
     */
    public function getAllTags(BlogTagParametersGroup $params = null): ?ElementCollection {
        if (is_null($params)) {
            $params = new BlogTagParametersGroup();
        }
        return $this->getAllElementCollectionItems(BlogTag::class, 'Tags', $params);
    }
}
