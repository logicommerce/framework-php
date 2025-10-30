<?php

namespace FWK\Controllers\Util\Internal;

use FWK\Core\Controllers\BaseHtmlController;
use FWK\Enums\Services;
use FWK\Core\Resources\Loader;
use FWK\Twig\TwigLoader;
use SDK\Core\Resources\BatchRequests;
use SDK\Core\Resources\Date;
use SDK\Enums\PageType;
use SDK\Enums\RelatedItemsType;
use SDK\Services\Parameters\Groups\PageParametersGroup;
use SDK\Services\Parameters\Groups\RelatedItemsParametersGroup;
use FWK\Enums\TwigAutoescape;
use FWK\Core\Resources\Router;
use FWK\Core\Form\FormFactory;
use FWK\Core\Theme\Theme;
use FWK\Core\Theme\Dtos\FormSetUser;
use SDK\Core\Resources\Environment;

/**
 * This is the DemoController class.
 * This class only is used in DEVEL mode and if the route is "demo", defined in Router constructor.<br>
 * This class extends BaseHtmlController (FWK\Core\Controllers\BaseHtmlController), see this class.
 *
 * @see BaseHtmlController
 *
 * @package FWK\Controllers\Util\Internal
 */
class DemoController extends BaseHtmlController {

    private string $content = 'default';

    /**
     * Constructor.
     */
    public function __construct($route) {
        if (!Environment::get('DEVEL')) {
            (new Router())->notFound(405);
        }
        parent::__construct($route);
        if (isset($_GET['content'])) {
            $this->content = $_GET['content'];
        }
    }

    /**
     * This method is the one in charge of defining all the data batch requests that
     * are needed for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     * @return void
     */
    protected function setBatchData(BatchRequests $requests): void {
        if ($this->content === 'default') {
            // Areas
            $areaService = Loader::service(Services::AREA);
            $areaService->addGetAreaById($requests, 'areaById', 1);
            $areaService->addGetAreas($requests, 'areas');
            $areaService->addGetCategoriesArea($requests, 'categoriesArea');
            $areaService->addGetCategoriesAreaCategories($requests, 'categoriesAreaCategories');

            // Banners
            $bannerService = Loader::service(Services::BANNER);
            $bannerService->addGetBannerById($requests, 'bannerById', 1);
            $bannerService->addGetBanners($requests, 'banners');

            // Categories
            $categoryService = Loader::service(Services::CATEGORY);
            $categoryService->addGetCategoryById($requests, 'categoryById', 1);
            $categoryService->addGetCategories($requests, 'categories');
            $relatedItemsParametersGroup = new RelatedItemsParametersGroup();
            $relatedItemsParametersGroup->setLimit(5);
            $categoryService->addGetRelatedItems($requests, 'categoryRelatedBanners', 1, RelatedItemsType::BANNERS, $relatedItemsParametersGroup);

            // News
            $newsService = Loader::service(Services::NEWS);
            $newsService->addGetNewsById($requests, 'pieceOfNewsById', 1);
            $newsService->addGetNews($requests, 'news');

            // Pages
            $pageService = Loader::service(Services::PAGE);
            $pageService->addGetPageById($requests, 'pageById', 1);
            $pageService->addGetPageById($requests, 'pages', 2);
            // Contact type page
            $pageParametersGroup = new PageParametersGroup();
            $pageParametersGroup->setPageType(PageType::CONTACT);
            $pageService->addGetPages($requests, 'contactPages', $pageParametersGroup);
            // Sitemap type page
            $pageParametersGroup = new PageParametersGroup();
            $pageParametersGroup->setPageType(PageType::SITEMAP);
            $pageService->addGetPages($requests, 'sitemapPages', $pageParametersGroup);
            // Subpages type page
            $pageParametersGroup = new PageParametersGroup();
            $pageParametersGroup->setPageType(PageType::SUBPAGES);
            $pageService->addGetPages($requests, 'subpagesPages', $pageParametersGroup);

            // Products
            $productService = Loader::service(Services::PRODUCT);
            $productService->addGetProductById($requests, 'productById', 1182);

            /*
             * $this->setDataValue('products', $productService->getProducts());
             * $this->setDataValue('productRelateds', $productService->getRelatedItems(1));
             * $bannerService->addGetBannersByPosition($requests,'categoryColBanners',6);
             * $bannerService->addGetBannersByPosition($requests,'categoryColBanners',6);
             *
             * var_dump($productService->getProduct(15));
             * $this->setDataValue('pages', $pageService->getNews());
             * $bannerService->addGetBannersByPosition($requests,'categoryColBanners',6);
             *
             * // Related Items
             * // News
             * // Products
             * // Pages
             *
             * // User
             *
             * var_dump($categoryService->getCategoryById(12));exit();
             *
             * echo '<pre>';
             * var_export($categoryService->getCategoryById(12));exit();
             * echo '</pre>';
             */
        } elseif ($this->content === 'forms') {
            /*
             * $areaService = Loader::service(Services::FORM);
             * $areaService->addGetForms($requests, 'endOrder', FormType::END_ORDER);
             * $areaService->addGetForms($requests, 'address', FormType::ADDRESS);
             * $areaService->addGetForms($requests, 'contact', FormType::CONTACT);
             * $areaService->addGetForms($requests, 'setUser', FormType::SET_USER);
             * $areaService->addGetForms($requests, 'updateUser', FormType::UPDATE_USER);
             */
        } elseif ($this->content === 'configuration') {
            $test = self::getTheme()->getConfiguration();
            var_dump($test);
        }
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
        if ($this->content === 'default') {
            // $this->checkCriticalServiceLoaded(null);

            $this->setDataValue('valueForlocalizedCurrency1', 0);
            $this->setDataValue('valueForlocalizedCurrency2', 156.356);
            $this->setDataValue('valueForlocalizedCurrency3', -156.122356);
            $this->setDataValue('valueForlocalizedCurrency4', 123156.356);

            $this->setDataValue('bannerDoneClick', Loader::service(Services::BANNER)->doneClick(1));

            // var_dump($this->getControllerData(self::CONTROLLER_ITEM));exit();
            $this->setDataValue('bannersSite', Loader::service(Services::BANNER)->getBannerSite());

            // WorkWithDate
            $this->setDataValue('workWithDate', Date::create((new \DateTime())->format(\DateTime::ATOM))); // Date::create('2018-05-17T12:03:01.012345Z')

            // Set a variable with value foo
            $this->setDataValue('variable', 'foo');

            // AUTOESCAPE_HTML
            $this->setDataValue('testAutoescape', '<div><p>testAutoescape</p></div>');

            // var_dump($this->getControllerData('productById'));

            // TEST PRODUCT 1 -> id: 3978; Pid: PID_3978

            echo '<br>productRelatedItem<br>';
            $relateditemRelated = new RelatedItemsParametersGroup();
            $relateditemRelated->setPId(1182);
            $relateditemRelated->setPosition(12);
            $this->setDataValue('productRelatedItem', Loader::service(Services::BASKET)->getRelatedItems(RelatedItemsType::PRODUCTS, $relateditemRelated));
            var_dump($this->getControllerData('productRelatedItem'));

            echo '<br>setCurrencty<br>';
            $this->setDataValue('setCurrencty', Loader::service(Services::USER)->setCurrency('EUR'));
            var_dump($this->getControllerData('setCurrencty'));

            echo '<br>products discounts<br>';
            $this->setDataValue('productsDiscounts', Loader::service(Services::PRODUCT)->getProductDiscounts(3978));
            var_dump($this->getControllerData('productsDiscounts'));
        } elseif ($this->content === 'forms') {
            var_dump(FormFactory::setUser()->getOutputElements()[FormSetUser::ADDRESSBOOK_FIELDS]);
            FormFactory::setUser()->getInputFilterParametersInOneLevel();

            exit();
            var_dump(FormFactory::setUser()->getOutputElements());
            var_dump(FormFactory::setUser()->getInputFilterParameters());

            var_dump(FormFactory::getComment(125)->getOutputElements());
            var_dump(FormFactory::getDeleteAccount()->getOutputElements());
            var_dump(FormFactory::getLogin()->getOutputElements());
            var_dump(FormFactory::getLostPassword()->getOutputElements());
            // var_dump(FormFactory::getNewsletter()->getOutputElements());
            var_dump(FormFactory::getProductContact(1234, 'email@email.com', '123456789')->getOutputElements());
            var_dump(FormFactory::getStockAlert(1222)->getOutputElements());
            var_dump(FormFactory::getContact()->getOutputElements());
            var_dump(FormFactory::getContact()->getInputFilterParameters());
        }
    }

