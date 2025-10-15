<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace RealEstate\Controller;

use Application\API\App;
use Application\API\Breadcrumb;
use Application\API\Export;
use Application\API\Printer;
use Application\Model\Config;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use Mail\API\Mail;
use RealEstate\View\Helper\Widget;
use System\Controller\BaseAbstractActionController;
use Zend\Db\Sql\Where;
use Zend\Form\Element\Time;
use Zend\Form\Element;
use \Zend\Mvc\Controller\AbstractActionController;
use User\Permissions\Acl\Acl;
use Zend\Paginator\Adapter\Null;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;
use RealEstate\Form;
use RealEstate\Model;
use \Zend\Form\Fieldset;
use Zend\Config\Reader\Xml;


class RealEstateController extends BaseAbstractActionController
{
    private $real_estate_config_advance = null;
    private $real_estate_config = null;
    private $regType;
    private $estateType;


    /**
     * @return \RealEstate\Model\RealEstateTable
     */
    private function getRealEstateTable()
    {
        return getSM()->get('real_estate_table');
    }

    private function getAdvanceConfig()
    {
        if ($this->real_estate_config_advance == null) {
            $this->real_estate_config_advance = getConfig('real_estate_config_advance')->varValue;
        }
        return $this->real_estate_config_advance;
    }

    private function getConfig()
    {
        if ($this->real_estate_config == null) {
            $this->real_estate_config = getConfig('real_estate_config')->varValue;
        }
        return $this->real_estate_config;
    }

    private function filterFields($fields, $regType = null, $estateType = null)
    {
        if ($regType && $estateType) {
            $fieldsFilter = $this->getAdvanceConfig();
            if (isset($fieldsFilter['estateType_fields'])) {
                $fieldsFilter = $fieldsFilter['estateType_fields'];
                if (isset($fieldsFilter[$estateType])) {
                    $fieldsFilter = $fieldsFilter[$estateType];
                    if (isset($fieldsFilter[$regType])) {
                        $fieldsFilter = $fieldsFilter[$regType]; //machineName=>value
                        $allFields = $fields;
                        $fields = array();

                        foreach ($allFields as $row) {
                            if (isset($fieldsFilter[$row['fieldMachineName']]) && $fieldsFilter[$row['fieldMachineName']] == '1')
                                $fields[] = $row;
                        }
                    }
                }
            }
        }
        return $fields;
    }

    private function getFields()
    {
        $fields = $this->getFieldsTable()->getByEntityType('real_estate')->toArray();
        return $fields;
    }

