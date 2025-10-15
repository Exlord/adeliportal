<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace RealEstate\Controller;

use Application\Model\Config;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use DataView\Lib\Visualizer;
use RealEstate\Model\RealEstateTable;
use System\Controller\BaseAbstractActionController;
use User\API\User;
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
use Application\API\App;
use Application\API\Breadcrumb;
use Application\API\Export;
use Application\API\Printer;
use Mail\API\Mail;
use RealEstate\View\Helper\Widget;


class RealEstateAdminController extends BaseAbstractActionController
{

    private $route;
    private $isRequest;
    private $real_estate_config;
    private $real_estate_config_advance;
    private $defaultAgent;
    private $regType;
    private $estateType;
    private $state_list;
    private $city_list;
    private $area_list;
    private $numberOfImages;
    /**
     * @var \RealEstate\Form\RealEstate
     */
    private $form;
    private $cUserId;

    /**
     * @return RealEstateTable
     */
    private function getRealEstateTable()
    {
        return getSM()->get('real_estate_table');
    }

    public function indexAction()
    {
        $flagShow = false;
        $params = $this->params()->fromQuery();
        $grid = new DataGrid('real_estate_table');
        $grid->itemCountPerPage = 50;
        $grid->route = 'admin/real-estate';
        if (isAllowed(\RealEstate\Module::ADMIN_REAL_ESTATE_ALL) || current_user()->id == 1)
            $flagShow = true;

        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '30px', 'align' => 'center')));
        $grid->setIdCell($id);
        $address = new Column('addressShort', 'Address');
        $hits = new Column('viewCounter', 'REALESTATE_HITS', array('headerAttr' => array('width' => '30px', 'align' => 'center')));
        $hits->hasTextFilter = false;

        $isRequest = new Visualizer('isRequest', 'Requested',
            array(
                '0' => 'glyphicon glyphicon-remove text-danger grid-icon',
                '1' => 'glyphicon glyphicon-ok text-success grid-icon'
            ), array(
                '0' => t('Registered RealEstates'),
                '1' => t('Requested Estates'),
            ), array('headerAttr' => array('width' => '30px', 'align' => 'center')));

        $app = new Visualizer('app', 'REALESTATE_APP',
            array(
                '0' => 'glyphicon glyphicon-remove text-danger grid-icon',
                '1' => 'glyphicon glyphicon-ok text-success grid-icon'
            ), array(
                '0' => t('REALESTATE_NEW_IN_SITE'),
                '1' => t('REALESTATE_NEW_IN_APP'),
            ), array('headerAttr' => array('width' => '30px', 'align' => 'center')));

        $stateTitle = new Column('stateTitle', 'State');
        $stateTitle->setTableName('s');

        $cityTitle = new Column('cityTitle', 'City', array('headerAttr' => array('width' => '65px', 'align' => 'center')));
        $cityTitle->setTableName('c');

        $areaTitle = new Column('areaTitle', 'Region', array('headerAttr' => array('width' => '90px', 'align' => 'center')));
        $areaTitle->setTableName('ca');

        $estateTypeNameTitle = new Column('estateTypeName', 'Estate Type');
        $estateTypeNameTitle->hasTextFilter = false;


        $regType = new Custom('regType', 'Register Type', function (Column $col) {
            $allRegType = getSM('category_item_table')->getItemsArrayByCatName('estate_reg_type');
            if (isset($allRegType[$col->dataRow->regType]))
                return t($allRegType[$col->dataRow->regType]);
            else
                return '';
        }, array('headerAttr' => array('width' => '65px', 'align' => 'center')));

        $publish = new Custom('expire', 'Published', function (Column $col) {
            $dateFormat = getSM()->get('viewhelpermanager')->get('DateFormat');
            $icon = 'glyphicon glyphicon-ok text-success grid-icon';
            $status = t('Published');
            $adminMsg = '';
            if ($col->dataRow->expire < time() || $col->dataRow->status == 5 || $col->dataRow->status == 2 || $col->dataRow->status == 0) {
                $status = t('UnPublished');
                $icon = 'glyphicon glyphicon-remove text-danger grid-icon';
            }
            $html = '<div class=tooltip-publish>
                    <div>
                    <label>' . t('Status') . ' : ' . $status . '</label>
                    </div>
                    <div>
                    <label>' . $adminMsg . '</label>
                    </div>
                    <div>
                    <label>' . t('Today') . ' : ' . $dateFormat(time(), 4) . '</label>
                    <br/>
                    <label>' . t('Publish Down') . ' : ' . $dateFormat($col->dataRow->expire, 4) . '</label>
                    </div>
            </div>';
            return '<span data-tooltip="' . $html . '" class="' . $icon . '" ></span>';
        },array('headerAttr' => array('width' => '65px', 'align' => 'center')));

        /*$special = new Custom('special', 'Special Estate', function (Column $col) {
            if ($col->dataRow->isSpecial) {
                $dateFormat = getSM()->get('viewhelpermanager')->get('DateFormat');
                $class = 'publish-up';
                $status = t('Published');
                $expireSpecial = $dateFormat($col->dataRow->expireSpecial, 4);
                $adminMsg = '';
                if ($col->dataRow->expireSpecial < time() || $col->dataRow->isSpecial == 0) {
                    $status = t('UnPublished');
                    $class = 'publish-down';
                    $expireSpecial = t('realEstate_admin_list_not_payment');
                }
                $html = '<div class=tooltip-publish>
                    <div>
                    <label>' . t('Status') . ' : ' . $status . '</label>
                    </div>
                    <div>
                    <label>' . $adminMsg . '</label>
                    </div>
                    <div>
                    <label>' . t('Today') . ' : ' . $dateFormat(time(), 4) . '</label>
                    <br/>
                    <label>' . t('Publish Down') . ' : ' . $expireSpecial . '</label>
                    </div>
            </div>';
                return '<div data-tooltip="' . $html . '" class="' . $class . '" ></div>';
            }
        }, array('headerAttr' => array('width' => '25px', 'align' => 'center')));*/

        $areaTitle->hasTextFilter = true;

        $newArea = new Custom('newArea', 'New Region', function (Column $col) {
            $class = '';
            if (!empty($col->dataRow->newArea))
                $class = 'error-area';
            return '<span class="' . $class . '" >' . $col->dataRow->newArea . '</span>';
        });

        $view = new Button('View', function (Button $col) {
            $col->route = 'app/real-estate/view';
            $col->routeParams['title'] = '';
            $col->routeParams['id'] = $col->dataRow->id;
            $col->contentAttr['target'][] = '_blank';
            $col->icon = 'glyphicon glyphicon-eye-open text-primary';
        }, array(
            'headerAttr' => array(),
            'contentAttr' => array(
                // 'class' => array('ajax_page_load', 'btn', 'btn-default'),
                'title' => 'Admin Side View'
            ),
        ));

        $regTypeArray = getSM('category_item_table')->getItemsArrayByCatName('estate_reg_type');
        foreach ($regTypeArray as $key => $val)
            $regTypeArray[$key] = t($val);
        $groupRegType = new Column('regType', 'Register Type');
        $groupRegType->selectFilterData = $regTypeArray;

        $estateTypeArray = $this->getCategoryItemTable()->getItemsTreeByMachineName('estate_type');
        $groupEstateType = new Column('estateType', 'Estate Type');
        $groupEstateType->selectFilterData = $estateTypeArray;

        $expireTime = getSM('real_estate_table')->expireTime;
        $expireArray[0] = t('-- Select --');
        foreach ($expireTime as $key => $val)
            $expireArray[$key] = t($val);
        $expire = new Select('expire', 'Expire', $expireArray,
            array(), array('headerAttr' => array('width' => '35px')));

        $statusArray = array();
        foreach (RealEstateTable::$RealStatesStatusView as $key => $val)
            $statusArray[$key] = t(ucfirst($val));
        $status = new Select('status', 'Status', $statusArray,
            array('0' => 'inactive', '5' => 'inactive', '1' => 'active'), array('headerAttr' => array('width' => '35px')));

