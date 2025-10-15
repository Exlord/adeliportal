<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Ads\Controller;

use Application\API\App;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use File\API\File;
use Localization\API\Date;
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use User\API\User;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Where;
use Zend\View\Model\JsonModel;

class AdminController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $adsConfig = getSM('ads_api')->loadCache(null);
        $this->viewModel->setVariables(array(
            'baseType' => $adsConfig,
            'error' => true,
        ));
        $this->viewModel->setTemplate('ads/admin/index');
        return $this->viewModel;
    }

    public function listAction()
    {
        $baseType = $this->params()->fromRoute('baseType', null);
        if ($baseType) {
            $adsConfig = getSM('ads_api')->loadCache(null);
            $baseTypeName = '';
            if (isset($adsConfig[$baseType]['name']))
                $baseTypeName = $adsConfig[$baseType]['name'];
            $baseTypeIsRequest = 0;
            if (isset($adsConfig[$baseType]['isRequest']))
                $baseTypeIsRequest = $adsConfig[$baseType]['isRequest'];
            $grid = new DataGrid('ads_table');
            getSM('ads_table')->getAdsList($grid->getSelect(), $baseType);
            $grid->route = 'admin/ad/list';
            $grid->routeParams = array('baseType' => $baseType);
            $id = new Column('id', 'Id', array(
                'headerAttr' => array('width' => '70px', 'align' => 'center'),
                'attr' => array('align' => 'center')
            ));
            $grid->setIdCell($id);

           // $title = new Column('title', 'Title');
            $state = new Column('stateTitle', 'State');
            $city = new Column('cityTitle', 'City');
            $name = new Column('name', 'Name');
            $hits = new Column('hits', 'Hits', array(
                'headerAttr' => array('width' => '70px', 'align' => 'center'),
                'attr' => array('align' => 'center')
            ));

            $payerStatus = new Custom('payerStatus', 'Payment', function (Column $col) {
                if ($col->dataRow->payerStatus) {
                    if ($col->dataRow->payerStatus == 1) {
                        $class = 'glyphicon glyphicon-ok text-success';
                        $status = t('ADS_PAYMENT_SUCCESS');
                    }
                    if ($col->dataRow->payerStatus == 2) {
                        $class = 'glyphicon glyphicon-user text-info';
                        $status = t('ADS_NOT_PAYMENT_BUT_PERM');
                    }
                    if ($col->dataRow->payerStatus == 3) {
                        $class = 'glyphicon glyphicon-retweet';
                        $status = t('ADS_NOT_PAYMENT_BUT_DEDUCTED');
                    }
                    if ($col->dataRow->payerStatus == 4) {
                        $class = 'glyphicon glyphicon-gift text-success';
                        $status = t('ADS_NOT_PAYMENT_FREE');
                    }
                } else {
                    $class = 'glyphicon glyphicon-remove text-danger';
                    $status = t('ADS_NOT_PAYMENT');
                }

                return '<span data-tooltip="' . $status . '" class="' . $class . '" ></span>';
            }, array('headerAttr' => array('width' => '50px', 'align' => 'center'), 'attr' => array('align' => 'center')));


            $refStatus = new Custom('roleId', 'ADS_REF_STATUS', function (Column $col) {

                $class = 'glyphicon glyphicon-remove text-danger';
                $status = t('ADS_NOT_REF');
                if ($col->dataRow->userRefId || $col->dataRow->roleId) {
                    $class = 'glyphicon glyphicon-ok text-success';
                    $status = sprintf(t('ADS_REF_BY'), $col->dataRow->displayName);
                }
                return '<span data-tooltip="' . $status . '" class="' . $class . '" ></span>';
            }, array('headerAttr' => array('width' => '50px', 'align' => 'center'), 'attr' => array('align' => 'center')));

            $price = new Custom('finalPrice', t('ADS_PRICE') . ' ' . t(getCurrency()), function (Column $col) {
                if ((int)$col->dataRow->finalPrice > 0)
                    $finalPrice = $col->dataRow->finalPrice;
                else
                    $finalPrice = t('ADS_FREE');
                return $finalPrice;
            }, array('headerAttr' => array('width' => '80px', 'align' => 'center'), 'attr' => array('align' => 'center')));

            $view = new Button('View', function (Button $col) {
                $col->route = 'app/ad/view';
                $col->routeParams['baseType'] = $col->dataRow->baseType;
                $col->routeParams['adId'] = $col->dataRow->id;
                $col->routeParams['adTitle'] = $col->dataRow->title;
                $col->contentAttr['target'][] = '_blank';
                $col->contentAttr['class'][] = 'btn btn-default btn-xs';
                $col->icon = 'glyphicon glyphicon-eye-open';
            }, array(
                'headerAttr' => array(),
                'contentAttr' => array(
                    // 'class' => array('ajax_page_load', 'btn', 'btn-default'),
                    'title' => 'View'
                ),
            ));

            $upgrade = new Button('ADS_UPGRADE', function (Button $col) {
                $col->route = 'admin/ad/upgrade';
                $col->icon = 'glyphicon glyphicon-repeat';
                $col->routeParams = array('id' => $col->dataRow->id);
            }, array(
                'headerAttr' => array('width' => '34px'),
                'attr' => array('align' => 'center'),
                'contentAttr' => array('target' => '_blank', 'class' => array('btn', 'btn-default ajax_page_load'))
            ));

            if (isAllowed(\Ads\Module::ADMIN_AD_LIST_CHANGE_ALL_FIELD)) {
                $status = new Select('status', 'Status',
                    array('0' => t('APP_NOT_CHECKED'), '1' => t('Approved'), '2' => t('ADS_REVOKED'), '3' => t('Not Approved')),
                    array('0' => 'inactive', '1' => 'active', '2' => 'inactive'),
                    array('headerAttr' => array('width' => '50px'))
                );
                $status->route = 'admin/ad/update';
                // $status->routeParams =array('baseType'=>$baseType);
            } else
                $status = new Custom('status', 'Status', function (Column $col) {
                    $html = '';
                    switch ($col->dataRow->status) {
                        case 0 :
                            $html = '<span class="text-danger">' . t('APP_NOT_CHECKED') . '</span>';
                            break;
                        case 1 :
                            $html = '<span class="text-success">' . t('Approved') . '</span>';
                            break;
                        case 2 :
                            $html = '<span class="text-danger">' . t('ADS_REVOKED') . '</span>';
                            break;
                        case 3 :
                            $html = '<span class="text-danger">' . t('Not Approved') . '</span>';
                            break;
                    }
                    return $html;
                }, array('headerAttr' => array('width' => '100px', 'align' => 'center'), 'attr' => array('align' => 'center')));


            $request = new Custom('regType', 'Type', function (Column $col) {
                $html = '';
                $className = 'glyphicon glyphicon-transfer text-primary';
                $text = t('ADS_TRANSFER');
                if($col->dataRow->regType)
                {
                    $className = 'glyphicon glyphicon-search text-info';
                    $text = t('ADS_REQUEST');
                }
                $html = '<span class="'.$className.'" title="'.$text.'" ></span>';
                return $html;
            }, array('headerAttr' => array('width' => '40px', 'align' => 'center'), 'attr' => array('align' => 'center')));

            $ref = new Button('ADS_REF', function (Button $col) {
                $col->route = 'admin/ad/ref/new';
                $col->routeParams['adId'] = $col->dataRow->id;
                $col->icon = 'glyphicon glyphicon-share-alt';
            }, array(
                'headerAttr' => array(),
                'contentAttr' => array(
                    'class' => array('ajax_page_load', 'btn', 'btn-default'),
                    'title' => 'ADS_REF'
                ),
            ));

            $del = new DeleteButton();
            $del->route = 'admin/ad/delete';

            $edit = new EditButton();
            $edit->route = 'admin/ad/edit';
            $edit->contentAttr=array('target'=>'_blank','class'=>'btn btn-default btn-xs');

            $columnsArray[] = $id;
            $columnsArray[] = $name;
            $columnsArray[] = $state;
            $columnsArray[] = $city;
            $columnsArray[] = $hits;
            $columnsArray[] = $price;
            $columnsArray[] = $status;
            $columnsArray[] = $request;
            $columnsArray[] = $refStatus;
            $columnsArray[] = $payerStatus;
            $columnsArray[] = $view;


            if (isAllowed(\Ads\Module::ADMIN_AD_NEW_REF))
                $columnsArray[] = $ref;

            $columnsArray[] = $upgrade;
            $columnsArray[] = $edit;
            $columnsArray[] = $del;

            $requestSelect = array(
                0 => t('ADS_TRANSFER'),
                1 => t('ADS_REQUEST'),
            );
            $groupRequest = new Column('regType', 'Type');
            $groupRequest->selectFilterData = $requestSelect;

            $grid->addColumns($columnsArray);

            $grid->setSelectFilters(array($groupRequest));

            $this->viewModel->setVariables(
                array(
                    'grid' => $grid->render(),
                    'buttons' => $grid->getButtons(),
                    'error' => false,
                    'baseTypeName' => $baseTypeName,
                    'baseTypeIsRequest'=>$baseTypeIsRequest,
                    'baseType'=>$baseType
                ));
        } else {
            $this->viewModel->setVariables(array(
                'error' => true,
            ));
        }
        $this->viewModel->setTemplate('ads/admin/list');
        return $this->viewModel;
    }

    public function newAction($model = null, $imagesFile = array(), $cityList = null)
    {
        ini_set('memory_limit', '128M');
        $baseTypeRoute = $this->params()->fromRoute('baseType', null);
        $isRequestRoute = $this->params()->fromRoute('isRequest', 0);
        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $admin_route = strpos($route, 'admin') > -1;
        $route_prefix = $admin_route ? 'admin' : 'app';
        if ($baseTypeRoute || $model) {
            if ($model) {
                $baseTypeRoute = $model->baseType;
                $isRequestRoute = $model->regType;
            }
            $adsConfig = getSM('ads_api')->loadCache($baseTypeRoute);
            if (isset($adsConfig[$isRequestRoute])) {
                $adsConfig = $adsConfig[$isRequestRoute];

                $adConfig = array();
                $keywordCount = 50;
                $starCountArray = array();
                $isGoogleMap = 0;
                if (isset($adsConfig['starCountArray']))
                    $starCountArray = $adsConfig['starCountArray'];
                if (isset($adsConfig['keywordCount']))
                    $keywordCount = $adsConfig['keywordCount'];
                if (isset($adsConfig['ads']))
                    $adConfig = $adsConfig['ads'];
                if (isset($adsConfig['baseTypeGoogleMap']) && $adsConfig['baseTypeGoogleMap'])
                    $isGoogleMap = $adsConfig['baseTypeGoogleMap'];
                $flagEdit = 0;
                $fieldsId = 0;
                $oldSmallImagePath = null;
                $oldSmallImage = null;
                $selectCat = getSM('category_table')->getAll(array('catMachineName' => 'ads_category_' . $baseTypeRoute))->current();
                if (isset($selectCat->id)) {

                    $baseTypePriceOneSms = 0;
                    $baseTypePriceOneEmail = 0;
                    if (isset($adsConfig['baseTypePriceOneSms']))
                        $baseTypePriceOneSms = $adsConfig['baseTypePriceOneSms'];
                    if (isset($adsConfig['baseTypePriceOneEmail']))
                        $baseTypePriceOneEmail = $adsConfig['baseTypePriceOneEmail'];

                    $flagFormSubmit = 0;
                    $sendRequestTypeArray = null;
                    $fieldsRequestSearch = 0;
                    if (isset($adsConfig['baseTypeRequest']) && $adsConfig['baseTypeRequest'] && !$isRequestRoute) {
                        $configFour = getConfig('ads_four_config_' . $baseTypeRoute . '_1')->varValue;
                        if (isset($configFour['fields_ads']['fieldsAds']))
                            $configFour = $configFour['fields_ads']['fieldsAds'];

                        $fieldsRequestSearch = array();
                        foreach ($configFour as $row) {
                            $fieldsRequestSearch[] = $row['base0'];
                        }
                        $flagFormSubmit = 1;
                        $sendRequestTypeArray[0] = 'ADS_WITHOUT_NOTIFY';
                        if ($baseTypePriceOneSms > -1)
                            $sendRequestTypeArray[1] = t('Sms') . ' , ' . t('ADS_NOTIFY_PRICE') . ' : ';
                        if ($baseTypePriceOneEmail > -1)
                            $sendRequestTypeArray[2] = t('Email') . ' , ' . t('ADS_NOTIFY_PRICE') . ' : ';
                        if (isset($sendRequestTypeArray[1]) && $sendRequestTypeArray[2])
                            $sendRequestTypeArray[3] = t('ADS_EMAIL_SMS') . ' , ' . t('ADS_NOTIFY_PRICE') . ' : ';
                        if (!isset($sendRequestTypeArray[1]) && !isset($sendRequestTypeArray[2])) {
                            $flagFormSubmit = 0;
                            $sendRequestTypeArray = null;
                        }
                    }

                    $state_list = getSM()->get('state_table')->getArray(1);
                    //$city_list = getSM()->get('city_table')->getArray(1);
                    $catArray = getSM('category_item_table')->getItemsTreeByMachineName('ads_category_' . $baseTypeRoute);
                    $city_list = array();
                    if ($cityList)
                        $city_list = $cityList;
                    $stateId = $this->params()->fromPost('stateId', false);
                    if ($stateId)
                        $city_list = getSM()->get('city_table')->getArray($stateId);
                    $form = new \Ads\Form\NewAd($adConfig, $catArray, $starCountArray, $state_list, $city_list, $baseTypeRoute, $isRequestRoute, $route_prefix, $sendRequestTypeArray);
                    $form = prepareForm($form, array('submit-new','cancel'));
                    $countImage = 0;
                    if (!$model) {
                        $model = new \Ads\Model\Ads();
                        $model->hits = 0;
                        $form->setAction(url($route_prefix . '/ad/new', array('baseType' => $baseTypeRoute, 'isRequest' => $isRequestRoute)));
                    } else {
                        $form->setAction(url('admin/ad/edit', array('id' => $model->id)));
                        $flagEdit = 1;
                        foreach ($adConfig as $key => $row)
                            if ($row['baseType'] == $model->baseType && $row['secondType'] == $model->secondType && $row['timeAds'] == $model->time) {
                                $model->adType = $key;
                                $countImage = $row['countImage'];
                            }
                        if ($model->smallImage) {
                            $oldSmallImage = unserialize($model->smallImage);
                            if (isset($oldSmallImage['fPath']))
                                $oldSmallImagePath = $oldSmallImage['fPath'];
                        }
                        $keywords = getSM('entity_relation_table')->getItemsNameArray($model->id, 'ads_keyword_' . $baseTypeRoute);
                        $model->keyword = implode(',', $keywords);
                        $model->catId = getSM('entity_relation_table')->getItemsIdArray($model->id, 'ads_category_' . $baseTypeRoute);
                        $fieldsId = $model->fields['id'];
                    }

                    //load filter fields
                    $filterFields = false;
                    $filterFieldsName = false;
                    $filterFieldsconfig = getConfig('ad_filter_fields_' . $baseTypeRoute . '_' . $isRequestRoute)->varValue;
                    if (isset($filterFieldsconfig['filters']))
                        $filterFields = $filterFieldsconfig['filters'];

                    if ($filterFields) {
                        $selectFieldsconfig = getConfig('ad_select_fields_' . $baseTypeRoute . '_' . $isRequestRoute)->varValue;
                        if (isset($selectFieldsconfig['selectFields'])) {
                            foreach ($selectFieldsconfig['selectFields'] as $key => $val)
                                $filterFieldsName[] = 'fields[' . $val . ']';
                        }
                        if ($filterFieldsName)
                            $filterFieldsName = json_encode($filterFieldsName);

                    }
                    $filterFields = json_encode($filterFields);


                    $form->bind($model);

                    if ($this->request->isPost()) {
                        $post = $this->request->getPost()->toArray();

                        $post = array_merge_recursive($post, $this->request->getFiles()->toArray());

                      //  debug($post);

                        if ($this->isSubmit()) {
                            $form->setData($post);

                            if ($form->isValid()) {
                                $fields = $model->getFields();
                                //debug($fields);
                                if ($flagEdit && $fieldsId)
                                    $fields['id'] = $fieldsId;

                                //barrsi noe agahi va hazine
                                $countUploadImg = 100;
                                if (isset($post['adType'])) {
                                    if (isset($adConfig[$post['adType']])) {
                                        $model->baseType = $adConfig[$post['adType']]['baseType'];
                                        $model->secondType = $adConfig[$post['adType']]['secondType'];
                                        $model->time = $adConfig[$post['adType']]['timeAds'];
                                        $model->regType = $adConfig[$post['adType']]['regType'];
                                        $countUploadImg = $adConfig[$post['adType']]['countImage'];
                                        if (isset($post['starCount'])) {
                                            $model->finalPrice = $adConfig[$post['adType']]['starPrice'] * $post['starCount'];
                                        } else
                                            $model->finalPrice = 0;
                                    }
                                }
                                //end

                                $notifyTypeR = 0;
                                $notifyId = '';
                                if (isset($post['notifyType']) && $post['notifyType']) {
                                    $notifyTypeR = $post['notifyType'];
                                    $adsRequest = App::getSession();
                                    if ($adsRequest->offsetExists('ads_request_notify')) {
                                        $adsRequestNotify = $adsRequest->offsetGet('ads_request_notify');
                                        if (isset($adsRequestNotify['r_id']))
                                            $notifyId = $adsRequestNotify['r_id'];
                                        if (isset($adsRequestNotify['price'][$notifyTypeR]))
                                            $model->finalPrice = $model->finalPrice + $adsRequestNotify['price'][$notifyTypeR];
                                        $adsRequest->offsetUnset('ads_request_notify');
                                    }

                                }

                                $model->createDate = time();
                                if (isset($model->time) && $model->time) {
                                    $expireDate = '+ ' . $model->time . ' month';
                                    $model->expireDate = strtotime($expireDate, time());
                                }

                                //upload image
                                if (isset($post['smallImage']['tmp_name']) && $post['smallImage']['tmp_name'] && !$oldSmallImagePath) {
                                    $smallImagesArray = array();
                                    if (isset($post['smallImage']['name']) && isset($post['smallImage']['tmp_name']) && !empty($post['smallImage']['tmp_name'])) {
                                        $smallImagesArray['tmp_name'] = $post['smallImage']['tmp_name'];
                                        $smallImagesArray['name'] = $post['smallImage']['name'];
                                        $smallImagesArray['fileType'] = $post['smallImage']['type'];
                                        $smallImagesArray['fPath'] = File::MoveUploadedFile($post['smallImage']['tmp_name'], PUBLIC_FILE . '/ads/', $post['smallImage']['name']);
                                        $model->smallImage = serialize($smallImagesArray);
                                    }
                                } elseif ($oldSmallImagePath)
                                    $model->smallImage = serialize($oldSmallImage);
                                else
                                    $model->smallImage = null;
                                //end
                                if (!$flagEdit)
                                    $model->userId = current_user()->id;

                                if (isAllowed(\Ads\Module::ADMIN_AD_NEW_PAYMENT))
                                    $model->payerStatus = 2;
                                else
                                    $model->payerStatus = 0;

                                if ($model->finalPrice == 0)
                                    $model->payerStatus = 4;

                                $model->editStatus = 0;

                                $model->text = strip_tags($model->text);
                                // http(s)://
                                $model->text = preg_replace('|https?://www\.[a-z\.0-9]+|i', '', $model->text);
                                // only www.
                                $model->text = preg_replace('|www\.[a-z\.0-9]+|i', '', $model->text);


                                // register to phoneBook site
                                if (getSM()->has('phoneBook_table')) {
                                    if ($model->mobile && $model->mail && current_user()->id == 0) {
                                        $dataPhoneBook['ID'] = '';
                                        $dataPhoneBook['nameAndFamily'] = $model->name;
                                        $dataPhoneBook['email'] = $model->mail;
                                        $dataPhoneBook['mobile'] = $model->mobile;
                                        $dataPhoneBook['phone'] = '';
                                        $dataPhoneBook['fax'] = '';
                                        $dataPhoneBook['comment'] = t('ADS_NEW');
                                        $dataPhoneBook['date'] = time();
                                        if (!getSM('phoneBook_table')->searchEmail($dataPhoneBook['email']))
                                            getSM('phoneBook_table')->save($dataPhoneBook);
                                    }
                                }
                                // end
                                $passUserMainView = '';
                                $userId = current_user()->id;

                                //register guest
                                $flagRegisterGuest = false;
                                if (current_user()->id == 0 && !empty($model->mail)) {
                                    $userCount = getSM()->get('user_table')->getAll(array('username' => $model->mail))->count();
                                    if ($userCount < 1) {
                                        $passUserMainView = rand(100, 1000000);
                                        $dataArrayUser = array(
                                            'basic' => array(
                                                'password' => $passUserMainView,
                                                'username' => $model->mail,
                                                'email' => $model->mail,
                                                'displayName' => $model->name,
                                            )
                                        );
                                        $userId = User::Save($dataArrayUser);
                                        $flagRegisterGuest = true;
                                    }else
                                    {
                                        //TODO SET USER ID WITH LIKE EMAIL
                                    }
                                }
                                //end

                                if($model->finalPrice==0)
                                    $model->starCount = 0;

                                $model->userId = $userId;
                                if (current_user()->id == 2 || isAllowed(\Ads\Module::ADMIN_AD_NEW_APPROVED)) //just admin
                                    $model->status = 1;
                                $id = $this->getTable()->save($model);
                                $this->getFieldsApi()->init('ads_' . $model->baseType . '_' . $isRequestRoute);
                                $this->getFieldsApi()->save('ads_' . $model->baseType . '_' . $isRequestRoute, $model->id, $fields);


                                if (isset($model->catId)) {
                                    $countCatItem = 0;
                                    if (isset($adConfig[$model->adType]['countCatItem']))
                                        $countCatItem = $adConfig[$model->adType]['countCatItem'];
                                    if (!is_array($model->catId))
                                        $model->catId = array($model->catId);
                                    if ($countCatItem) {
                                        if (count($model->catId) > $countCatItem)
                                            $model->catId = array_slice($model->catId, 0, $countCatItem);
                                        getSM('entity_relation_table')->saveAll($model->id, 'ads_category_' . $baseTypeRoute, $model->catId);
                                    }
                                }
                                if (isset($model->keyword)) {
                                    $keywordsArray2 = array();
                                    $keywordsArray = preg_split("/((\r?\n)|(\r\n?)|(,)|(،))/", $model->keyword);
                                    foreach ($keywordsArray as $key => $val) {
                                        $valKey = trim($val);
                                        if (!empty($valKey)) {
                                            $valKey = strip_tags($valKey);
                                            $keywordsArray2[$valKey] = $key;
                                        }
                                    }
                                    $model->keyword = array_slice($keywordsArray2, 0, $keywordCount);
                                    getSM('category_item_table')->allOperationSave(array_keys($model->keyword), $model->id, 'ads_keyword_' . $baseTypeRoute, 'ads_keyword_' . $baseTypeRoute);
                                }

                                //upload image
                                if (isset($adConfig['countImage']) && $adConfig['countImage'])
                                    $countUploadImg = $adConfig['countImage'];
                                if ($flagEdit && (count($imagesFile) <= $countImage))
                                    $countUploadImg = $countImage - count($imagesFile);
                                $imageValue = $this->request->getFiles()->toArray();

                                if (isset($post['image']['image']) && isset($imageValue['image']['image'])) {
                                    $imageOptions = $post['image']['image'];
                                    $imageArray = $imageValue['image']['image'];
                                    if ($imageArray) {
                                        $counterUpload = 0;
                                        foreach ($imageArray as $key => $row) {
                                            if ($counterUpload < $countUploadImg) {
                                                $files = array();
                                                if (isset($row['imageValue']['name']) && isset($row['imageValue']['tmp_name']) && !empty($row['imageValue']['tmp_name'])) {
                                                    if (isset($imageOptions[$key]['imageTitle']))
                                                        $files[$key]['title'] = $imageOptions[$key]['imageTitle'];
                                                    if (isset($imageOptions[$key]['imageAlt']))
                                                        $files[$key]['alt'] = $imageOptions[$key]['imageAlt'];
                                                    $files[$key]['tmp_name'] = $row['imageValue']['tmp_name'];
                                                    $files[$key]['name'] = $row['imageValue']['name'];
                                                    $files[$key]['fileType'] = $row['imageValue']['type'];
                                                    $this->getFileApi()->save('ads_' . $model->baseType . '_' . $model->regType, $model->id, $files, 100);
                                                }
                                            }
                                            $counterUpload++;
                                        }
                                    }
                                }
                                //end
                                db_log_info("new ad item with id:$id is created");
                                $this->flashMessenger()->addSuccessMessage(sprintf(t('ADS_NEW_SUCCESS_WITH_CODE'),$id));

                                if (current_user()->id != 2) { //bating admin
                                    $this->flashMessenger()->addInfoMessage("ADS_NEW_WILL_BE_APPROVED");
                                }

                                if ($flagRegisterGuest) {
                                    $this->flashMessenger()->addInfoMessage(t('ADS_REGISTER_GUEST'));
                                    $this->flashMessenger()->addInfoMessage(sprintf(t('ADS_INFO_REGISTER_GUEST'), $model->mail, $passUserMainView));
                                }

                                //notify user about successful new Ad
                                if ($notifyApi = getNotifyApi()) {
                                    //region Notify Attendance
                                    if (isset($model->mail) && has_value($model->mail)) {
                                        $email = $notifyApi->getEmail();
                                        $email->to = array($model->mail => $model->name);
                                        $email->from = Mail::getFrom();
                                        $email->subject = t('ADS_NEW_SUCCESS');
                                        $email->entityType = 'ads_' . $model->baseType . '_' . $isRequestRoute;
                                        $email->queued = 0;
                                    }

                                    if (isset($model->mobile) && has_value($model->mobile)) {
                                        $sms = $notifyApi->getSms();
                                        $sms->to = $model->mobile;
                                    }

                                    $notifyApi->notify('Ads', 'ads_new', array(
                                        '__AD_CODE__' => $id,
                                        '__NAME__' => $model->name,
                                        '__SITE_URL__' => App::siteUrl(),
                                        // '__VIEW_LINK__' => App::siteUrl() . url('app/ad/view', array('adId' => $id, 'adTitle' => $model->title)),
                                        '__USERNAME__' => $model->mail,
                                        '__PASSWORD__' => $passUserMainView,
                                    ));
                                    //endregion
                                }

                                if (!isAllowed(\Ads\Module::ADMIN_AD_NEW_PAYMENT) && $model->finalPrice > 0) {
                                    //Payment
                                    $paymentParams = array(
                                        'amount' => $model->finalPrice,
                                        'email' => $model->mail,
                                        'comment' => t('ADS_PAY_FOR_NEW_AD'),
                                        'validate' => array(
                                            'route' => 'app/ad/new-validate',
                                            'params' => array(
                                                'id' => $id,
                                                'entityType' => 'ads_' . $model->baseType . '_' . $isRequestRoute,
                                                'userId' => $model->userId,
                                                'notifyTypeR' => $notifyTypeR,
                                                'notifyId' => $notifyId,
                                            ),
                                        )
                                    );
                                    $paymentParams = serialize($paymentParams);
                                    $paymentParams = base64_encode($paymentParams);
                                    return $this->redirect()->toRoute('app/payment', array(), array('query' => array('routeParams' => $paymentParams)));
                                    //end
                                }

                                if (isAllowed(\Ads\Module::ADMIN_AD_NEW_PAYMENT) || $model->finalPrice==0) {
                                    if ($notifyTypeR && $notifyId) {
                                        $notifyId = explode(',', $notifyId);
                                        $select = getSM('ads_table')->getAll(array('id' => $notifyId));
                                        if ($select) {
                                            foreach ($select as $row) {
                                                if ($notifyApi = getNotifyApi()) {
                                                    //region Notify Attendance
                                                    if ($notifyTypeR == 2 || $notifyTypeR == 3) {
                                                        if (isset($row->mail) && has_value($row->mail)) {
                                                            $email = $notifyApi->getEmail();
                                                            $email->to = array($row->mail => $row->name);
                                                            $email->from = Mail::getFrom();
                                                            $email->subject = t('ADS_LIKE_NEW');
                                                            $email->entityType = 'ads_' . $row->baseType . '_' . $row->regType;
                                                            $email->queued = 0;
                                                        }
                                                    }
                                                    if ($notifyTypeR == 1 || $notifyTypeR == 3) {
                                                        if (isset($row->mobile) && has_value($row->mobile)) {
                                                            $sms = $notifyApi->getSms();
                                                            $sms->to = $row->mobile;
                                                        }
                                                    }
                                                    $AdUrl = App::siteUrl().url('app/ad/view',array('baseType'=>$baseTypeRoute,'adId'=>$id));
                                                    $notifyApi->notify('Ads', 'ads_send_like_request', array(
                                                        '__AD_CODE__' => $id,
                                                        '__SITE_URL__' => App::siteUrl(),
                                                        '__AD_URL__'=>$AdUrl,
                                                    ));
                                                    //endregion
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($flagEdit)
                                    return $this->indexAction();

                                if ($this->isSubmitAndClose())
                                    return $this->redirect()->toRoute($route_prefix . '/ad/new');
                                // elseif ($this->isSubmitAndNew()) {
                                $model = new \Ads\Model\Ads();
                                $form->bind($model);
                                // }
                            } else
                                $this->formHasErrors();

                        } elseif ($this->isCancel()) {
                            return $this->redirect()->toRoute($route_prefix . '/ad/new');
                        }
                    }

                    $resolver = $this->getEvent()
                        ->getApplication()
                        ->getServiceManager()
                        ->get('Zend\View\Resolver\TemplatePathStack');
                    $template = 'ads/admin/new-' . $baseTypeRoute . '-' . $isRequestRoute;
                    if ($resolver->resolve($template))
                        $this->viewModel->setTemplate($template);
                    else
                        $this->viewModel->setTemplate('ads/admin/new');
                    $this->viewModel->setVariables(array(
                        'form' => $form,
                        'error' => false,
                        'adConfig' => $adConfig,
                        'imagesFile' => $imagesFile,
                        'countImage' => $countImage,
                        'flagEdit' => $flagEdit,
                        'oldSmallImagePath' => $oldSmallImagePath,
                        'keywordCount' => $keywordCount,
                        'filterFields' => $filterFields,
                        'filterFieldsName' => $filterFieldsName,
                        'isGoogleMap' => $isGoogleMap,
                        'route_prefix' => $route_prefix,
                        'flagFormSubmit' => $flagFormSubmit,
                        'fieldsRequestSearch' => $fieldsRequestSearch,
                        'baseType' => $baseTypeRoute,
                        'baseTypePriceOneEmail' => $baseTypePriceOneEmail,
                        'baseTypePriceOneSms' => $baseTypePriceOneSms,
                    ));
                    return $this->viewModel;
                } else {
                    $this->flashMessenger()->addErrorMessage(sprintf(t('PS_ALERT_CREATE_CATEGORY'), \ProductShowcase\Module::PS_ENTITY_TYPE));
                    return $this->redirect()->toRoute($route_prefix . '/ad/new');
                }
            }
        } else {
            $adsConfig = getSM('ads_api')->loadCache(null);
            $this->viewModel->setTemplate('ads/admin/first-new');
            if (is_array($adsConfig))
                $this->viewModel->setVariables(array(
                    'baseType' => $adsConfig,
                    'error' => false,
                    'route_prefix' => $route_prefix,
                ));
            else
                $this->viewModel->setVariables(array(
                    'error' => true,
                ));
            return $this->viewModel;
        }
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $model = $this->getTable()->get($id);
            if ($model) {
                $imagesFile = getSM('file_table')->getByEntityType('ads_' . $model->baseType . '_' . $model->regType, $id, true);
                $model->catId = getSM('entity_relation_table')->getItemsIdArray($id, 'ads_category_' . $model->baseType);
                $this->getFieldsApi()->init('ads_' . $model->baseType . '_' . $model->regType);
                $model->fields = $this->getFieldsApi()->getFieldData($id);
                $city_list = getSM()->get('city_table')->getArray($model->stateId);
                return $this->newAction($model, $imagesFile, $city_list);
            }
        }
        return $this->invalidRequest('admin/product-showcase');
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);

            if ($id && $field && has_value($value)) {
                if ($field == 'status') {
                    $model = $this->getTable()->get($id);
                    $updateArray = array(
                        $field => $value
                    );
                    if ($model && ($model->status == 0 || $model->status == 3) && $value == 1) {
                        if (isset($model->time) && $model->time) {
                            $expireDate = '+ ' . $model->time . ' month';
                            $expireDate = strtotime($expireDate, time());
                            $updateArray['expireDate'] = $expireDate;

                            //notify user about Approved new Ad
                            if (($notifyApi = getNotifyApi()) && $value == 1 && $model->status != 1) {
                                //region Notify Attendance
                                if (isset($model->mail) && has_value($model->mail)) {
                                    $email = $notifyApi->getEmail();
                                    $email->to = array($model->mail => $model->name);
                                    $email->from = Mail::getFrom();
                                    $email->subject = t('ADS_APPROVED');
                                    $email->entityType = 'ads_' . $model->baseType . '_' . $model->regType;
                                    $email->queued = 0;
                                }

                                if (isset($model->mobile) && has_value($model->mobile)) {
                                    $sms = $notifyApi->getSms();
                                    $sms->to = $model->mobile;
                                }

                                $notifyApi->notify('Ads', 'ads_approved', array(
                                    '__AD_CODE__' => $id,
                                    '__NAME__' => $model->name,
                                    '__SITE_URL__' => App::siteUrl(),
                                    '__VIEW_LINK__' => App::siteUrl() . url('app/ad/view', array('baseType' => $model->baseType, 'adId' => $id, 'adTitle' => $model->title)),
                                ));
                                //endregion
                            }
                        }
                    }
                    if ($model && ($model->status == 0) && $value == 3) {
                        //notify user about Approved new Ad
                        if (($notifyApi = getNotifyApi()) && $value == 1 && $model->status != 1) {
                            //region Notify Attendance
                            if (isset($model->mail) && has_value($model->mail)) {
                                $email = $notifyApi->getEmail();
                                $email->to = array($model->mail => $model->name);
                                $email->from = Mail::getFrom();
                                $email->subject = t('ADS_NOT_APPROVE');
                                $email->entityType = 'ads_' . $model->baseType . '_' . $model->regType;
                                $email->queued = 0;
                            }

                            if (isset($model->mobile) && has_value($model->mobile)) {
                                $sms = $notifyApi->getSms();
                                $sms->to = $model->mobile;
                            }

                            $notifyApi->notify('Ads', 'ads_not_approved', array(
                                '__AD_CODE__' => $id,
                                '__NAME__' => $model->name,
                                '__SITE_URL__' => App::siteUrl(),
                            ));
                            //endregion
                        }
                    }
                    $this->getTable()->update($updateArray, array('id' => $id));
                    return new JsonModel(array('status' => 1));
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
                $model = $this->getTable()->get($id);
                $this->getTable()->remove($id);
                $this->getFieldsApi()->init('ads_' . $model->baseType);
                $this->getFieldsApi()->remove($id);
                getSM('entity_relation_table')->removeByEntityId($id, 'ads');
                //TODO REMOVE TABLE FILE
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    public function configAction()
    {
        $this->viewModel->setTemplate('ads/admin/config');
        return $this->viewModel;
    }

    public function NewTypeConfigAction()
    {
        $config = getConfig('ads_new_type_config');
        $configVarValue = $config->varValue;
        $lastBaseType = array();
        if (isset($configVarValue['base_type_ads']['baseType']))
            foreach ($configVarValue['base_type_ads']['baseType'] as $row)
                $lastBaseType[$row['idBaseTypeAdsValue']] = $row['baseTypeAdsValue'];

        $form = prepareConfigForm(new \Ads\Form\NewTypeConfig\Config());
        $form->setData($config->varValue);
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());

                    $newBaseType = array();
                    foreach ($config->varValue['base_type_ads']['baseType'] as $row)
                        $newBaseType[$row['idBaseTypeAdsValue']] = $row['baseTypeAdsValue'];

                    $createNewBase = null;
                    foreach ($newBaseType as $key => $val)
                        if (!array_key_exists($key, $lastBaseType))
                            $createNewBase[$key] = $val;

                    foreach ($config->varValue as &$row)
                        if (is_array($row)) {
                            unset($row['add_more_select_option']);
                            foreach ($row as &$row2) {
                                if (is_array($row2))
                                    foreach ($row2 as &$row3)
                                        unset($row3['drop_collection_item']);
                            }
                        }
                    $this->getConfigTable()->save($config);
                    if (is_array($createNewBase)) {
                        foreach ($createNewBase as $key => $val) {
                            if ($key) {
                                $modelCategory = new \Category\Model\Category();
                                $modelCategory->catMachineName = 'ads_category_' . $key;
                                $modelCategory->catName = $val;
                                $modelCategory->catText = t('ADS_CATEGORIES') . ' ' . $val;
                                getSM('category_table')->save($modelCategory);
                                $modelKeyword = new \Category\Model\Category();
                                $modelKeyword->catMachineName = 'ads_keyword_' . $key;
                                $modelKeyword->catName = $val;
                                $modelKeyword->catText = t('ADS_KEYWORDS') . ' ' . $val;
                                getSM('category_table')->save($modelKeyword);
                            }
                        }
                    }
                    getSM('ads_api')->saveCache();
                    db_log_info("Ads First Configs changed");
                    $this->flashMessenger()->addSuccessMessage('ADS_CONFIG_SUCCESS');
                }
            }
        }

        $this->viewModel->setTemplate('ads/admin/new-type-config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function firstConfigAction()
    {
        $flagView = false; //avval bayad noe agahi entekhab shavad
        $baseType = $this->params()->fromRoute('baseType', null);
        $isRequest = $this->params()->fromRoute('isRequest', 0);
        if ($baseType) {
            $flagView = true;
            $configNewType = getConfig('ads_new_type_config')->varValue;
            if (isset($configNewType['base_type_ads']) && $configNewType['base_type_ads']) {
                $config = getConfig('ads_first_config_' . $baseType . '_' . $isRequest);
                $form = prepareConfigForm(new \Ads\Form\FirstConfig\Config());
                $form->setAction(url('admin/ad/config/first-config', array('baseType' => $baseType, 'isRequest' => $isRequest)));
                $form->setData($config->varValue);
                if ($this->request->isPost()) {
                    $post = $this->request->getPost();
                    if (isset($post['buttons']['submit'])) {
                        $form->setData($this->request->getPost());
                        if ($form->isValid()) {
                            $config->setVarValue($form->getData());

                            foreach ($config->varValue as &$row)
                                if (is_array($row)) {
                                    unset($row['add_more_select_option']);
                                    foreach ($row as &$row2) {
                                        if (is_array($row2))
                                            foreach ($row2 as &$row3)
                                                unset($row3['drop_collection_item']);
                                    }
                                }
                            $this->getConfigTable()->save($config);
                            getSM('ads_api')->saveCache();
                            db_log_info("Ads First Configs Witch Type " . $baseType . "_" . $isRequest . "  changed");
                            $this->flashMessenger()->addSuccessMessage('ADS_CONFIG_SUCCESS');
                        }
                    }
                }
                $this->viewModel->setVariables(array('form' => $form, 'flagView' => $flagView, 'error' => false));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }
        } else {
            $adsConfig = getSM('ads_api')->loadCache(null);
            if ($adsConfig) {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => false, 'baseType' => $adsConfig));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }

        }
        $this->viewModel->setTemplate('ads/admin/first-config');
        return $this->viewModel;


    }

    public function secondConfigAction()
    {
        $flagView = false; //avval bayad noe agahi entekhab shavad
        $baseType = $this->params()->fromRoute('baseType', null);
        $isRequest = $this->params()->fromRoute('isRequest', 0);
        if ($baseType) {
            $flagView = true;
            $config_first = getConfig('ads_first_config_' . $baseType . '_' . $isRequest)->varValue;
            if ($config_first && isset($config_first['time_ads']) && isset($config_first['second_type_ads'])) {
                $imagesFile = getSM('file_table')->getByEntityType('ads_second_config_' . $baseType . '_' . $isRequest, $baseType, true);
                $config = getConfig('ads_second_config_' . $baseType . '_' . $isRequest);
                $oldConfig = $config->varValue;
                $form = prepareConfigForm(new \Ads\Form\SecondConfig\Config($baseType, $isRequest));
                $form->setAction(url('admin/ad/config/second-config', array('baseType' => $baseType, 'isRequest' => $isRequest)));
                $form->setData($config->varValue);
                if ($this->request->isPost()) {
                    $imageValue = $this->request->getFiles()->toArray();
                    $post = $this->request->getPost();
                    if (isset($post['buttons']['submit'])) {
                        $form->setData($this->request->getPost());
                        if ($form->isValid()) {
                            $config->setVarValue($form->getData());

                            foreach ($config->varValue as &$row)
                                if (is_array($row)) {
                                    unset($row['add_more_select_option']);
                                    foreach ($row as &$row2) {
                                        if (is_array($row2))
                                            foreach ($row2 as &$row3)
                                                unset($row3['drop_collection_item']);
                                    }
                                }

                            if (isset($imageValue['limited_ads']['limitedAds'])) {
                                foreach ($imageValue['limited_ads']['limitedAds'] as $key => $row) {
                                    //upload image
                                    $files = array();
                                    if (isset($row['image']['name']) && isset($row['image']['tmp_name']) && !empty($row['image']['tmp_name'])) {
                                        $files[$key]['tmp_name'] = $row['image']['tmp_name'];
                                        $files[$key]['name'] = $row['image']['name'];
                                        $files[$key]['fileType'] = $row['image']['type'];
                                        $fPath = $this->getFileApi()->save('ads_second_config_' . $baseType . '_' . $isRequest, $baseType, $files, 100);
                                        if (is_array($fPath))
                                            foreach ($fPath as $val)
                                                if ($val)
                                                    if (isset($config->varValue['limited_ads']['limitedAds'][$key]))
                                                        $config->varValue['limited_ads']['limitedAds'][$key]['image'] = $val;
                                    } else {
                                        if (isset($oldConfig['limited_ads']['limitedAds'][$key]['image']))
                                            $config->varValue['limited_ads']['limitedAds'][$key]['image'] = $oldConfig['limited_ads']['limitedAds'][$key]['image'];
                                    }

                                    //end
                                }
                            }

                            $this->getConfigTable()->save($config);
                            getSM('ads_api')->saveCache();
                            db_log_info("Ads Second Configs Type " . $baseType . "_" . $isRequest . " changed");
                            $this->flashMessenger()->addSuccessMessage('ADS_CONFIG_SUCCESS');
                        }
                    }
                }
                $this->viewModel->setVariables(array('form' => $form, 'error' => false, 'flagView' => $flagView, 'imagesFile' => $imagesFile));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }
        } else {
            $adsConfig = getSM('ads_api')->loadCache(null);
            if ($adsConfig) {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => false, 'baseType' => $adsConfig));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }

        }
        $this->viewModel->setTemplate('ads/admin/second-config');
        return $this->viewModel;
    }

    public function thirdConfigAction()
    {
        $flagView = false; //avval bayad noe agahi entekhab shavad
        $baseType = $this->params()->fromRoute('baseType', null);
        $isRequest = $this->params()->fromRoute('isRequest', 0);
        if ($baseType) {
            $flagView = true;
            $config_second = getConfig('ads_second_config_' . $baseType . '_' . $isRequest)->varValue;
            if ($config_second && isset($config_second['limited_ads'])) {
                $config = getConfig('ads_third_config_' . $baseType . '_' . $isRequest);
                $form = prepareConfigForm(new \Ads\Form\ThirdConfig\Config($baseType, $isRequest));
                $form->setAction(url('admin/ad/config/third-config', array('baseType' => $baseType, 'isRequest' => $isRequest)));
                $form->setData($config->varValue);
                if ($this->request->isPost()) {
                    $post = $this->request->getPost();
                    if (isset($post['buttons']['submit'])) {
                        $form->setData($this->request->getPost());
                        if ($form->isValid()) {
                            $config->setVarValue($form->getData());

                            foreach ($config->varValue as &$row)
                                if (is_array($row)) {
                                    unset($row['add_more_select_option']);
                                    foreach ($row as &$row2) {
                                        if (is_array($row2))
                                            foreach ($row2 as &$row3)
                                                unset($row3['drop_collection_item']);
                                    }
                                }
                            if (isset($config->varValue['sort_ads']['sortAds'])) {
                                $i = 1;
                                $dataSort = array();
                                foreach ($config->varValue['sort_ads']['sortAds'] as $row) {
                                    $dataSort[] = array(
                                        'baseType' => $baseType,
                                        'secondType' => $row['secondType'],
                                        'order' => $i,
                                        'homePage' => $row['isHomePage'],
                                        'isRequest' => $isRequest,
                                    );
                                    $i++;
                                }
                                getSM('ads_order_table')->saveOrder($dataSort);
                            }
                            $this->getConfigTable()->save($config);
                            getSM('ads_api')->saveCache();
                            db_log_info("Ads Third Configs Type " . $baseType . "_" . $isRequest . " changed");
                            $this->flashMessenger()->addSuccessMessage('ADS_CONFIG_SUCCESS');
                        }
                    }
                }
                $this->viewModel->setVariables(array('form' => $form, 'error' => false, 'flagView' => $flagView));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }
        } else {
            $adsConfig = getSM('ads_api')->loadCache(null);
            if ($adsConfig) {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => false, 'baseType' => $adsConfig));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }
        }
        $this->viewModel->setTemplate('ads/admin/third-config');
        return $this->viewModel;
    }

    public function fourConfigAction()
    {
        $flagView = false; //avval bayad noe agahi entekhab shavad
        $baseType = $this->params()->fromRoute('baseType', null);
        $isRequest = $this->params()->fromRoute('isRequest', 0);
        if ($baseType) {
            $flagView = true;
            $configNewType = getConfig('ads_new_type_config')->varValue;
            if (isset($configNewType['base_type_ads']) && $configNewType['base_type_ads']) {
                $config = getConfig('ads_four_config_' . $baseType . '_' . $isRequest);
                $form = prepareConfigForm(new \Ads\Form\FourConfig\Config($baseType, $isRequest));
                $form->setAction(url('admin/ad/config/four-config', array('baseType' => $baseType, 'isRequest' => $isRequest)));
                $form->setData($config->varValue);
                if ($this->request->isPost()) {
                    $post = $this->request->getPost();
                    if (isset($post['buttons']['submit'])) {
                        $form->setData($this->request->getPost());
                        if ($form->isValid()) {
                            $config->setVarValue($form->getData());

                            foreach ($config->varValue as &$row)
                                if (is_array($row)) {
                                    unset($row['add_more_select_option']);
                                    foreach ($row as &$row2) {
                                        if (is_array($row2))
                                            foreach ($row2 as &$row3)
                                                unset($row3['drop_collection_item']);
                                    }
                                }

                            $this->getConfigTable()->save($config);
                            getSM('ads_api')->saveCache();
                            db_log_info("Ads Four Configs Type " . $baseType . "_" . $isRequest . " changed");
                            $this->flashMessenger()->addSuccessMessage('ADS_CONFIG_SUCCESS');
                        }
                    }
                }
                $this->viewModel->setVariables(array('form' => $form, 'error' => false, 'flagView' => $flagView));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }
        } else {
            $adsConfig = getSM('ads_api')->loadCache(null);
            if ($adsConfig) {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => false, 'baseType' => $adsConfig));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }

        }
        $this->viewModel->setTemplate('ads/admin/four-config');
        return $this->viewModel;
    }

    public function advanceConfigAction()
    {
        $config = getConfig('ads_advance_config');
        $form = prepareConfigForm(new \Ads\Form\AdvanceConfig());
        $form->setData($config->varValue);
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getConfigTable()->save($config);
                    getSM('ads_api')->saveCache();
                    db_log_info("Ads Advance Configs changed");
                    $this->flashMessenger()->addSuccessMessage('ADS_CONFIG_SUCCESS');
                }
            }
        }
        $this->viewModel->setVariables(array('form' => $form, 'error' => false));
        $this->viewModel->setTemplate('ads/admin/advance-config');
        return $this->viewModel;
    }

    public function selectFieldsConfigAction()
    {
        $flagView = false; //avval bayad noe agahi entekhab shavad
        $baseType = $this->params()->fromRoute('baseType', null);
        $isRequest = $this->params()->fromRoute('isRequest', 0);
        if ($baseType) {
            $flagView = true;
            if ($adsConfig = getSM('ads_api')->loadCache($baseType)) {
                $config = getConfig('ad_select_fields_' . $baseType . '_' . $isRequest);
                $fields_api = $this->getFieldsApi();
                $fields_table = $this->getFieldsApi()->init('ads_' . $baseType . '_' . $isRequest);
                $fields = $this->getFieldsTable()->getByEntityType('ads_' . $baseType . '_' . $isRequest)->toArray();
                $fields_Array = $this->getFieldsApi()->getArrayFields($fields, 2);
                $form = prepareConfigForm(new \Ads\Form\SelectFields\SelectFields($fields_Array));
                $form->setAction(url('admin/ad/config/select-fields', array('baseType' => $baseType, 'isRequest' => $isRequest)));
                $form->setData($config->varValue);
                if ($this->request->isPost()) {
                    $post = $this->request->getPost();
                    if (isset($post['buttons']['submit'])) {
                        $form->setData($this->request->getPost());
                        if ($form->isValid()) {
                            $config->setVarValue($form->getData());
                            unset($config->varValue['showFields']);
                            $this->getConfigTable()->save($config);
                            db_log_info("Ads Type " . $baseType . "_" . $isRequest . " Select Fields Configs changed");
                            $this->flashMessenger()->addSuccessMessage('ADS_CONFIG_SUCCESS');
                        }
                    }
                }
                $this->viewModel->setVariables(array('form' => $form, 'error' => false, 'flagView' => $flagView));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }
        } else {
            $adsConfig = getSM('ads_api')->loadCache(null);
            if (is_array($adsConfig)) {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => false, 'baseType' => $adsConfig));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }
        }
        $this->viewModel->setTemplate('ads/admin/select-fields-config');
        return $this->viewModel;
    }

    public function filterFieldsConfigAction()
    {
        ini_set('memory_limit', '128M');
        $flagView = false; //avval bayad noe agahi entekhab shavad
        $baseType = $this->params()->fromRoute('baseType', null);
        $isRequest = $this->params()->fromRoute('isRequest', 0);
        if ($baseType) {
            $flagView = true;
            $selected_config = getConfig('ad_select_fields_' . $baseType . '_' . $isRequest)->varValue;
            if ($adsConfig = getSM('ads_api')->loadCache($baseType) && isset($selected_config['selectFields'])) {
                $config = getConfig('ad_filter_fields_' . $baseType . '_' . $isRequest);
                $fields_api = $this->getFieldsApi();
                $fields_table = $this->getFieldsApi()->init('ads_' . $baseType . '_' . $isRequest);
                $fields = $this->getFieldsTable()->getByEntityType('ads_' . $baseType . '_' . $isRequest)->toArray();
                $fields_Array = $this->getFieldsApi()->getArrayFields($fields, 2);
                $dataSelectFields = array();
                $fields_Array_ids = $this->getFieldsApi()->getArrayFields($fields, 3);
                foreach ($selected_config['selectFields'] as $val) {
                    if (isset($fields_Array_ids[$val])) {
                        $dataSelectFields[$fields_Array_ids[$val]] = $val;
                    }
                }
                $selectedFields = $this->getFieldsApi()->getFields(array_keys($dataSelectFields));
                $selectedFieldsArray = array();
                foreach ($selectedFields as $row) {
                    if (array_key_exists($row['fieldMachineName'], $fields_Array))
                        unset($fields_Array[$row['fieldMachineName']]);
                    $f_items = array();
                    if (isset($row['fieldConfigData']['select_field']['keyValuePairs']) && $row['fieldConfigData']['select_field']['keyValuePairs'])
                        foreach ($row['fieldConfigData']['select_field']['keyValuePairs'] as $row2)
                            $f_items[$row2['field_key']] = $row2['field_value'];
                    if (isset($row['fieldConfigData']['radio_field']['keyValuePairs']) && $row['fieldConfigData']['radio_field']['keyValuePairs'])
                        foreach ($row['fieldConfigData']['radio_field']['keyValuePairs'] as $row2)
                            $f_items[$row2['field_key']] = $row2['field_value'];
                    $selectedFieldsArray[$row['fieldMachineName']] = array(
                        'label' => $row['fieldName'],
                        'items' => $f_items,
                    );
                }
                $form = prepareConfigForm(new \Ads\Form\FilterFields\Main($selectedFieldsArray, $fields_Array));
                $form->setAction(url('admin/ad/config/filter-fields', array('baseType' => $baseType, 'isRequest' => $isRequest)));
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
                            $this->getConfigTable()->save($config);
                            db_log_info("Ads Type " . $baseType . " Filter Fields Configs changed");
                            $this->flashMessenger()->addSuccessMessage('ADS_CONFIG_SUCCESS');
                        }
                    }
                }
                $this->viewModel->setVariables(array('form' => $form, 'error' => false, 'flagView' => $flagView));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }
        } else {
            $adsConfig = getSM('ads_api')->loadCache(null);
            if (is_array($adsConfig)) {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => false, 'baseType' => $adsConfig));
            } else {
                $this->viewModel->setVariables(array('flagView' => $flagView, 'error' => true));
            }
        }
        $this->viewModel->setTemplate('ads/admin/filter-fields-config');
        return $this->viewModel;
    }

    public function deleteImgAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost();
            if ($id) {
                $this->getTable()->update(array('smallImage' => null), array('id' => $id));
                return new JsonModel(array(
                    'status' => 1,
                ));
            }
        }
        return new JsonModel(array(
            'status' => 0,
        ));
    }

    public function upgradeAction()
    {
        $id = $this->params()->fromRoute('id');
        if ($id) {
            $select = $this->getTable()->get($id);
            $adsConfig = getSM('ads_api')->loadCache($select->baseType);
            $isRequest = $select->regType;
            if (isset($adsConfig[$isRequest])) {
                $adsConfig = $adsConfig[$isRequest];
                $adConfig = array();
                $starCountArray = array();
                if (isset($adsConfig['starCountArray']))
                    $starCountArray = $adsConfig['starCountArray'];
                if (isset($adsConfig['ads']))
                    $adConfig = $adsConfig['ads'];
                if ($select) {
                    $adRow = 0;
                    foreach ($adConfig as $key => $row)
                        if ($row['baseType'] == $select->baseType && $row['secondType'] == $select->secondType && $row['timeAds'] == $select->time) {
                            $adRow = $key;
                        }
                    $currentAd = $adConfig[$adRow]['baseType_name'] . '*' . $adConfig[$adRow]['secondType_name'] . '*' . $adConfig[$adRow]['timeAds'] . t('ADS_MONTHLY');
                    $timeRemaining = 0;
                    if (time() < $select->expireDate)
                        $timeRemaining = Date::formatInterval($select->expireDate - time());
                    $moneyRemaining = 0;
                    if ($timeRemaining && $select->finalPrice) {
                        $allTime = (int)(($select->expireDate - $select->createDate) / 86400);
                        $partTime = (int)(($select->expireDate - time()) / 86400);
                        $moneyRemaining = (int)(($select->finalPrice * $partTime) / $allTime);
                    }
                    $form = new \Ads\Form\Upgrade($adConfig, $starCountArray);
                    $form->setAttribute('action', url('admin/ad/upgrade', array('id' => $id)));
                    $form = prepareForm($form, array('submit-new'));

                    if ($this->request->isPost()) {
                        $post = $this->request->getPost()->toArray();
                        if ($this->isSubmit()) {
                            $form->setData($post);
                            if ($form->isValid()) {
                                $data = array();
                                if (isset($post['adType'])) {
                                    if (isset($adConfig[$post['adType']])) {
                                        $data['baseType'] = $adConfig[$post['adType']]['baseType'];
                                        $data['secondType'] = $adConfig[$post['adType']]['secondType'];
                                        $data['time'] = $adConfig[$post['adType']]['timeAds'];
                                        if (isset($post['starCount'])) {
                                            $data['finalPrice'] = $adConfig[$post['adType']]['starPrice'] * $post['starCount'];
                                            $data['starCount'] = $post['starCount'];
                                        } else {
                                            $data['finalPrice'] = 0;
                                            $data['starCount'] = 0;
                                        }
                                    }
                                }

                                if ($data['finalPrice'] > 0 && $moneyRemaining > 0) {
                                    $data['finalPrice'] = $data['finalPrice'] - $moneyRemaining;
                                    if ($data['finalPrice'] <= 0)
                                        $data['finalPrice'] = 0;
                                }

                                $data['createDate'] = time();
                                if (isset($data['time']) && $data['time']) {
                                    $expireDate = '+ ' . $data['time'] . ' month';
                                    $data['expireDate'] = strtotime($expireDate, time());
                                }

                                if (isAllowed(\Ads\Module::ADMIN_AD_NEW_PAYMENT))
                                    $data['payerStatus'] = 2;
                                else
                                    $data['payerStatus'] = 0;

                                if (isset($data['finalPrice']) && $data['finalPrice'] == 0)
                                    $data['payerStatus'] = 1;


                                if (current_user()->id == 2) {
                                    $data['status'] = 1;
                                    $data['editStatus'] = 0;
                                } else {
                                    $data['status'] = 0;
                                    $data['editStatus'] = 1;
                                }


                                $id = $this->getTable()->update($data, array('id' => $id));

                                $this->flashMessenger()->addSuccessMessage('ADS_UPDATE_SUCCESS');
                                $this->flashMessenger()->addInfoMessage('ADS_EDIT_FORCE');

                                if (!isAllowed(\Ads\Module::ADMIN_AD_NEW_PAYMENT) && $data['finalPrice'] > 0) {
                                    //Payment
                                    $paymentParams = array(
                                        'amount' => $data['finalPrice'],
                                        'email' => $select->mail,
                                        'comment' => t('ADS_PAY_FOR_UPGRADE_AD'),
                                        'validate' => array(
                                            'route' => 'app/ad/new-validate',
                                            'params' => array(
                                                'id' => $id,
                                                'entityType' => 'ads_' . $data['baseType'] . '_' . $data['secondType'],
                                                'userId' => $select->userId,
                                            ),
                                        )
                                    );
                                    $paymentParams = serialize($paymentParams);
                                    $paymentParams = base64_encode($paymentParams);
                                    return $this->redirect()->toRoute('app/payment', array(), array('query' => array('routeParams' => $paymentParams)));
                                    //end
                                }

                                if ($this->isSubmitAndClose())
                                    return $this->indexAction();
                                elseif ($this->isSubmitAndNew()) {
                                    $model = new \Ads\Model\Ads();
                                    $form->bind($model);
                                }
                            } else
                                $this->formHasErrors();

                        } elseif ($this->isCancel()) {
                            return $this->indexAction();
                        }
                    }


                    $this->viewModel->setTemplate('ads/admin/upgrade');
                    $this->viewModel->setVariables(array(
                        'form' => $form,
                        'currentAd' => $currentAd,
                        'timeRemaining' => $timeRemaining,
                        'moneyRemaining' => $moneyRemaining,
                        'adConfig' => $adConfig));
                    return $this->viewModel;
                }
            }
        }

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

    public function getDataBlockAction()
    {
        $data = $this->params()->fromPost();
        if (isset($data['baseType']) && $data['baseType']) {
            $adsConfig = getSM('ads_api')->loadCache($data['baseType']);
            $secondTypeAdsArray = '';
            if (isset($adsConfig['secondType']))
                foreach ($adsConfig['secondType'] as $key => $val)
                    $secondTypeAdsArray .= '<option value="' . $key . '">' . $val . '</option>';
            $starCountArray = '';
            if (isset($adsConfig['starCountArray']))
                foreach ($adsConfig['starCountArray'] as $key => $val)
                    $starCountArray .= '<option value="' . $key . '">' . $val . '</option>';
            return new JsonModel(array(
                'status' => 1,
                'starCountArray' => $starCountArray,
                'secondType' => $secondTypeAdsArray,
            ));
        }
        return new JsonModel(array(
            'status' => 0
        ));
    }

    public function getTable()
    {
        return getSM('ads_table');
    }

    public function newAdsRefAction()
    {
        $adId = $this->params()->fromRoute('adId', null);
        if ($adId) {
            $flagEdit = false;
            $roleArray = getSM('role_table')->getRolesAdmin();
            $userArray = getSM('user_table')->getUsers(2);
            $form = new \Ads\Form\NewAdsRef($roleArray, $userArray);
            $model = new \Ads\Model\AdsRef();

            /*$model->adId = $adId;
            $model->senderId = current_user()->id;*/
            // prepareForm($form, array('cancel', 'submit-new'));
            $searchAdRef = getSM('ads_ref_table')->searchByAdId($adId);
            if ($searchAdRef) {
                $flagEdit = true;
                $r_array = array();
                $u_array = array();
                foreach ($searchAdRef as $row) {
                    if ($row->roleId)
                        $r_array[] = $row->roleId;
                    if ($row->userId)
                        $u_array[] = $row->userId;
                }
                $model->roleId = $r_array;
                $model->userId = $u_array;
            }
            $form->setAction(url('admin/ad/ref/new', array('adId' => $adId)));
            $form->bind($model);

            if ($this->request->isPost()) {
                $post = $this->request->getPost();
                if ($this->isSubmit()) {
                    $form->setData($post);
                    if ($form->isValid()) {

                        $dataArray = null;
                        foreach ($model->roleId as $role) {
                            $dataArray[] = array(
                                'adId' => $adId,
                                'roleId' => $role,
                                'senderId' => current_user()->id,
                            );
                        }
                        foreach ($model->userId as $user) {
                            $dataArray[] = array(
                                'adId' => $adId,
                                'userId' => $user,
                                'senderId' => current_user()->id,
                            );
                        }

                        if ($flagEdit)
                            getSM('ads_ref_table')->delete(array('adId' => $adId));
                        if (is_array($dataArray)) {
                            getSM('ads_ref_table')->multiSave($dataArray);
                            $this->flashMessenger()->addSuccessMessage('ADS_REF_SUCCESS');
                        } else
                            $this->flashMessenger()->addErrorMessage('ADS_ERROR_SUBMIT');

                        if ($this->isSubmitAndNew()) {
                            $model = new \Ads\Model\AdsRef();
                            $form->bind($model);
                        } else {
                            $adSelect = $this->getTable()->get($adId);
                            if ($adSelect) {
                                return $this->redirect()->toUrl(url('admin/ad/list', array('baseType' => $adSelect->baseType)));
                            } else
                                return $this->indexAction();
                        }
                    } else
                        $this->formHasErrors();

                } elseif ($this->isCancel()) {
                    return $this->indexAction();
                }
            }

            $this->viewModel->setVariables(array('form' => $form));
        }
        $this->viewModel->setTemplate('ads/admin/new-ref');
        return $this->viewModel;
    }

    public function requestSearchCountAction()
    {
        $data = $this->params()->fromPost();
        if (is_array($data) && isset($data['baseType']) && $data['baseType']) {
            if (isset($data['params']) && is_array($data['params'])) {
                $configFour = getConfig('ads_four_config_' . $data['baseType'] . '_1')->varValue;
                $where = new Where();
                $where->equalTo('tbl_ads.baseType', $data['baseType']);
                $where->equalTo('tbl_ads.regType', 1);
                foreach ($configFour['fields_ads']['fieldsAds'] as $row) {
                    if (isset($data['params'][$row['base0']])) {
                        switch ($row['type']) {
                            case 'equalTo' :
                                foreach ($row['base1'] as $val)
                                    $where->equalTo($val, $data['params'][$row['base0']]);
                                break;
                            case 'like' :
                                foreach ($row['base1'] as $val)
                                    $where->like($val, '%' . $data['params'][$row['base0']] . '%');
                                break;
                            case 'or_equalTo' :
                                $whereOrEqualTo = new Where();
                                foreach ($row['base1'] as $val)
                                    $whereOrEqualTo->or->equalTo($val, $data['params'][$row['base0']]);
                                $where->addPredicate($whereOrEqualTo);
                                break;
                            case 'or_like' :
                                $whereOrEqualTo = new Where();
                                foreach ($row['base1'] as $val)
                                    $whereOrEqualTo->or->like($val, $data['params'][$row['base0']]);
                                $where->addPredicate($whereOrEqualTo);
                                break;
                            case 'lessThan' :
                                foreach ($row['base1'] as $val)
                                    $where->lessThan($val, $data['params'][$row['base0']]);
                                break;
                            case 'greaterThan' :
                                foreach ($row['base1'] as $val)
                                    $where->greaterThan($val, $data['params'][$row['base0']]);
                                break;
                        }
                    }
                }
                $fields_table = $this->getFieldsApi()->init('ads_' . $data['baseType'] . '_1');
                $select = $this->getTable()->getLikeRequest($data['baseType'], $where, $fields_table);
                $counter = 0;
                $requestIdArray = '';
                if ($select->count()) {
                    foreach ($select as $row) {
                        $requestIdArray[] = $row->adId;
                        $counter++;
                    }
                    $requestIdArray = implode($requestIdArray);
                }
                $adsConfig = getSM('ads_api')->loadCache($data['baseType']);
                $p_sms = 0;
                $p_email = 0;
                $p_sms_email = 0;
                if (isset($adsConfig[0])) {
                    $adsConfig = $adsConfig[0];
                    $baseTypePriceOneSms = 0;
                    $baseTypePriceOneEmail = 0;
                    if (isset($adsConfig['baseTypePriceOneSms']))
                        $baseTypePriceOneSms = $adsConfig['baseTypePriceOneSms'];
                    if (isset($adsConfig['baseTypePriceOneEmail']))
                        $baseTypePriceOneEmail = $adsConfig['baseTypePriceOneEmail'];

                    if ($baseTypePriceOneSms > -1)
                        $p_sms = $baseTypePriceOneSms * $counter;
                    if ($baseTypePriceOneEmail > -1)
                        $p_email = $baseTypePriceOneEmail * $counter;
                    if ($p_sms && $p_email)
                        $p_sms_email = $p_sms + $p_email;
                }
                $adsRequest = App::getSession();
                if (!$adsRequest->offsetExists('ads_request_notify'))
                    $adsRequest->offsetSet('ads_request_notify', array());
//                $adsRequestNotify = $adsRequest->offsetGet('ads_request_notify');
                $adsRequest->ads_request_notify['r_id'] = $requestIdArray;
                $adsRequest->ads_request_notify['price'] = array(
                    '1' => $p_sms,
                    '2' => $p_email,
                    '3' => $p_sms_email,
                );
                return new JsonModel(array(
                    'status' => 1,
                    'count' => $counter,
                    //'r_id' => $requestIdArray,
                    'p_sms_v' => currencyFormat($p_sms),
                    'p_email_v' => currencyFormat($p_email),
                    'p_sms_email_v' => currencyFormat($p_sms_email),
                    /*'p_sms' => $p_sms,
                    'p_email' => $p_email,
                    'p_sms_email' => $p_sms_email,*/
                ));
            }
        }
        return new JsonModel(array(
            'status' => 0,
        ));
    }

}