    public function indexAction()
    {
//        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
//        $path = strpos($route, 'admin') > -1 ? 'admin' : 'app';
//        if ($path == 'app')
//            Breadcrumb::AddMvcPage('Real Estate', 'app/real-estate');
//
//        $status = $this->params()->fromRoute('status', false);
//        $keyword = $this->params()->fromPost('q', false);
//
//        $category_table = $this->getCategoryItemTable();
//        $estateType = $category_table->getItemsTreeByMachineName('estate_type');
//        $regType = Model\RealEstateTable::$estateRegType;
//
//        $state_list = $this->getStateTable()->getArray(1);
//        $selected_state = $this->params()->fromQuery('stateId', current(array_keys($state_list)));
//        //var_dump($regType);
//        $city_list = $this->getCityTable()->getArray($selected_state);
//        $prices = Model\RealEstateTable::makePrices();
//
//        $real_estate_M = t('real_estate_M');
//        $to = t('meter_to');
//        $estateAreas = array(
//            '0-50' => t('Under') . " 50 $real_estate_M",
//            '51-75' => "51 $to 75 $real_estate_M",
//            '76-100' => "76 $to 100 $real_estate_M",
//            '101-150' => "101 $to 150 $real_estate_M",
//            '151-200' => "151 $to 200 $real_estate_M",
//            '201-500' => "201 $to 500 $real_estate_M",
//            '501-1000' => "501 $to 1000 $real_estate_M",
//            '1001-' => t('Over') . " 1000 $real_estate_M",
//        );
//
//        $filter_form = new \RealEstate\Form\Filter($estateType, $regType, $state_list, $city_list, $prices, $estateAreas);
//        $filter_form->setAttribute('action', url($path . '/real-estate'));
//
//        $filter_data = $this->params()->fromQuery('filter_data', false);
//        if ($filter_data)
//            $filter_data = json_encode($filter_data);
//
//        $this->viewModel->setVariables(array(
//                'filter_form' => $filter_form,
//                'path' => $path,
//                'hideElement' => 1,
//                'status' => $status,
//                'keyword' => $keyword,
//                'filter_data' => $filter_data
//            )
//        );
        $this->viewModel->setTemplate('real-estate/real-estate/index');
        return $this->viewModel;
    }

//    public function listAction()
//    {
//        $staticUserId = $this->params()->fromPost('userId', false);
//
//        /* @var $config Config */
//        $config = getConfig('real_estate_config');
//        $config = $config->varValue;
//        $homeInfoCost = 0;
//        if (isset($config['homeInfoCost']))
//            $homeInfoCost = $config['homeInfoCost'];
//        $hideElement = 1; // bad az amaliat jostojo elementhaye gheymat ba tavajoh be regtype hide shavand . be soorate pishfarz = sell
//        $route1 = getSM()->get('Request')->getRequestUri();
//        $realEstateTable = $this->getRealEstateTable();
//
//        /* @var $fields_api \Fields\API\Fields */
//        $fields_api = $this->getFieldsApi();
//        $fields_table = $this->getFieldsApi()->init('real_estate');
//
//        $fields = $this->getFields();
//        $last_visit = time();
//        /*$cookie = $this->getRequest()->getCookie();
//        if ($cookie->offsetExists('last_visit')) {
//            $last_visit = $cookie->offsetGet('last_visit');
//        }*/
//
//        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
//        $admin_route = strpos($route, 'admin') > -1;
//
//        $status = $this->params()->fromPost('status', false);
//        $keyword = $this->params()->fromPost('keyword', false);
//        $orWhere = array();
//        $where = array();
//        switch ($status) {
//            case 'approved':
//            case 'not-approved':
//            case 'transferred':
//            case 'canceled':
//            case 'archived':
//                $where[$realEstateTable->getTable() . '.status'] = \RealEstate\Model\RealEstateTable::$RealStatesStatus[$status];
//                $where[$realEstateTable->getTable() . '.expire > ?'] = time();
//                break;
//            case 'expired':
//                $where[$realEstateTable->getTable() . '.expire < ?'] = time();
//                break;
//            case 'deleted' :
//                break;
//            case 'recycle' :
//                $where[$realEstateTable->getTable() . '.status'] = 6;
//                break;
//            case 'keyword' :
//                $where[$realEstateTable->getTable() . '.status'] = 1;
//                $where[$realEstateTable->getTable() . '.expire > ?'] = time();
//                break;
//            case 'requested':
//                $where[$realEstateTable->getTable() . '.status'] = 1;
//                $where[$realEstateTable->getTable() . '.isRequest'] = 1;
//                $where[$realEstateTable->getTable() . '.expire > ?'] = time();
//                break;
//            case 'special' :
//                $where[$realEstateTable->getTable() . '.status'] = 1;
//                $where[$realEstateTable->getTable() . '.expire > ?'] = time();
//                $where[$realEstateTable->getTable() . '.expireSpecial > ?'] = time();
//                $where[$realEstateTable->getTable() . '.isSpecial'] = 1;
//                break;
//            default :
//                if (strpos($route1, 'admin') == false) {
//                    $where[$realEstateTable->getTable() . '.status'] = array(1, 3, 4);
//                    $where[$realEstateTable->getTable() . '.expire > ?'] = time();
//                }
//                $where[$realEstateTable->getTable() . '.status <> ?'] = 6;
//                break;
//
//        }
//
//        /*if (!isAllowed(\RealEstate\Module::ADMIN_VIEW_OTHERS_ESTATES_LIST) && App::isAdminRoute()) {
//            $where['u.id'] = current_user()->id;
//        }*/
//        //show estates belonging to this user only
//        if ($staticUserId)
//            $where['u.id'] = $staticUserId;
//
//        $order = array();
//
//        $search = $this->params()->fromPost('filter_data', false);
//
//        if ($search) {
////            $filter_form->setData($this->params()->fromQuery());
////            if ($filter_form->isValid()) {
//            $filter_data = $search;
////            $hideElement = $filter_data['filter_regType'];
//            if (isset($filter_data['filter_isRequest']) && $filter_data['filter_isRequest'])
//                $where[$realEstateTable->getTable() . '.isRequest'] = $filter_data['filter_isRequest'];
//
//            if (isset($filter_data['filter_estateType']) && has_value($filter_data['filter_estateType']))
//                $where[$realEstateTable->getTable() . '.estateType'] = $filter_data['filter_estateType'];
//
//            $filter_regType = '';
//            if (isset($filter_data['filter_regType']) && $filter_data['filter_regType'])
//                $filter_regType = $filter_data['filter_regType'];
//            if (has_value($filter_regType))
//                $where[$realEstateTable->getTable() . '.regType'] = $filter_data['filter_regType'];
//
//            if (isset($filter_data['filter_stateId']) && has_value($filter_data['filter_stateId']))
//                $where[$realEstateTable->getTable() . '.stateId'] = $filter_data['filter_stateId'];
//
//            if (isset($filter_data['filter_cityId']) && has_value($filter_data['filter_cityId']))
//                $where[$realEstateTable->getTable() . '.cityId'] = $filter_data['filter_cityId'];
//
//            $estateArea = '';
//            if (isset($filter_data['filter_estateArea']) && $filter_data['filter_estateArea'])
//                $estateArea = $filter_data['filter_estateArea'];
//            if (has_value($estateArea)) {
//                $estateArea = explode('-', $estateArea);
//                $estateArea_from = @$estateArea[0];
//                $estateArea_to = @$estateArea[1];
//                if ($estateArea_from)
//                    $where[$realEstateTable->getTable() . '.estateArea >= ?'] = $estateArea_from;
//                if ($estateArea_to)
//                    $where[$realEstateTable->getTable() . '.estateArea <= ?'] = $estateArea_to;
//            }
//
//            if ($filter_regType != 2) {
//                $totalPrice_range = '';
//                if (isset($filter_data['filter_totalPrice_range']) && $filter_data['filter_totalPrice_range'])
//                    $totalPrice_range = $filter_data['filter_totalPrice_range'];
//                if (has_value($totalPrice_range)) {
//                    $totalPrice_range = explode('-', $totalPrice_range);
//                    $price_from = @$totalPrice_range[0];
//                    $price_to = @$totalPrice_range[1];
//                    if ($price_from)
//                        $where[$realEstateTable->getTable() . '.totalPrice >= ?'] = $price_from;
//                    if ($price_to)
//                        $where[$realEstateTable->getTable() . '.totalPrice <= ?'] = $price_to;
//                } else {
//                    $totalPrice_from = '';
//                    if (isset($filter_data['filter_totalPrice_from']) && $filter_data['filter_totalPrice_from'])
//                        $totalPrice_from = $filter_data['filter_totalPrice_from'];
//                    if (has_value($totalPrice_from))
//                        $where[$realEstateTable->getTable() . '.totalPrice >= ?'] = $totalPrice_from;
//                    $totalPrice_to = '';
//                    if (isset($filter_data['filter_totalPrice_to']) && $filter_data['filter_totalPrice_to'])
//                        $totalPrice_to = $filter_data['filter_totalPrice_to'];
//                    if (has_value($totalPrice_to))
//                        $where[$realEstateTable->getTable() . '.totalPrice <= ?'] = $totalPrice_to;
//                }
//            } else {
//                $filter_mortgagePrice = '';
//                if (isset($filter_data['filter_mortgagePrice']) && $filter_data['filter_mortgagePrice'])
//                    $filter_mortgagePrice = $filter_data['filter_mortgagePrice'];
//                if (has_value($filter_mortgagePrice))
//                    $where[$realEstateTable->getTable() . '.mortgagePrice <= ?'] = $filter_mortgagePrice;
//                $filter_rentalPrice = '';
//                if (isset($filter_data['filter_rentalPrice']) && $filter_data['filter_rentalPrice'])
//                    $filter_rentalPrice = $filter_data['filter_rentalPrice'];
//                if (has_value($filter_rentalPrice))
//                    $where[$realEstateTable->getTable() . '.rentalPrice <= ?'] = $filter_rentalPrice;
//            }
////            }
//        }
//
//        if ($status == "keyword")
//            $search = true;
//        $has_data = true;
//        if ($admin_route) {
//
//        } else {
//            if (!isset($where[$realEstateTable->getTable() . '.isRequest']))
//                $where[$realEstateTable->getTable() . '.isRequest'] = 0;
//            if (!$search)
//                $has_data = false;
//            if ($staticUserId)
//                $has_data = true;
//        }
//
//        $exportType = $this->params()->fromQuery('exportType', false);
//        $exportId = $this->params()->fromQuery('exportId', false);
//        $isExport = $this->params()->fromQuery('isExport', false);
//
//        if ($isExport) {
//            $has_data = true;
//            if ($exportId != 'all') {
//                $exportId = explode(',', $exportId);
//                foreach ($exportId as $val)
//                    $exportIds[] = (int)$val;
//                $where[$realEstateTable->getTable() . '.id'] = $exportIds;
//            }
//        }
//
//
//        $pagination = null;
//        $defaultAgent = null;
//        if ($has_data) {
//
//            if (!$isExport) { //agar print ya export nabud
//                $page = $this->params()->fromPost('page', 1);
//                $itemCount = $this->params()->fromPost('per_page', 20);
//            } else {
//                $page = null;
//                $itemCount = -1;
//            }
//
//            $pagination = $this->getRealEstateTable()
//                ->getAll($fields_table, $page, $where, array('tbl_realestate.isSpecial DESC', 'tbl_realestate.created DESC'), $keyword, $itemCount);
//            //var_dump($pagination);
////            $category_table = getSM()->get('category_item_table');
////            $estateType = $category_table->getItemsTreeByMachineName('estate_type');
//
//            $config = $this->getAdvanceConfig();
//            $defaultAgent = $config['defaultAgent'];
//            $defaultAgent = getSM()->get('user_table')->get($defaultAgent);
//            //   var_dump($filter_form);
//
//        }
//
//
//        /*$infoAgent = 0;
//        $configGSC = $this->getServiceLocator()->get('config_table')->getByVarName('global-real-estate-config');
//        if ($configGSC->varValue && isset($configGSC->varValue['info']))
//              $infoAgent = $configGSC->varValue['info'];*/
//
//        //   if (!$admin_route)
//        // $this->layout('layout/layout-onlineorders.phtml');
//        $uri = $this->request->getRequestUri();
//        $this->viewModel->setTemplate('real-estate/real-estate/list');
//        $this->viewModel->setVariables(
//            array(
//                'pagination' => $pagination,
////                'estateType' => $estateType,
//                'defaultAgent' => $defaultAgent,
//                'admin_route' => $admin_route,
//                'last_visit' => $last_visit,
//                'uri' => $uri,
//                'status' => $status,
//                'has_data' => $has_data,
//                'filed_api' => $fields_api,
//                'fields' => $fields,
//                'isExport' => $isExport,
//                'homeInfoCost' => $homeInfoCost,
//                'query' => $this->params()->fromPost(),
//                // 'infoAgent'=>$infoAgent,
//            )
//        );
//
////        if (!$staticUserId)
////
////        else{//request came from users view no search is needed
////            $this->viewModel->setVariable('filter_form', false);
////        $this->viewModel->setTerminal(true);
////        }
//
//        if ($isExport) {
//            $viewModel = new ViewModel();
//            $viewModel->setTerminal(true);
//            $viewModel->setTemplate('real-estate/real-estate/view-print');
//            $viewModel->setVariables(array(
//                'item' => $pagination,
//                'defaultAgent' => $defaultAgent,
//                'admin_route' => $admin_route,
//                'last_visit' => $last_visit,
//                'uri' => $uri,
//                'status' => $status,
//                'has_data' => $has_data,
//                'filed_api' => $fields_api,
//                'fields' => $fields,
//                'isExport' => $isExport,
//                'homeInfoCost' => $homeInfoCost,
//                'query' => $this->params()->fromPost(),
//            ));
//            $htmlOutput = $this->getServiceLocator()
//                ->get('viewrenderer')
//                ->render($viewModel);
//            if ($exportType == "word") {
//                return Export::exportToWord($htmlOutput, 'export-word-real-estate-list');
//            } else {
//                $mailTemplateId = null;
//                $config = $this->getServiceLocator()->get('config_table')->getByVarName('real_estate_config');
//                if (isset($config->varValue['allMailTemplate']))
//                    $mailTemplateId = $config->varValue['allMailTemplate'];
//
//                return Printer::getViewModel($htmlOutput, $mailTemplateId);
//            }
//        } else
//            return $this->viewModel;
//
//
////        $cookie = $this->getResponse()->getCookie();
////        $cookie->last_visit = time();
//    }