//        $groupStatus = new Column('status', 'Status');
        $status->selectFilterData = $statusArray;


        $expireSelect = array(
            'expired' => t('Expired'),
            'notExpired' => t('Not Expired'),
        );
        $groupExpired = new Column('expire', 'Expire Status');
        $groupExpired->selectFilterData = $expireSelect;
        if (isset($params['grid_filter_expire']) && $params['grid_filter_expire'] != '') {
            $groupExpired->filterValue = time();
            if ($params['grid_filter_expire'] == 'expired')
                $groupExpired->filterOperator = '<';
            if ($params['grid_filter_expire'] == 'notExpired')
                $groupExpired->filterOperator = '>';
        }


        $requestSelect = array(
            0 => t('Not Requested'),
            1 => t('Requested'),
        );
        $groupRequest = new Column('isRequest', 'Request Status');
        $groupRequest->selectFilterData = $requestSelect;

        $specialSelect = array(
            0 => t('Normal Estates'),
            1 => t('Special Estates'),
        );
        $groupSpecial = new Column('isSpecial', 'Status Special Estate');
        $groupSpecial->selectFilterData = $specialSelect;

        $edit = new EditButton();
        $delete = new DeleteButton();

        $grid->addColumns(array($id, /*$stateTitle,*/
            $cityTitle, $areaTitle, $estateTypeNameTitle, $regType, $address, $newArea, $hits /*, $special*/, $status , $publish/*, $expire*/, $app, $isRequest, $view, $edit, $delete));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('New Estate Transfer', 'New Estate Transfer', false, 'admin/real-estate/new-transfer');
        $grid->addNewButton('New Estate Request', 'New Estate Request', false, 'admin/real-estate/new-request');
        $grid->addButton('Print', 'Print', false, 'admin/real-estate/list', 'real-estate-print');
        //$grid->addButton('Word Export', 'Word Export', false, 'admin/real-estate', 'real-estate-word-export');
        $grid->addButton('Approve', 'Approve', false, 'admin/real-estate', 'approve');
        $grid->addButton('Disapprove', 'Disapprove', false, 'admin/real-estate', 'disapprove');
        $grid->setSelectFilters(array($status, $groupRequest, $groupSpecial, $groupExpired, $groupRegType, $groupEstateType));
        $grid->defaultSort = $id;
        $grid->defaultSortDirection = DataGrid::SORT_DESC;

        getSM('real_estate_table')->getEstateList($grid->getSelect(), $flagShow);

        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        $this->viewModel->setTemplate('real-estate/real-estate-admin/index');
        return $this->viewModel;
    }

    public function newAction()
    {
        if (!App::isAdminRoute())
            Breadcrumb::AddMvcPage('Real Estate', 'app/real-estate');

        $allowNew = true; //baraye ejaze new dadan ya nadadan


        if ($allowNew) {

            $this->initRealty('new-');

            $this->form->setAttribute('action', $this->url()->fromRoute($this->route));
            $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
            $admin_route = strpos($route, 'admin') > -1;
            $item = new Model\RealEstate();

            $this->form->bind($item);
            if ($this->request->isPost()) {

                $postFiles = $this->request->getFiles()->toArray();
                $postOther = $this->request->getPost()->toArray();
                $post = array_merge_recursive($postOther, $postFiles);
                $this->form->setData($post);

                if ($this->form->isValid()) {
                    $fields = $item->getTransferFields();
                    $images = $item->getImages();
                    $item->userId = current_user()->id;
                    if ($item->userId == 0)
                        $item->userId = $this->defaultAgent;
                    elseif ($item->userId != 2) {
                        $item->published = time();
                    } elseif ($item->userId == 2) {
                        $item->status = 1;
                        $item->published = time();
                    }

                    $numberOfDays = $item->expire;
                    $item->expire = time() + (2592000 * (int)$item->expire);
                    $item->created = time();

                    //$config = getConfig('real_estate_config')->varValue;

                    // $item->allowEdit = 0;
                    /*if (isset($post['priceOneMeter']) && $post['priceOneMeter'])
                        $item->priceOneMeter = intval($post['priceOneMeter']);*/

                    // register to phoneBook site
                    if (($item->ownerPhone || $item->ownerMobile) && $item->ownerEmail && current_user()->id == 0) {
                        $dataPhoneBook['ID'] = '';
                        $dataPhoneBook['nameAndFamily'] = $item->ownerName;
                        $dataPhoneBook['email'] = $item->ownerEmail;
                        $dataPhoneBook['mobile'] = $item->ownerMobile;
                        $dataPhoneBook['phone'] = $item->ownerPhone;
                        $dataPhoneBook['fax'] = '';
                        $dataPhoneBook['comment'] = 'Home has registered';
                        $dataPhoneBook['date'] = time();
                        if (!getSM('phoneBook_table')->searchEmail($dataPhoneBook['email']))
                            getSM('phoneBook_table')->save($dataPhoneBook);
                    }
                    // end
                    //register guest
                    $flagRegisterGuest = false;
                    $passUserMain = '';
                    if (current_user()->id == 0 && !empty($item->ownerEmail)) {
                        $userCount = getSM()->get('user_table')->getAll(array('username' => $item->ownerEmail))->count();
                        if ($userCount < 1) {
                            $passUserMain = rand(100, 1000000);
                            $dataArrayUser = array(
                                'basic' => array(
                                    'password' => $passUserMain,
                                    'username' => $item->ownerEmail,
                                    'email' => $item->ownerEmail,
                                    'displayName' => $item->ownerName,
                                )
                            );
                            $userId = User::Save($dataArrayUser);
                            $flagRegisterGuest = true;
                        }
                    }
                    //end

                    $isSpecial = 0;
                    $showInfo = 0;
                    if ($numberOfDays > 0) {
                        //check isSpecial & showInfo = agar 1 bud bayad 0 shavad va bad az taiid pardakht update mishavad
                        $expireSpecial = 0;
                        $expireShowInfo = 0;

                        $expirePaymentArray = array();

                        if (current_user()->id != 1 && current_user()->id != 2) { //agar admin ya serveradmin nabud be dargahe bank beravad
                            if ($item->isSpecial == 1) {
                                $isSpecial = 1;
                                $expireDate = '+ ' . $numberOfDays . ' month';
                                $expirePaymentArray['expireSpecial'] = strtotime($expireDate, time());
                            }
                            if ($item->showInfo == 1) {
                                $showInfo = 1;
                                $expireDate = '+ ' . $numberOfDays . ' month';
                                $expirePaymentArray['expireShowInfo'] = strtotime($expireDate, time());
                            }
                        } elseif (current_user()->id == 1 || current_user()->id == 2) { //agar admin ya server admin bud nabayad be dargahe bank beravad
                            if ($item->isSpecial == 1) {
                                $expireDate = '+ ' . $numberOfDays . ' month';
                                $item->expireSpecial = strtotime($expireDate, time());
                                $item->isSpecial = 1;
                            }
                            if ($item->showInfo == 1) {
                                $expireDate = '+ ' . $numberOfDays . ' month';
                                $item->expireShowInfo = strtotime($expireDate, time());
                                $item->showInfo = 1;
                            }
                        }
                        //end
                    } else {
                        $item->isSpecial = 0;
                        $item->showInfo = 0;
                    }

                    $item->isRequest = $this->isRequest;
                    $item->modified = time();
                    $item->app = 0;
                    if ($this->isRequest) {
                        $item->totalPrice = 0;
                        $item->rentalPrice = 0;
                        $item->priceOneMeter = 0;
                        $item->mortgagePrice = 0;
                    }

                    if (isset($post['addressFull']) && $post['addressFull'] && (!isset($post['addressShort']) || (isset($post['addressShort']) && !$post['addressShort']))) {
                        $lengthAddress = (int)strlen($post['addressFull']);
                        $item->addressShort = mb_substr($post['addressFull'], 0, (int)($lengthAddress / 4), 'UTF-8') . ' ...';
                    }

                    $id = $this->getRealEstateTable()->save($item);
                    $this->getFieldsApi()->save('real_estate', $id, $fields);

                    if (!$this->isRequest) {
                        $this->getFileApi()->save('real_estate', $id, $images, $this->numberOfImages);
                    }


                    //send notify baraye sahebe estate
                    $notify = getNotifyApi();
                    if ($notify) {
                        if ($item->ownerMobile) {
                            $sms = $notify->getSms();
                            $sms->to = $item->ownerMobile;
                        }
                        if ($item->ownerEmail) {
                            $email = $notify->getEmail();
                            $email->to = $item->ownerEmail;
                            $email->from = Mail::getFrom();
                            $email->subject = t('new_realEstate_registration_subject');
                            $email->entityType = \RealEstate\Module::ENTITY_TYPE;
                            $email->queued = 0;
                        }
                        $params = array(
                            '__CODE__' => $id,
                            '__NAME__' => $item->ownerName,
                        );
                        $notify->notify('RealEstate', 'new_estate_registration', $params);
                    }
                    //end

                    //send notify baraye modir system
                    $notify = getNotifyApi();
                    if ($notify) {
                        $params = array();

                        if ($this->defaultAgent) {
                            $selectAgent = getSM('user_table')->get($this->defaultAgent);
                            if (isset($selectAgent->email) && $selectAgent->email) {
                                $email = $notify->getEmail();
                                $email->to = $selectAgent->email;
                                $email->from = Mail::getFrom();
                                $email->subject = t('new_realEstate_registration_subject');
                                $email->entityType = \RealEstate\Module::ENTITY_TYPE;
                                $email->queued = 0;
                                $params = array(
                                    '__CODE__' => $id,
                                    '__NAME__' => $item->ownerName,
                                    '__EMAIL__' => $item->ownerEmail,
                                    '__MOBILE__' => $item->ownerMobile,
                                    '__ADDRESS__' => $item->addressFull,
                                );
                            }
                        }
                        $notify->notify('RealEstate', 'new_estate_registrationForManage', $params);
                    }
                    //end

                    //send Notify baraye moshavere amlak sahebe mantaghe
                    $notify = getNotifyApi();
                    if ($notify) {
                        $params = array();
                        if (!empty($item->areaId)) {
                            $agentAreaId = getSM('agent_area_table')->getAll(array('areaId' => $item->areaId))->current();
                            if ($agentAreaId) {
                                $agentAreaInfo = getSM('user_table')->get($agentAreaId->agentId);
                                if ($agentAreaInfo) {
                                    if ($agentAreaInfo->email) {
                                        $email = $notify->getEmail();
                                        $email->to = $agentAreaInfo->email;
                                        $email->from = Mail::getFrom();
                                        $email->subject = t('realEstate_new_estate_registration_in_your_area');
                                        $email->entityType = \RealEstate\Module::ENTITY_TYPE;
                                        $email->queued = 0;
                                        $params = array(
                                            '__CODE__' => $id,
                                        );
                                    }
                                    $notify->notify('RealEstate', 'estate_registration_in_agent_area', $params);
                                }
                            }
                        }
                    }
                    //end
                    if ($item->isRequest) {
                        $this->flashMessenger()->addSuccessMessage('REALESTATE_REQUEST_SUCCESS_CREATE');
                    } else {
                        $this->flashMessenger()->addSuccessMessage('Your Real-Estate has been created successfully');
                        $this->flashMessenger()->addInfoMessage(sprintf(t('Your Real-Estate ID is %s .'), $id));
                    }
                    db_log_info("new Real-Estate item with id:$id is created");
                    if (current_user()->id != 2) {
                        if ($item->isRequest) {
                            $this->flashMessenger()->addInfoMessage("RealEstate_REQUEST_New_WillBeApproved");
                        } else {
                            $this->flashMessenger()->addInfoMessage("RealEstate_New_WillBeApproved");
                        }
                    }
                    if ($flagRegisterGuest) {
                        $this->flashMessenger()->addInfoMessage(t('REALESTATE_REGISTER_GUEST'));
                        $this->flashMessenger()->addInfoMessage(sprintf(t('REALESTATE_INFO_REGISTER_GUEST'), $item->ownerEmail, $passUserMain));
                    }

                    if ($id && current_user()->id != 1 && current_user()->id != 2 && $numberOfDays > 0) {
                        //check for isEspicial & showInfo = redirect to payment
                        $specialAddsCost = 0;
                        if (isset($this->real_estate_config['specialRealtyCost']))
                            $specialAddsCost = (int)$this->real_estate_config['specialRealtyCost'];
                        $showInfoPrice = 0;
                        if (isset($this->real_estate_config['showInfoPrice']))
                            $showInfoPrice = (int)$this->real_estate_config['showInfoPrice'];
                        $typePayment = null;
                        $amountPayment = 0;
                        $flagPayment = false;
                        if ($isSpecial && $showInfo) {
                            $typePayment = 'both';
                            $amountPayment = ($specialAddsCost * $numberOfDays) + ($showInfoPrice * $numberOfDays);
                            $flagPayment = true;
                        } elseif ($isSpecial && !$showInfo) {
                            $typePayment = 'isSpecial';
                            $amountPayment = $specialAddsCost * $numberOfDays;
                            $flagPayment = true;
                        } elseif ($showInfo && !$isSpecial) {
                            $typePayment = 'showInfo';
                            $amountPayment = $showInfoPrice * $numberOfDays;
                            $flagPayment = true;
                        }

                        if ($flagPayment) {
                            //payment for realestate
                            $paymentParams = array(
                                'amount' => $amountPayment,
                                'email' => $item->ownerEmail,
                                'comment' => 'Pay For Create a Special Home',
                                'validate' => array(
                                    'route' => 'app/real-estate/create-special-estate',
                                    'params' => array(
                                        'id' => $id,
                                        'typePayment' => $typePayment,
                                        'expire' => $expirePaymentArray,
                                    ),
                                )
                            );
                            $paymentParams = serialize($paymentParams);
                            $paymentParams = base64_encode($paymentParams);
                            return $this->redirect()->toRoute('app/payment', array(), array('query' => array('routeParams' => $paymentParams)));
                            //end
                        }
                        //end
                    }

                    $item = new Model\RealEstate();
                    $this->form->bind($item);
                    $post = $this->request->getPost();
                    if (isset($post['buttons']['submit-new'])) {

                        // removes images info from html elements after form submitted (submitted and new form requested)
                        $images = $this->form->get('images');
                        $images = $images->getElements();
                        foreach ($images as $value)
                            $value->setValue(null);
                    }

                    $newViewModel = new viewModel();
                    $newViewModel->setTemplate('real-estate/real-estate-admin/after-new');
                    return $newViewModel;
                }
            }
            $estateType_regType = $this->real_estate_config_advance['estateType_regType'];
            $estateType_fields = $this->real_estate_config_advance['estateType_fields'];

            $this->viewModel->setVariables(
                array(
                    'form' => $this->form,
                    'estateType_regType' => json_encode($estateType_regType),
                    'estateType_fields' => json_encode($estateType_fields),
                    'isRequest' => $this->isRequest,
                    'admin_route' => $admin_route,
                )
            );
            $this->viewModel->setTerminal(false);
            $this->viewModel->setTemplate('real-estate/real-estate-admin/new');
            return $this->viewModel;
        } else {
            $this->flashMessenger()->addErrorMessage('you have reached your limit to register realty');
            return $this->redirect()->toRoute('app/front-page');
        }
    }

    public function editAction()
    {

        $id = $this->params()->fromRoute('id');
        $route0 = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $admin_route = strpos($route0, 'admin') > -1;
        $homeId = $id;
        $data = $this->getRealEstateTable()->getForEdit($id, $this->isRequest);
        if (isset($data['transferFields']))
            $fieldDataId = $data['transferFields']['id'];

        if ((isAllowed(\RealEstate\Module::ADMIN_REAL_ESTATE_EDIT_ALL) || current_user()->id == $data['userId'])) {

            $images = $data['images'];
            $google = $data['googleLatLong'];
            unset($data['images']);
            $app = 0;
            if (isset($data['app']) && $data['app'])
                $app = 1;
            $this->initRealty('edit', $data['userId'], $app);

            $route = getSM()->get('Request')->getRequestUri();

            $path = strpos($route, 'admin') > -1 ? 'admin/real-estate/edit' : 'app/real-estate/edit';

            $this->form->setAttribute('action', $this->url()->fromRoute($path, array('id' => $id)));
            $this->form->setData($data);


            if ($this->request->isPost()) {

                $postFiles = $this->request->getFiles()->toArray();
                $postOther = $this->request->getPost()->toArray();
                $post = array_merge_recursive($postOther, $postFiles);

                $this->form->setData($post);


                if ($this->form->isValid()) {
                    $item = $this->form->getData();


                    $fields = $item['transferFields'];
                    $imagess = array();
                    if (isset($item['images']))
                        $imagess = $item['images'];
                    $maxCountImages = (int)($this->numberOfImages) - (int)(count($images));
                    if (($maxCountImages < 1) || ($maxCountImages == ''))
                        $maxCountImages = 0;
                    unset($item['images']);
                    unset($item['transferFields']);
                    unset($item['buttons']);
                    $item['userId'] = $data['userId'];
                    if (!$item['userId']) {
                        $item['userId'] = $this->defaultAgent;
                        $item['status'] = 0;
                    }
                    $numberOfDays = (int)$item['expire'];
                    $item['expire'] = time() + (2592000 * (int)$item['expire']);
                    $item['modified'] = time();
                    $item['id'] = $id;

                    /*if (isset($post['priceOneMeter']) && $post['priceOneMeter'])
                        $item['priceOneMeter'] = intval($post['priceOneMeter']);*/
                    $isSpecial = 0;
                    $showInfo = 0;
                    if ($numberOfDays > 0) {
                        //check isSpecial & showInfo = agar 1 bud bayad 0 shavad va bad az taiid pardakht update mishavad
                        $expireSpecial = 0;
                        $expireShowInfo = 0;
                        $item['isSpecial'] = 0;
                        $item['showInfo'] = 0;
                        $expirePaymentArray = array();
                        if (current_user()->id != 1 && current_user()->id != 2) { //agar admin ya serveradmin nabud be dargahe bank beravad
                            if (isset($post['isSpecial']) && $post['isSpecial'] == 1) {
                                if ($data['expireSpecial'] < time()) {
                                    $isSpecial = 1;
                                    $expireDate = '+ ' . $numberOfDays . ' month';
                                    $expirePaymentArray['expireSpecial'] = strtotime($expireDate, time());

                                } else {
                                    $isSpecial = 1;
                                    $expireDate = '+ ' . $numberOfDays . ' month';
                                    $expirePaymentArray['expireSpecial'] = strtotime($expireDate, $data['expireSpecial']);
                                }
                            }
                            if (isset($post['showInfo']) && $post['showInfo'] == 1) {
                                if ($data['expireShowInfo'] < time()) {
                                    $showInfo = 1;
                                    $expireDate = '+ ' . $numberOfDays . ' month';
                                    $expirePaymentArray['expireShowInfo'] = strtotime($expireDate, time());
                                } else {
                                    $showInfo = 1;
                                    $expireDate = '+ ' . $numberOfDays . ' month';
                                    $expirePaymentArray['expireShowInfo'] = strtotime($expireDate, $data['expireShowInfo']);
                                }
                            }
                        } elseif (current_user()->id == 1 || current_user()->id == 2) { //agar admin ya server admin bud nabayad be dargahe bank beravad
                            if (isset($post['isSpecial']) && $post['isSpecial'] == 1) {
                                if ($data['expireSpecial'] < time()) {
                                    $expireDate = '+ ' . $numberOfDays . ' month';
                                    $item['expireSpecial'] = strtotime($expireDate, time());
                                    $item['isSpecial'] = 1;
                                } else {
                                    $expireDate = '+ ' . $numberOfDays . ' month';
                                    $item['expireSpecial'] = strtotime($expireDate, $data['expireSpecial']);
                                    $item['isSpecial'] = 1;
                                }
                            }
                            if (isset($post['showInfo']) && $post['showInfo'] == 1) {
                                if ($data['expireShowInfo'] < time()) {
                                    $expireDate = '+ ' . $numberOfDays . ' month';
                                    $item['expireShowInfo'] = strtotime($expireDate, time());
                                    $item['showInfo'] = 1;
                                } else {
                                    $expireDate = '+ ' . $numberOfDays . ' month';
                                    $item['expireShowInfo'] = strtotime($expireDate, $data['expireShowInfo']);
                                    $item['showInfo'] = 1;
                                }
                            }
                        }
                        //end
                    }

                    $this->getRealEstateTable()->save($item);
                    $id = $item['id'];
                    //send Notify baraye moshavere amlak sahebe mantaghe
                    if ($item['areaId'] != $data['areaId']) {
                        if (!empty($item->areaId)) {
                            $agentAreaId = getSM('agent_area_table')->getAll(array('areaId' => $item->areaId))->current();
                            if ($agentAreaId) {
                                $agentAreaInfo = getSM('user_table')->get($agentAreaId->agentId);
                                if ($agentAreaInfo) {
                                    if ($agentAreaInfo->email) {
                                        $notify = getNotifyApi();
                                        if ($notify) {
                                            $email = $notify->getEmail();
                                            $email->to = $agentAreaInfo->email;
                                            $email->from = Mail::getFrom();
                                            $email->subject = t('realEstate_new_estate_registration_in_your_area');
                                            $email->entityType = \RealEstate\Module::ENTITY_TYPE;
                                            $email->queued = 0;
                                            $params = array(
                                                '__CODE__' => $item['id'],
                                            );
                                            $notify->notify('RealEstate', 'estate_registration_in_agent_area', $params);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //end


                    if (!$this->isRequest) {
                        $fields['id'] = $fieldDataId;
                        $this->getFieldsApi()->save('real_estate', $item['id'], $fields);

                        $this->getFileApi()->save('real_estate', $item['id'], $imagess, $maxCountImages);
                    }

                    getSM()->get('logger')->log(LOGGER_INFO, "Real-Estate item with id:$id is Edited");
                    $this->flashMessenger()->addSuccessMessage('Your Real-Estate has been edited successfully');
                    $data = $this->getFileTable()->getByEntityType('real_estate', $id, true);


                    $images = $data;

                    if ($imagess[0]['name'] != '' && $maxCountImages != 0)
                        $this->flashMessenger()->addSuccessMessage('Your Images has been uploaded successfully');
                    elseif ($maxCountImages == 0)
                        $this->flashMessenger()->addErrorMessage('To upload a new photo, delete old images');

                    if ($id && current_user()->id != 1 && current_user()->id != 2 && $numberOfDays > 0) {
                        //check for isEspicial & showInfo = redirect to payment
                        $specialAddsCost = (int)$this->real_estate_config['specialRealtyCost'];
                        $showInfoPrice = (int)$this->real_estate_config['showInfoPrice'];
                        $typePayment = null;
                        $amountPayment = 0;
                        $flagPayment = false;
                        if ($isSpecial && $showInfo) {
                            $typePayment = 'both';
                            $amountPayment = ($specialAddsCost * $numberOfDays) + ($showInfoPrice * $numberOfDays);
                            $flagPayment = true;
                        } elseif ($isSpecial && !$showInfo) {
                            $typePayment = 'isSpecial';
                            $amountPayment = $specialAddsCost * $numberOfDays;
                            $flagPayment = true;
                        } elseif ($showInfo && !$isSpecial) {
                            $typePayment = 'showInfo';
                            $amountPayment = $showInfoPrice * $numberOfDays;
                            $flagPayment = true;
                        }

                        if ($flagPayment) {
                            //payment for realestate
                            $paymentParams = array(
                                'amount' => $amountPayment,
                                'email' => $item['ownerEmail'],
                                'comment' => 'Pay For Create a Special Home',
                                'validate' => array(
                                    'route' => 'app/real-estate/create-special-estate',
                                    'params' => array(
                                        'id' => $id,
                                        'typePayment' => $typePayment,
                                        'expire' => $expirePaymentArray,
                                    ),
                                )
                            );
                            $paymentParams = serialize($paymentParams);
                            $paymentParams = base64_encode($paymentParams);
                            return $this->redirect()->toRoute('app/payment', array(), array('query' => array('routeParams' => $paymentParams)));
                            //end
                        }
                        //end
                    }

                }
            }

            $estateType_regType = $this->real_estate_config_advance['estateType_regType'];
            $estateType_fields = $this->real_estate_config_advance['estateType_fields'];
            $this->viewModel->setVariables(
                array(
                    'form' => $this->form,
                    'estateType_regType' => json_encode($estateType_regType),
                    'estateType_fields' => json_encode($estateType_fields),
                    'isRequest' => $this->isRequest,
                    'images' => $images,
                    'maxImage' => $this->numberOfImages,
                    'homeId' => $homeId,
                    'admin_route' => $admin_route,
                    'google' => $google,
                )
            );
            $this->viewModel->setTemplate('real-estate/real-estate-admin/new');
            return $this->viewModel;
        } else {
            $this->flashMessenger()->addErrorMessage('Your entry is restricted by admin');
            return $this->redirect()->toRoute('app/real-estate');
        }

    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            $url = url('admin/real-estate');
            if ($id) {
                if ($field == 'expire') {
                    if ($value != 0) {
                        $item = getSM()->get('real_estate_table')->getForUpdate($id, array('expire'));
                        $current_time = time();
                        $current_expire = ($item->expire > $current_time) ? $item->expire : $current_time;
                        $expire = $current_expire + ($value * 2592000);
                        getSM()->get('real_estate_table')->update(array('expire' => $expire), array('id' => $id));

                        return new JsonModel(array(
                            'status' => 1,
                            'callback' => 'System.Pages.ajaxLoad("' . $url . '")',
                        ));
                    }
                }
                if ($field == 'status') {

                    $status = $value;
                    if ($status != '') {
                        $idArray = explode(',', $id);
                        /*$data = array();
                        foreach ($idArray as $val) {
                            $data[] = array(
                                'status' => $status,
                                'id' => $val
                            );
                        }*/
                        if ($status == 1) {
                            $params = array();
                            $select = getSM('real_estate_table')->getRealestate(array('id' => $idArray));
                            $notify = getNotifyApi();
                            if ($notify) {
                                foreach ($select as $row) {
                                    //send notify baraye sahebe estate
                                    if ($row->ownerMobile && $row->status != 1) {
                                        $sms = $notify->getSms();
                                        $sms->to = $row->ownerMobile;
                                    }
                                    if ($row->ownerEmail && $row->status != 1) {
                                        $email = $notify->getEmail();
                                        $email->to = $row->ownerEmail;
                                        $email->from = Mail::getFrom();
                                        $email->subject = t('realEstate_approved_estate');
                                        $email->entityType = \RealEstate\Module::ENTITY_TYPE;
                                        $email->queued = 0;
                                    }
                                    $params = array(
                                        '__CODE__' => $row->id,
                                        '__NAME__' => $row->ownerName,
                                        '__VIEWURL__' => App::siteUrl() . url('app/real-estate/view', array('id' => $row->id, 'title' => '')),
                                    );
                                    $notify->notify('RealEstate', 'approved_estate', $params);
                                }
                            }
                            //end

                        }
                        getSM()->get('real_estate_table')->update(array('status' => $status), array('id' => $idArray));
                        // getSM()->get('real_estate_table')->multiSave($data);

                        return new JsonModel(array(
                            'status' => 1,
                            'callback' => 'System.Pages.ajaxLoad("' . $url . '")',
                        ));
                    }
                }
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $url = url('admin/real-estate');
                $idArray = $id;
                $estate = getSM('real_estate_table')->getRealestate(array('id' => $idArray));
                if ($estate) {
                    foreach ($estate as $row) {
                        if ($row->status != 5) {
                            getSM('real_estate_table')->update(array('status' => 5), array('id' => $row->id));
                        } else {
                            $this->getRealEstateTable()->remove($row->id);
                            $this->getFieldsApi()->init('real_estate');
                            $this->getFieldsApi()->remove($row->id);
                            $file = getSM()->get('file_table')->getByEntityType('real_estate', $row->id);
                            foreach ($file as $value) //TODO delete filed in file api not here
                                @unlink(PUBLIC_PATH . $value->fPath);
                            getSM()->get('file_table')->removeById($row->id);
                        }
                    }
                    return new JsonModel(array(
                        'status' => 1,
                        'callback' => 'System.Pages.ajaxLoad("' . $url . '")',
                    ));
                }
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function initRealty($type = 'new-', $userId = 0, $app = 0)
    {
        $this->getRoute($type);
        $this->real_estate_config = $this->getConfigTable()->getByVarName('real_estate_config')->varValue;

        $this->real_estate_config_advance = $this->getConfigTable()->getByVarName('real_estate_config_advance')->varValue;
        $this->defaultAgent = $this->real_estate_config_advance['defaultAgent'];


        $this->regType = getSM('category_item_table')->getItemsArrayByCatName('estate_reg_type');
        $this->estateType = $this->getCategoryItemTable()->getItemsTreeByMachineName('estate_type');

        $this->state_list = getSM()->get('state_table')->getArray(1);
        $selected_state = $this->params()->fromPost('stateId', current(array_keys($this->state_list)));
        $this->city_list = getSM()->get('city_table')->getArray($selected_state);
        $this->area_list = getSM()->get('city_area_table')->getArray(140, -1);
        $this->numberOfImages = 0;
        if (!$this->isRequest) {
            $this->cUserId = current_user()->id;
            $roles = current_user()->roles;

            if ($this->cUserId == 0 && $type == "edit") // -------------- karbar jari mehman ast -----------
                $roles = getSM()->get('user_role_table')->getRolesArray($userId); //-------- role id sahebe melk ---------

            foreach ($roles as $role) {
                /*if ($this->cUserId) //---------------- agar karbar mehman nist -------------------
                    $roleId = $role['id'];
                else
                    $roleId = $role;*/

                if (isset($this->real_estate_config['numberOfImages']) && isset($this->real_estate_config['numberOfImages'][$role['id']]))
                    $this->numberOfImages = max((int)$this->real_estate_config['numberOfImages'][$role['id']], $this->numberOfImages);
                else
                    $this->numberOfImages = 1;


                //$this->numberOfImages -= $countOldImages;
            }
        }

        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $admin_route = strpos($route, 'admin') > -1;
        $route_prefix = $admin_route ? 'admin' : 'app';
        $this->form = new Form\RealEstate($this->getRealEstateTable()->expireTime, $this->regType, $this->estateType, $this->state_list, $this->city_list, $this->numberOfImages, $this->isRequest, $route_prefix, $this->area_list,$app);
        $this->form->get('buttons')->remove('cancel');

        //$this->fields_id_list = $this->real_estate_config['transferFields'];
        $transferFields = new Fieldset('transferFields');
        $this->form->add($transferFields);
        $inputFilters = $this->getFieldsApi()->loadFieldsByType('real_estate', $this->form, $transferFields);
        $this->form->addInputFilters(array('transferFields' => $inputFilters));


        if (!$this->isRequest) {

            if (!App::isAdminRoute())
                Breadcrumb::AddMvcPage('New Estate Transfer', 'app/real-estate/new-transfer');

            $specialAddsCost = 0;
            $showInfoPrice = 0;
            if (isset($this->real_estate_config['specialRealtyCost']))
                $specialAddsCost = $this->real_estate_config['specialRealtyCost'];
            if (isset($this->real_estate_config['showInfoPrice']))
                $showInfoPrice = $this->real_estate_config['showInfoPrice'];
            $options = $this->form->get('isSpecial')->getOptions();
            $showInfo = $this->form->get('showInfo')->getOptions();
            $specialAddsCost = (int)$specialAddsCost;
            $showInfoPrice = (int)$showInfoPrice;
            $options['description'] = sprintf(t($options['description']), number_format($specialAddsCost));
            $showInfo['description'] = sprintf(t($showInfo['description']), number_format($showInfoPrice));
            $this->form->get('isSpecial')->setOptions($options);
            $this->form->get('showInfo')->setOptions($showInfo);
            unset($options);
            unset($showInfo);
        } else
            if (!App::isAdminRoute())
                Breadcrumb::AddMvcPage('New Estate Request', 'app/real-estate/new-request');

    }

    private function getRoute($type)
    {
        $route_type = $this->params()->fromRoute('route-type');

        if (!App::isAdminRoute())
            $this->route = 'app/real-estate/' . $type . (string)$route_type;
        else
            $this->route = 'admin/real-estate/' . $type . (string)$route_type;
        $this->isRequest = ($route_type == 'request');

    }

    public function configAction()
    {
        $mailTemplate = getSM('template_table')->getArray();
        /* @var $config Config */
        $config = $this->getServiceLocator()->get('config_table')->getByVarName('real_estate_config');
        $roles = getSM('role_table')->getRoleForSelect();
        $form = prepareConfigForm(new Form\Config($roles, $mailTemplate));
        $form->setData($config->varValue);

        if ($this->request->isPost()) {

            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());

                if ($form->isValid()) {
                    $config->setVarValue($form->getData());

                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Real Estate Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Real-Estate configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('real-estate/real-estate-admin/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function configMoreAction()
    {
        ini_set('memory_limit', '268435456');
        $emptyFields = true;
        /* @var $config Config */
        $config = $this->getServiceLocator()->get('config_table')->getByVarName('real_estate_config_advance');
        $form = $this->getConfigForm();
        if ($config && $config->varValue)
            $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            $result = array();

            $this->parseUrlString($post['data'], $result);
            unset($post['data']);
            $post = array_merge_recursive($result, $post);
            if (isset($post['buttons']['submit'])) {
                $form->setData($post);

                if ($form->isValid()) {

                    $config->setVarValue($form->getData());

                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Real Estate Advance Configs changed");
                    $this->flashMessenger()->addInfoMessage('Real-Estate configs saved successfully');
                } else
                    $this->formHasErrors();
            }
        }

        $this->viewModel->setTemplate('real-estate/real-estate-admin/config-more');
        $this->viewModel->setVariables(array(
            'form' => $form,
            'emptyFields' => $form->hasFields
        ));
        return $this->viewModel;
    }

    private function getConfigForm()
    {
        $roles = '';
        /* @var $config Config */
        $config = $this->getServiceLocator()->get('config_table')->getByVarName('real_estate_config');

        if (isset($config->varValue['agentUserRole']))
            $roles = $config->varValue['agentUserRole'];


        $users = getSM()->get('user_table')->getByRoleId($roles);

        $form = new  Form\ConfigMore($users);
        $fields_list = $this->getFieldsTable()->getByEntityType('real_estate');
        if ($fields_list && $fields_list->count()) {

            $fields_list = $fields_list->toArray();

            $form->hasFields = true;

            $category_table = $this->getServiceLocator()->get('category_item_table');

            $regType = getSM('category_item_table')->getItemsArrayByCatName('estate_reg_type');
            $estateType = $category_table->getItemsTreeByMachineName('estate_type');
            unset($estateType[0]);

            $form->regType = $regType;
            $form->estateType = $estateType;
            $estateType_regType_fieldset = $form->get('estateType_regType');
            $estateType_fields_fieldset = $form->get('estateType_fields');
//        $regType_fields_fieldset = $form->get('regType_fields');

            //disabled regTypes for EstateTypes
            foreach ($estateType as $key => $value) {
                $estate_fieldset = new Fieldset();
                $estate_fieldset->setName($key);
                $estate_fieldset->setLabel($value);
                foreach ($regType as $reg_key => $reg_value) {
                    $estate_fieldset->add(array(
                        'name' => $reg_key,
                        'type' => 'Zend\Form\Element\Checkbox',
                        'options' => array(
                            'label' => $reg_value
                        ),

                    ));
                }
                $estateType_regType_fieldset->add($estate_fieldset);
            }

            //disabled fields for EstateTypes
            foreach ($estateType as $key => $value) {
                $estate_fieldset = new Fieldset();
                $estate_fieldset->setName($key);
                $estate_fieldset->setLabel($value);

                foreach ($regType as $Keyreg => $reg) {
                    $regType_fieldset = new Fieldset();
                    $regType_fieldset->setName($Keyreg);
                    $regType_fieldset->setLabel($reg);
                    foreach ($fields_list as $field) {
                        $regType_fieldset->add(array(
                            'name' => $field['fieldMachineName'],
                            'type' => 'Zend\Form\Element\Checkbox',
                            'options' => array(
                                'label' => $field['fieldName'],
                                'disable-twb' => true
                            )
                        ));
                    }
                    $estate_fieldset->add($regType_fieldset);
                }


                $estateType_fields_fieldset->add($estate_fieldset);

            }

            //disabled fields for RegTypes
            $filter = $form->getInputFilter();
            $filter = $filter->get('estateType_fields');


            foreach ($estateType as $key => $value) {
                $estateFilter = $filter->get($key);

                foreach ($regType as $Keyreg => $reg) {
                    $regFilter = $estateFilter->get($Keyreg);
                    foreach ($fields_list as $field) {
                        $inputFilter = $regFilter->get($field['fieldMachineName']);
                        $inputFilter->setRequired(false);
                    }
                }
            }
        } else
            $form->hasFields = false;
        return $form;
    }

    public function agentAreaAction()
    {
        //insert agent areas
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            if (isset($data['areaId']) && $data['areaId'] && isset($data['agentId']) && $data['agentId']) {
                $insertData = array();
                $arrayId = explode(',', $data['areaId']);
                foreach ($arrayId as $val) {
                    $model = new \RealEstate\Model\AgentArea();
                    $model->agentId = $data['agentId'];
                    $model->areaId = $val;
                    $insertData[] = $model;
                }
                if (!empty($insertData)) {
                    getSM('agent_area_table')->removeAll($data['agentId']);
                    getSM('agent_area_table')->multiSave($insertData);
                    return new JsonModel(array(
                        'status' => 1
                    ));
                }
            }
            return new JsonModel(array(
                'status' => 0
            ));
        }
        //end

        //get real estate agent list
        $agentUserRoleId = array();
        $agentUserRole = getConfig('real_estate_config')->varValue;
        if (isset($agentUserRole['agentUserRole']) && $agentUserRole['agentUserRole'])
            $agentUserRoleId = $agentUserRole['agentUserRole'];
        $selectAgent = getSM('user_table')->getByRoleId($agentUserRoleId, false, 'array', 1);
        //end

        //get state & city & area for default
        $state_list = getSM()->get('state_table')->getArray(1);
        $selected_state = $this->params()->fromPost('stateId', current(array_keys($state_list)));
        $city_list = getSM()->get('city_table')->getArray($selected_state);
        $area_list = getSM()->get('city_area_table')->getArray(140, 0);
        //end

        $this->viewModel->setTemplate('real-estate/real-estate-admin/agent-area');
        $this->viewModel->setVariables(array(
            'selectAgent' => $selectAgent,
            'stateList' => $state_list,
            'cityList' => $city_list,
            'areaList' => $area_list
        ));
        return $this->viewModel;
    }

    public function getAgentAreaAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            if (isset($data['agentId']) && $data['agentId']) {
                $select = getSM('agent_area_table')->getAgentAreaArray($data['agentId']);

                if (!empty($select)) {
                    $this->viewModel->setTerminal(true);
                    $this->viewModel->setTemplate('real-estate/real-estate-admin/get-agent-area');
                    $this->viewModel->setVariables(array('select' => $select));
                    $html = $this->render($this->viewModel);
                    return new JsonModel(array(
                        'status' => 1,
                        'html' => $html,
                    ));
                }
            }
        }
        return new JsonModel(array(
            'status' => 0
        ));
    }

    private function parseUrlString($string, &$result)
    {
        if ($string === '') return false;
        $result = array();
        // find the pairs "name=value"
        $pairs = explode('&', urldecode($string));
        foreach ($pairs as $pair) {
            // use the original parse_str() on each element
            parse_str($pair, $params);

            $result = $this->mergeArray($result, $params);

        }
        return true;
    }

    private function mergeArray($arr1, $arr2)
    {
        foreach ($arr2 as $key => $value) {
            if (is_array($value)) {
                if (isset($arr1[$key]))
                    $arr1[$key] = $this->mergeArray($arr1[$key], $value);
                else
                    $arr1[$key] = $value;
            } else
                $arr1[$key] = $value;
        }
        return $arr1;
    }
}