    /**
     *
     * @see \FWK\Core\Controllers\Controller::setTwig()
     */
    protected function setTwig(array $data = [], bool $loadCore = true, int $autoescape = 0): TwigLoader {
        $twig = parent::setTwig([], true, TwigAutoescape::AUTOESCAPE_HTML, INTERNAL_THEME);
        $this->addFunctions($twig);
        return $twig;
    }

    /**
     *
     * @see \FWK\Core\Controllers\Controller::render()
     */
    protected function render(String $content = null, String $layout = null, String $format = 'html'): string {
        $layout = 'layouts/demo.html.twig';
        $content = 'Content/Demo/' . $this->content . '.html.twig';
        return parent::render($content, $layout, '');
    }

    /**
     * This method is used to add functions to twig.
     *
     * @param \FWK\Twig\TwigLoader $twig
     *
     * @return void
     */
    protected function addFunctions(\FWK\Twig\TwigLoader $twig): void {
        $twig->addFunction($this->getFunctionDemo());
    }

    private function getFunctionDemo(): \Twig_Function {
        return new \Twig\TwigFunction('function_demo', function (int $param = 1): \Twig\Markup {
            if ($param == 1) {
                return new \Twig\Markup('<p class="text-success">Text ONE from FWK</p>', CHARSET);
            } else {
                return new \Twig\Markup('<p class="text-danger">This is text number from FWK:  ' . $param . ' </p>', CHARSET);
            }
        });
    }

    /**
     * This method is the one in charge of defining all the data batch requests that are
     * basic for the controller and adding them to the BatchRequests given by parameter.
     *
     * @param BatchRequests $request
     *            where the method will add the batch requests.
     */
    final protected function setControllerBaseBatchData(BatchRequests $requests): void {
    }
}