    public function listAction()
    {
        $parentAreaId = 0;
        //region Vars
        $where = new Where();
        $keyword = null;
        $order = array('tbl_realestate.isSpecial DESC');
        $sortOptions = array(
            'table' => array(
                'created' => t('REALESTATE_CRATED_DATE'),
                'priceOneMeter' => t('RealEstate_price_one_meter'),
                'totalPrice' => t('RealEstate_total_price'),
            ),
            'field' => array(
                'zirbana' => t('REALESTATE_ZIRBANA'),
                'c_otagh_khab' => t('REALESTATE_COUNT_ROOM'),
                // 'sale_sakht' => t('REALESTATE_BUILDING_YEAR'),
            ),
        );
        $validFilters = array(
            'table' => array(
                'estateType',
                'regType',
                'stateId',
                'cityId',
                'totalPrice',
                'mortgagePrice',
                'rentalPrice',
                'parentAreaId',
                'areaId',
                'isRequest',
                'userId',
            )
        );
        //endregion

        //region Params,Query,Post
        $page = $this->params()->fromQuery('page', 1);
        $itemCount = $this->params()->fromQuery('per_page', 20);
        $staticUserId = $this->params()->fromPost('userId', false);
        $sort = $this->params()->fromQuery('sort', 'table.created.desc');
        $onlyItems = $this->params()->fromPost('only-items', false);
        $route = 'app/real-estate/list';
        $filter = array_merge_recursive($this->params()->fromQuery(), $this->params()->fromPost());

        //endregion

        //region Config & DefaultAgent
        $configA = $this->getAdvanceConfig();
        $config = $this->getConfig();

        $homeInfoCost = 0;
        if (isset($config['homeInfoCost']))
            $homeInfoCost = $config['homeInfoCost'];

        $defaultAgent = $configA['defaultAgent'];
        $defaultAgent = getSM()->get('user_table')->get($defaultAgent);
        //endregion

        //region Fields
        /* @var $fields_api \Fields\API\Fields */
        $fields_api = $this->getFieldsApi();
        $fields_table = $this->getFieldsApi()->init('real_estate');
        $fields = $this->getFields();
        foreach ($fields as $f) {
            $validFilters['field'][] = $f['fieldMachineName'];
        }
        //endregion

        //region Sort
        $sort = explode('.', $sort);
        if (isset($sortOptions[@$sort[0]])) {
            $sort_table = $sort[0];
            if ($sort_table) {
                if (isset($sortOptions[$sort_table][@$sort[1]])) {
                    $sort_column = $sort[1];
                    $sort_order = (isset($sort[2]) && $sort[2] == 'asc') ? $sort[2] : 'desc';

                    if ($sort[0] == 'table')
                        $sort_table = 'tbl_realestate';
                    else
                        $sort_table = 'f';

                    $order[] = $sort_table . '.' . $sort_column . ' ' . $sort_order;
                }
            }
        }
        //endregion

        //region Filters
        /* $where->equalTo('tbl_realestate.status',1);
         $where->equalTo('tbl_realestate.status',3);
         $where->equalTo('tbl_realestate.status',4);*/
        $flagHasArea = 0;
        $where->notEqualTo('tbl_realestate.status', 6);
        $where->notEqualTo('tbl_realestate.status', 2);
        $where->notEqualTo('tbl_realestate.status', 5);
        $where->notEqualTo('tbl_realestate.status', 0);
        $where->greaterThan('tbl_realestate.expire', time());
        foreach ($filter as $type => $params) {
            if (isset($validFilters[$type])) {
                foreach ($params as $name => $value) {
                    if (in_array($name, $validFilters[$type])) {
                        $tableName = ($type == 'table') ? 'tbl_realestate' : 'f';
                        if ($name == 'parentAreaId') {
                            if (has_value($value)) {
                                $tableName = 'ca2';
                                $name = 'id';
                                $flagHasArea = 1;
                                $parentAreaId = $value;
                            }
                        }
                        if (!is_array($value))
                            $value = trim($value);

                        if (is_array($value))
                            $where->in($tableName . '.' . $name, $value);
                        elseif (strpos($value, ',') > 0) {
                            $value = explode(',', $value);
                            $where->between($tableName . '.' . $name, $value[0], $value[1]);
                        } elseif (isset($value) && has_value($value)) {
                            if (is_numeric($value))
                                $where->equalTo($tableName . '.' . $name, $value);
                            else
                                $where->like($tableName . '.' . $name, "%" . $value . "%");
                        }
                    }
                }
            }
        }
        //endregion

        $pagination = $this->getRealEstateTable()
            ->getAll($fields_table, $page, $where, $order, $keyword, $itemCount, $flagHasArea);

        //region View
        if ($onlyItems) {
            $this->viewModel->setTemplate('real-estate/real-estate/list-items');
            $this->viewModel->setTerminal(true);
        } else {
            $this->viewModel->setTemplate('real-estate/real-estate/list');
        }
        $this->viewModel->setTerminal(false);
        $this->viewModel->setVariables(
            array(
                'pagination' => $pagination,
                'defaultAgent' => $defaultAgent,
                'filed_api' => $fields_api,
                'fields' => $fields,
                'homeInfoCost' => $homeInfoCost,
                'query' => $this->params()->fromQuery(),
                'route' => $route,
                'sortOptions' => $sortOptions,
                'parentAreaId' => $parentAreaId,
            )
        );
        return $this->viewModel;
        //endregion
    }

