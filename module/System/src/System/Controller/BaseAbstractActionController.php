<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/1/12
 * Time: 11:51 AM
 */
namespace System\Controller;

use Application\API\Breadcrumb;
use Menu\Navigation\Service\DynamicNavigationFactory;
use System\Form\Buttons;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Navigation\Page\Mvc;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\MvcEvent;

class BaseAbstractActionController
    extends AbstractActionController
    implements ServiceLocatorAwareInterface
{

    /**
     * @var \Zend\Http\Request
     */
    protected $request;
    /**
     * @var \Zend\View\Model\ViewModel
     */
    public $viewModel;

    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * @var Mvc
     */
    protected $lastBreadcrumbPage = null;

    //region Public Methods
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->services;
    }

    private function _setViewModel()
    {
        $this->viewModel = new ViewModel();
        $this->viewModel->setTemplate('empty');
//        $this->viewModel->setTerminal($this->params()->fromPost('terminal'));
    }

    public function onDispatch(MvcEvent $e)
    {
        $this->_setViewModel();
        /* @var $request \Zend\Http\Request */
        $request = $e->getRequest();
        if (strpos($request->getUri(), 'admin') > -1)
            $this->setAdminBreadcrumb();
        else {
            Breadcrumb::Init();
        }

//        $pageDataType = $this->params()->fromQuery('page-data-type', 'html');
//        if ($pageDataType == 'json')
//            $this->layout('layout/json');

        $layout = $this->params()->fromPost('systemLayout', false);
        if ($layout)
            $this->layout('layout/' . $layout);

        return parent::onDispatch($e);
    }
    //endregion

    //region Protected Methods
    /**
     * @return \Application\Model\ConfigTable
     */
    protected function getConfigTable()
    {
        return getSM()->get('config_table');
    }

    /**
     * @return \Category\Model\CategoryItemTable
     */
    protected function getCategoryItemTable()
    {
        return getSM()->get('category_item_table');
    }

    /**
     * @return \GeographicalAreas\Model\StateTable
     */
    protected function getStateTable()
    {
        return getSM()->get('state_table');
    }

    /**
     * @return \GeographicalAreas\Model\CityTable
     */
    protected function getCityTable()
    {
        return getSM()->get('city_table');
    }

    /**
     * @return \GeographicalAreas\Model\CountryTable
     */
    protected function getCountryTable()
    {
        return getSM()->get('country_table');
    }

    /**
     * @return \Fields\API\Fields
     */
    protected function getFieldsApi()
    {
        if (getSM()->has('fields_api'))
            return getSM()->get('fields_api');
        throw new \Exception('This system requires Fields module to use this functionality.');
    }

    protected function hasFieldsApi()
    {
        return getSM()->has('fields_api');
    }

    /**
     * @return \Page\API\Page
     */
    protected function getPage()
    {
        return getSM()->get('page_api');
    }

    /**
     * @return \Fields\Model\FieldTable
     */
    protected function getFieldsTable()
    {
        return getSM()->get('fields_table');
    }

    /**
     * @return \File\Model\FileTable
     */
    protected function getFileTable()
    {
        return getSM()->get('file_table');
    }

    /**
     * @return \File\API\File
     */
    protected function getFileApi()
    {
        return getSM()->get('file_api');
    }

    protected function accessDenied()
    {
        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $path = strpos($route, 'admin') > -1 ? 'admin/users' : 'app/user';
        return $this->redirect()->toRoute($path . '/auth-access-denied');
    }

    protected function formHasErrors()
    {
        $this->flashMessenger()->addErrorMessage('There are some errors in your submitted form data.');
    }

    protected function invalidRequest($redirect = false, $routeParams = array(), $routeOptions = array())
    {
        $this->flashMessenger()->addErrorMessage('Your request is NOT valid and cannot be processed.');
        if ($redirect)
            return $this->redirect()->toRoute($redirect, $routeParams, $routeOptions);
    }

    protected function unknownAjaxError()
    {
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function render($view)
    {
        return $this->getServiceLocator()
            ->get('viewrenderer')
            ->render($view);
    }

    /**
     * returns View Helper Manager
     * @return \Zend\View\HelperPluginManager
     */
    protected function vhm()
    {
        return $this->getServiceLocator()->get('viewhelpermanager');
    }

    /**
     * @param Mvc $parent
     * @param $label
     * @param array $params
     * @param null $route
     * @return Mvc
     */
    protected function addBreadcrumb(Mvc $parent, $label, $params = array(), $route = null)
    {
        $page = clone $parent;
        $page->setLabel($label);
        if ($params)
            $page->setParams($params);
        if ($route)
            $page->setRoute($route);
        $parent->addPage($page);
        return $page;
    }

    /**
     * @param null $nav
     * @param null $route
     * @param array $params
     * @return null|Mvc
     */
    protected function setAdminBreadcrumb($nav = null, $route = null, $params = array())
    {
        if (!$route && !$params) {
            /* @var $mvcEvent \Zend\Mvc\MvcEvent */
            $mvcEvent = getSM()->get('Application')
                ->getMvcEvent();

            $routeMatch = $mvcEvent->getRouteMatch();

            if (!$route)
                $route = $routeMatch->getMatchedRouteName();
            if (!$params)
                $params = $routeMatch->getParams();
        }
        if (strpos($route, 'admin') > -1) {
            if (!$nav)
                $nav = 'admin_menu';

            if (!($nav instanceof \Zend\Navigation\Navigation))
                /* @var $nav \Zend\Navigation\Navigation */
                $nav = getSM($nav);

            /* @var $page \Zend\Navigation\Page\Mvc */
            $page = $nav->findBy('route', $route);

            if ($page) {
                $page->setParams($params);
                $page->setVisible(true);
                if (!$page->getLabel()) {
                    $route = explode('/', $route);
                    $page->setLabel(array_pop($route));
                }

                /* @var $parent \Zend\Navigation\Page\Mvc */
                $parent = $page->getParent();
                if ($parent && $parent instanceof \Zend\Navigation\Page\Mvc && !$parent->getVisible())
                    $this->setAdminBreadcrumb($nav, $parent->getRoute(), $params);
                return $page;
            }
        }
        return null;
    }

    protected function adminMenuPage()
    {
        $this->viewModel->setTemplate('application/admin/menu-page');
        return $this->viewModel;
    }

    /**
     * Is this action a from submit?
     *
     * dose post data contain the name of the submit,submit-new or submit-close button
     *
     * @param string $buttons Buttons fieldset , if there is none set to Null
     * @param array $post
     * @return bool
     */
    protected function isSubmit($buttons = 'buttons', array $post = null)
    {
        return (self::_formStatus(Buttons::SAVE, $buttons, $post) ||
            self::_formStatus(Buttons::SAVE_NEW, $buttons, $post) ||
            self::_formStatus(Buttons::SAVE_CLOSE, $buttons, $post) ||
            self::_formStatus(Buttons::SAVE_AS_COPY, $buttons, $post)
        );
    }

    /**
     * Is this action a from submit and new?
     *
     * dose post data contain the name of the submit-new button
     *
     * @param string $buttons Buttons fieldset , if there is none set to Null
     * @param array $post
     * @return bool
     */
    protected function isSubmitAndNew($buttons = 'buttons', array $post = null)
    {
        return self::_formStatus(Buttons::SAVE_NEW, $buttons, $post);
    }

    /**
     * Is this action a from submit and close?
     *
     * dose post data contain the name of the submit-close button
     *
     * @param string $buttons Buttons fieldset , if there is none set to Null
     * @param array $post
     * @return bool
     */
    protected function isSubmitAndClose($buttons = 'buttons', array $post = null)
    {
        return self::_formStatus(Buttons::SAVE_CLOSE, $buttons, $post);
    }

    /**
     * Is this action a form submit to save as a copy
     *
     * dose post data contain the name of the submit-copy button
     *
     * @param string $buttons
     * @param array $post
     * @return bool
     */
    protected function isSaveAsCopy($buttons = 'buttons', array $post = null)
    {
        return self::_formStatus(Buttons::SAVE_AS_COPY, $buttons, $post);
    }

    /**
     * Is this action a from cancel?
     *
     * dose post data contain the name of the cancel button
     *
     * @param string $buttons Buttons fieldset , if there is none set to Null
     * @param array $post
     * @return bool
     */
    protected function isCancel($buttons = 'buttons', array $post = null)
    {
        return self::_formStatus(Buttons::CANCEL, $buttons, $post);
    }

    protected function somethingIsNotRight()
    {
        $this->flashMessenger()->addErrorMessage("Something is not right, please try again later");
    }

    protected function somethingWentWrong()
    {
        $this->flashMessenger()->addErrorMessage('Something went wrong while creating your request, please try again later.');
    }
    //endregion

    //region Private Methods
    private function _formStatus($button, $buttons = null, array $post = null)
    {
        $post = $post ? $post : $this->request->getPost()->toArray();
        $data = ($buttons && isset($post[$buttons])) ? $post[$buttons] : $post;
        return isset($data[$button]);
    }
    //endregion
}