    /**
     * @return ViewModel
     */
    public function itemsAction()
    {
        return $this->listAction(true);
    }

    public function viewAction($id = 0)
    {
        if ($id) {
            $permission = true; //namayeshe tamam etelaate melk zira kharidari karde ast
        } else {
            $id = $this->params()->fromRoute('id', 0);
            $permission = false;
        }
        $defaultAgent = null;
        $config = $this->getAdvanceConfig();
        $r_config = $this->getConfig();
        $defaultAgent = $config['defaultAgent'];

        $defaultAgent = getSM()->get('user_table')->get($defaultAgent);

        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $admin_route = strpos($route, 'admin') > -1;
        $fields_table = $this->getFieldsApi()->init('real_estate');
        $realEstateTable = $this->getRealEstateTable();
        $file = getSM()->get('file_table')->getByEntityType('real_estate', $id);
        $userId = current_user()->id;
        $fileArray = array();
        foreach ($file as $value)
            $fileArray[] = $value->fPath;


        $where = array();
        $userAgent = 0;
        if (!$userId && !$permission) {
            $where[$realEstateTable->getTable() . '.status'] = array(1, 3, 4);
            $where[$realEstateTable->getTable() . '.expire > ?'] = time();
            $where[$realEstateTable->getTable() . '.status <> ?'] = 6;
        } else
            $where[$realEstateTable->getTable() . '.status <> ?'] = 6;


        $item = $realEstateTable->get($id, $fields_table, $where, 2);

        if (isset($item->userId) && (int)$item->userId > 0 && (int)$item->userId != 2) {

            if (isset($r_config['agentUserRole']) && $r_config['agentUserRole']) {
                $userRole = getSM('user_role_table')->getRoles($item->userId);
                $userRoleArray = array();
                if ($userRole)
                    foreach ($userRole as $row)
                        $userRoleArray[] = $row['id'];

                $containsSearch = count(array_intersect($userRoleArray, $r_config['agentUserRole']));
                if ($containsSearch) {
                    $userAgent = getSM()->get('user_table')->get($item->userId);
                }
            }
        }
        if ($item) {

            /*$infoAgent = 0;
            $configGSC = $this->getServiceLocator()->get('config_table')->getByVarName('global-real-estate-config');
            if ($configGSC->varValue && isset($configGSC->varValue['info']))
                $infoAgent = $configGSC->varValue['info'];*/

            $google = $item->googleLatLong;
            $sessionObj = App::getSession('realEstate');
            if (!isset($sessionObj->id) or $sessionObj->id != $id) {
                $sessionObj->id = $id;
                $viewCounterMultiplier = getSM()->get('config_table')->getByVarName('real_estate_config')->varValue['viewCounterMultiplier'];
                if (!$viewCounterMultiplier)
                    $viewCounterMultiplier = 0;
                $realEstateTable->updateCounter($id, 'viewCounter', $viewCounterMultiplier);
            }
            if ((isAllowed(\RealEstate\Module::ADMIN_REAL_ESTATE_VIEW_ALL)) || current_user()->id == $item->userId || !App::isAdminRoute()) {
                $fields = $this->getFields();
                $fields = $this->filterFields($fields /*, $item->regType, $item->estateType*/);

                $this->viewModel->setVariables(array(
                    'item' => $item,
                    'fields' => $this->getFieldsApi()->generate($fields, $item),
                    'files' => $fileArray,
                    'admin_route' => $admin_route,
                    'google' => $google,
                    'permission' => $permission,
                    'defaultAgent' => $defaultAgent,
                    'userAgent' => $userAgent,
                    // 'infoAgent'=>$infoAgent
                ));
                $this->viewModel->setTemplate('real-estate/real-estate/view');
                return $this->viewModel;
            } else {
                $this->flashMessenger()->addErrorMessage('Your entry is restricted by admin');
                return $this->redirect()->toRoute('app/real-estate');
            }
        } else {
            $this->viewModel->setTemplate('real-estate/real-estate/view');
            return $this->viewModel;
        }
    }

    public function archiveAction()
    {

        $id = $this->params('id', false);

        $redirect = $this->params()->fromQuery('redirect', false);
        if ($id) {
            getSM()->get('real_estate_table')->update(array('status' => 2), array('id' => $id));
            $this->flashMessenger()->addSuccessMessage('Archived success');

        }
        if ($redirect)
            return $this->redirect()->toUrl($redirect);


        return $this->redirect()->toRoute('admin/real-estate');
    }

    public function editUserAction()
    {
        $allowEdit = ''; //3 halat be edit dade shod ke agar modir ejaze edit baraye hame dad agar field allowEdit melk 0 bud edit mishavad vali agar modir ezaze edit ra nadade bud yani allAllowEdit = 0 bud agar allowEdit = 1 bud hatman edit mishavad va agar = 2 bud be hich onvan edit nemishavad hatta agar allAllowEdit = 1 bashad
        $form = new \RealEstate\Form\EditUser();
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $form->setData($post);
            if ($form->isValid()) {
                if (isset($post['submit'])) {
                    $id = $this->params()->fromPost('id', false);
                    $passForEdit = $this->params()->fromPost('passForEdit', false);
                    $ownerMobile = $this->params()->fromPost('ownerMobile', false);
                    if ($data = $this->getRealEstateTable()->checkForEdit($id, $passForEdit, $ownerMobile)) {
                        $allowEdit = $data->allowEdit;

                        if ($allowEdit == 1) {
                            $path = url('app/real-estate/edit', array('id' => $id));
                            return $this->redirect()->toUrl($path);
                        }
                        if ($allowEdit == 0) {
                            $config = getConfig('real_estate_config')->varValue;
                            if ($config['allAllowEdit'] == 1) {
                                $path = "app/real-estate/edit";
                                return $this->redirect()->toRoute($path, array('id' => $id));
                            } else {
                                $this->flashMessenger()->addErrorMessage('Your entry is restricted by admin');
                            }
                        }
                        if ($allowEdit == 2)
                            $this->flashMessenger()->addErrorMessage('Your entry is restricted by admin');
                    } else
                        $this->flashMessenger()->addErrorMessage('Invalid Request !');
                }
            }
        }
        return new $this->viewModel(array(
            'form' => $form,
        ));


    }

    public function viewAllInfoEstateAction()
    {
        $routeParams = $this->params()->fromRoute('params');
        $routeParams = unserialize(base64_decode($routeParams));
        if (isset($routeParams['payerId'])) {
            $data = getSM('payment_table')->getStatus($routeParams['payerId']);
            if ($data) {
                //send email for customer
                $viewHtml = $this->viewAction($data['data']['validate']['params']['id']);
                //$html = $this->render($viewHtml);

                //send email for sahebe melk
                $notify = getNotifyApi();
                if ($notify) {
                    $params = array();
                    $selectRealEstate = getSM('real_estate_table')->getrealestate(array('id' => $data['data']['validate']['params']['id']))->current();
                    if ($selectRealEstate->ownerMobile) {
                        $sms = $notify->getSms();
                        $sms->to = $selectRealEstate->ownerMobile;
                    }
                    if ($selectRealEstate->ownerEmail) {
                        $email = $notify->getEmail();
                        $email->to = $selectRealEstate->ownerEmail;
                        $email->from = Mail::getFrom();
                        $email->subject = t('realEstate_payment_all_info');
                        $email->entityType = \RealEstate\Module::ENTITY_TYPE;
                        $email->queued = 0;
                    }
                    $notify->notify('RealEstate', 'real_estate_payment_all_info_for_homeowners', $params);
                }
                // $this->flashMessenger()->addSuccessMessage('A sample has been sent to your email');
                return $viewHtml;
            }
        }
        return $this->flashMessenger()->addErrorMessage('Invalid Request !');
    }

    public function validateSpecialAndInfoEstateAction()
    {
        $params = $this->params()->fromRoute('params');
        $params = unserialize(base64_decode($params));
        $data = getSM('payment_table')->getStatus($params['payerId']);
        if ($data) {
            if (isset($data['data']['validate']['params']['typePayment'])) {
                $dataArray = array();
                if ($data['data']['validate']['params']['typePayment'] == 'both')
                    $dataArray = array(
                        'isSpecial' => 1,
                        'showInfo' => 1,
                        'expireSpecial' => $data['data']['validate']['params']['expire']['expireSpecial'],
                        'expireShowInfo' => $data['data']['validate']['params']['expire']['expireShowInfo'],
                    );
                elseif ($data['data']['validate']['params']['typePayment'] == 'isSpecial')
                    $dataArray = array(
                        'isSpecial' => 1,
                        'expireSpecial' => $data['data']['validate']['params']['expire']['expireSpecial'],
                    );
                elseif ($data['data']['validate']['params']['typePayment'] == 'showInfo')
                    $dataArray = array(
                        'showInfo' => 1,
                        'expireShowInfo' => $data['data']['validate']['params']['expire']['expireShowInfo'],
                    );
                $id = $data['data']['validate']['params']['id'];
                if ($id) {
                    getSM('real_estate_table')->update($dataArray, array('id' => $id));


                    //send email for sahebe melk
                    $notify = getNotifyApi();
                    if ($notify) {
                        $params = array();
                        $selectRealEstate = getSM('real_estate_table')->getrealestate(array('id' => $id))->current();
                        if ($selectRealEstate->ownerMobile) {
                            $sms = $notify->getSms();
                            $sms->to = $selectRealEstate->ownerMobile;
                        }
                        if ($selectRealEstate->ownerEmail) {
                            $email = $notify->getEmail();
                            $email->to = $selectRealEstate->ownerEmail;
                            $email->from = Mail::getFrom();
                            $email->subject = t('realEstate_info_validate');
                            $email->entityType = \RealEstate\Module::ENTITY_TYPE;
                            $email->queued = 0;
                        }
                        $params = array(
                            '__PAYERID__' => $params['payerId'],
                            '_ESTATEID__' => $data['data']['validate']['params']['id'],
                            '__TYPE__' => $data['data']['validate']['params']['typePayment'],
                        );
                        $notify->notify('RealEstate', 'real_estate_validate_special_show_info', $params);
                    }

                    $this->flashMessenger()->addSuccessMessage('A sample has been sent to your email');
                    $this->viewModel->setTerminal(false);
                    return $this->viewModel;
                }
            }
        }
        $this->flashMessenger()->addErrorMessage('Invalid Request !');
        return $this->redirect()->toRoute('app/front-page');
    }

    public function realEstateAgentAction()
    {
        $page = $this->params()->fromQuery('page', 1);
        $agentUserRole = getConfig('real_estate_config')->varValue;
        if (isset($agentUserRole['agentUserRole']) && $agentUserRole['agentUserRole'])
            $agentUserRole = $agentUserRole['agentUserRole'];
        $select = getSM('user_table')->getByRoleId($agentUserRole, false, 'full', $page);

        $this->viewModel->setTemplate('real-estate/real-estate/real-estate-agent');
        $this->viewModel->setVariables(array(
            'select' => $select
        ));
        return $this->viewModel;
    }

    public function latestRealEstateRegTypeAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost()->toArray();
            if (isset($data['estateType']) && $data['estateType']) {
                /* @var $fields_api \Fields\API\Fields */
                $fields_api = $this->getFieldsApi();
                $fields_table = $this->getFieldsApi()->init('real_estate');
                $fields = $this->getFields();
                $dataArray = getSM('real-estate-table')->getLatestRealEstateArrayForList($data['estateType'], $data['estateRegType'], $data['count']);
                $this->viewModel->setTerminal(true);
                $this->viewModel->setTemplate('real-estate/helper/latest-real-estate-reg-type');
                $this->viewModel->setVariables(array(
                    'data' => $dataArray,
                    'estateType' => $data['estateType'],
                    'estateRegType' => $data['estateRegType'],
                    'filed_api' => $fields_api,
                    'fields' => $fields
                ));
                $html = $this->render($this->viewModel);
                return new JsonModel(array(
                    'status' => 1,
                    'html' => $html,
                ));
            }
        }
    }

    public function appDownloadAction()
    {
        $flag = false;
        if (isAllowed(\RealEstate\Module::APP_REAL_ESTATE_APP_DOWNLOAD)) {
            $flag = true;
            $this->redirect()->toUrl('http://melkyab.org/clients/melkyab/files/APP/realestate.rar');
        }
        return $this->viewModel->setVariables(array(
            'flag' => $flag,
        ));
    }

    public function compareAction()
    {
        $dataArray = array();
        $realId = $this->params()->fromQuery('compare');
        if ($realId) {
            $realId = explode(',', $realId);
            $realEstateTable = $this->getRealEstateTable();
            $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
            $admin_route = strpos($route, 'admin') > -1;
            /* @var $fields_api \Fields\API\Fields */
            $fields_api = $this->getFieldsApi();
            $fields_table = $this->getFieldsApi()->init('real_estate');

            $fields = $this->getFields();

            $where = array();
            $where[$realEstateTable->getTable() . '.status'] = array(1, 3, 4);
            $where[$realEstateTable->getTable() . '.expire > ?'] = time();
            $where[$realEstateTable->getTable() . '.status <> ?'] = 6;
            $where[$realEstateTable->getTable() . '.id'] = $realId;

            $pagination = null;
            $defaultAgent = null;

            $pagination = $this->getRealEstateTable()->getAll($fields_table, null, $where);
            foreach ($pagination as $row) {
                $dataArray[$row->id] = array(
                    'data' => (array)$row,
                    'fields' => $this->getFieldsApi()->generate($fields, $row, true),
                );
            }

            $this->viewModel->setTemplate('real-estate/real-estate/compare');
            $this->viewModel->setVariables(
                array(
                    'dataArray' => $dataArray,
                    'filed_api' => $fields_api,
                    'admin_route' => $admin_route,
                )
            );
            return $this->viewModel;
        } else {
            //TODO melki entekhab nashode ast
        }
    }

    public function searchByMapAction()
    {
        $estateRegType = getSM('category_item_table')->getItemsByMachineName('estate_reg_type');
        $this->viewModel->setTemplate('real-estate/real-estate/search-by-map');
        $this->viewModel->setVariables(array(
            'estateRegType' => $estateRegType,
        ));
        return $this->viewModel;
    }

    public function regionStatisticAction()
    {
        if ($this->request->isPost()) {
            $areaId = $this->params()->fromPost('areaId');
            $parentId = $this->params()->fromPost('parentId', 0);
            $areaText = $this->params()->fromPost('areaText', null);
            if ($areaId) {
                $areaIdForName = $areaId;
                if ($parentId) {
                    $areaIdArray = getSM('city_area_table')->getSubArray($areaId);
                    $areaId = array_keys($areaIdArray);
                }
                $data = $this->getRealEstateTable()->getPriceRange($areaId);
                if (!$areaText)
                    $areaText = getSM('city_area_table')->getAreaName($areaIdForName);
                $this->viewModel->setTemplate('real-estate/real-estate/region-statistic');
                $this->viewModel->setVariables(array(
                    'data' => $data,
                    'areaText' => $areaText,
                ));
                $html = $this->render($this->viewModel);
                return new JsonModel(array(
                    'status' => 1,
                    'html' => $html,
                ));
            }
        }
        return new JsonModel(array(
            'status' => 0,
        ));
    }

    public function statisticAction()
    {
        $this->viewModel->setTemplate('real-estate/real-estate/statistic');
        return $this->viewModel;
    }

    public function exportAction()
    {
        $type = $this->params()->fromQuery('exportType', 'print');
        $id = $this->params()->fromQuery('exportId', null);
        if ($id) {
            $idArray = explode(',', $id);
            $fields_table = $this->getFieldsApi()->init('real_estate');
            $fields = $this->getFields();
            $where = array(
                'tbl_realestate.status' => array(1, 3, 4),
                'tbl_realestate.expire > ?' => time(),
                'tbl_realestate.id' => $idArray,
            );
            $select = $this->getRealEstateTable()->getAll($fields_table, null, $where, null, null, null, 2);
            if ($type == 'print') {
                $this->viewModel->setTemplate('real-estate/real-estate/print');
                $this->viewModel->setVariables(array(
                    'item' => $select,
                    'fields' => $fields,
                ));
                $htmlOutput = $this->render($this->viewModel);
                $mailTemplateId = null;
                // TODO GET TEMPLATE FROM SYSTEM CONFIGS
                return Printer::getViewModel($htmlOutput, $mailTemplateId);
            }
        }
    }
}
