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
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use Zend\Db\Sql\Where;

class ClientController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $adsConfig = getSM('ads_api')->loadCache(null);
        if ($adsConfig) {
            $this->viewModel->setTemplate('ads/admin/first-new');
            $this->viewModel->setVariables(array(
                'baseType' => $adsConfig,
                'error' => false,
            ));
        } else {
            $this->flashMessenger()->addErrorMessage('ADS_NOT_SET_CONFIG');
            $this->viewModel->setVariables(array(
                'error' => true,
            ));
        }
        $this->viewModel->setTemplate('ads/client/index');
        return $this->viewModel;
    }

    public function listAction()
    {
        $baseType = array();
        $flagViewType = 1;
        $keywordSearch = null;
        $categorySearch = null;
        $baseTypeId = $this->params()->fromRoute('baseType', null); //ads Type Id
        $isRequest = $this->params()->fromRoute('isRequest', 0); //ads Type Id
        $adsConfig = getSM('ads_api')->loadCache($baseTypeId);
        if (isset($adsConfig[$isRequest])) {
            $adsConfig = $adsConfig[$isRequest];
            $sortOptions = array(
                /*'table' => array(
                    'starCount' => t('ADS_STAR_COUNT'),
                    'hits' => t('ADS_HITS'),
                ),*/
                /*'field' => array(
                    /// 'zirbana' => t('REALESTATE_ZIRBANA'),
                ),*/
            );
            $validFilters = array(
                'table' => array(
                    'title',
                    'stateId',
                    'cityId',
                    'keyword',
                    'category',
                    'name',
                    'mobile',
                )
            );
            //endregion

            //region Params,Query,Post
            $page = $this->params()->fromQuery('page', 1);
            $itemCount = $this->params()->fromQuery('per_page', 20);
            // $staticUserId = $this->params()->fromPost('userId', false);
            $sort = $this->params()->fromQuery('sort', false);
            // $onlyItems = $this->params()->fromPost('only-items', false);
            //$route = 'app/real-estate/list';
            $filter = array_merge_recursive($this->params()->fromQuery(), $this->params()->fromPost());


            $showKeyword = false;
            $showCategory = false;
            $showCreateDate = false;
            $showHits = false;
            $advanceConfig = getConfig('ads_advance_config')->varValue;
            if (isset($advanceConfig['showKeyword']) && $advanceConfig['showKeyword'])
                $showKeyword = true;
            if (isset($advanceConfig['showCategory']) && $advanceConfig['showCategory'])
                $showCategory = true;
            if (isset($advanceConfig['showCreateDate']) && $advanceConfig['showCreateDate'])
                $showCreateDate = true;
            if (isset($advanceConfig['showHits']) && $advanceConfig['showHits'])
                $showHits = true;


            if ($baseTypeId) {
                if ($adsConfig['homePage']) {
                    $fields_api = $this->getFieldsApi();
                    $fields_table = $this->getFieldsApi()->init('ads_' . $baseTypeId . '_' . $isRequest);
                    $fields = $this->getFieldsTable()->getByEntityType('ads_' . $baseTypeId . '_' . $isRequest)->toArray();
                    foreach ($fields as $f) {
                        $validFilters['field'][] = $f['fieldMachineName'];
                    }
                    //region Sort
                    $order = array();
                    if ($sort) {
                        $sort = explode('.', $sort);
                        if (isset($sortOptions[@$sort[0]])) {
                            $sort_table = $sort[0];
                            if ($sort_table) {
                                if (isset($sortOptions[$sort_table][@$sort[1]])) {
                                    $sort_column = $sort[1];
                                    $sort_order = (isset($sort[2]) && $sort[2] == 'asc') ? $sort[2] : 'desc';

                                    if ($sort[0] == 'table')
                                        $sort_table = 'tbl_ads';
                                    else
                                        $sort_table = 'f';

                                    $order[] = $sort_table . '.' . $sort_column . ' ' . $sort_order;
                                }
                            }
                        }
                    }
                    //endregion


                    $flagViewType = 2;
                    $sortSecondType = array();
                    $secondType = null;
                    $smallImgConfig = null;
                    // $sortByCreated = 0;
                    $starCount = 7;
                    $baseTypeMachineName = '';

                    if (isset($adsConfig['secondType']))
                        $secondType = $adsConfig['secondType'];
                    if (isset($adsConfig['smallImg']))
                        $smallImgConfig = $adsConfig['smallImg'];
                    /* if (isset($adsConfig['sortByCreated']))
                         $sortByCreated = $adsConfig['sortByCreated'];*/
                    if (isset($adsConfig['starCount']))
                        $starCount = $adsConfig['starCount'];
                    if (isset($adsConfig['baseTypeMachineName']))
                        $baseTypeMachineName = $adsConfig['baseTypeMachineName'];


                    foreach ($adsConfig['homePage'] as $row) {
                        if (isset($row['baseType']) && $row['baseType'] == $baseTypeId)
                            $sortSecondType[] = $row['secondType'];
                    }

                    $showStatusType = 0;
                    $advanceConfig = getConfig('ads_advance_config')->varValue;
                    if (isset($advanceConfig['showStatusType']))
                        $showStatusType = $advanceConfig['showStatusType'];

                    $where = new Where();
                    $keyword = null;

                    $where->equalTo('tbl_ads.regType', $isRequest);
                    $where->equalTo('tbl_ads.editStatus', 0);
                    $where->equalTo('tbl_ads.baseType', $baseTypeId);
                    $where->in('tbl_ads.secondType', $sortSecondType);
                    if (!$showStatusType) {
                        $where->equalTo('tbl_ads.status', 1);
                        $where->greaterThan('tbl_ads.expireDate', time());
                    } else {
                        $where->notEqualTo('tbl_ads.status', 0);
                        $where->notEqualTo('tbl_ads.status', 3);
                    }

                    foreach ($filter as $type => $params) {
                        if (isset($validFilters[$type])) {
                            foreach ($params as $name => $value) {
                                if (in_array($name, $validFilters[$type])) {
                                    if ($name != 'keyword' && $name != 'category') {
                                        $tableName = ($type == 'table') ? 'tbl_ads' : 'f';
                                        if (!is_array($value))
                                            $value = trim($value);
                                        if (is_array($value))
                                            $where->in($tableName . '.' . $name, $value);
                                        elseif (strpos($value, ',') > 0) {
                                            $value = explode(',', $value);
                                            $where->between($tableName . '.' . $name, $value[0], $value[1]);
                                        } elseif (isset($value) && $value) {
                                            if (is_numeric($value)) {
                                                $where->equalTo($tableName . '.' . $name, $value);
                                            } else
                                                $where->like($tableName . '.' . $name, "%" . $value . "%");
                                        }
                                    } else {
                                        switch ($name) {
                                            case 'keyword':
                                                $keywordSearch = $value;
                                                break;
                                            case 'category':
                                                $categorySearch = $value;
                                                break;
                                        }
                                    }

                                }
                            }
                        }
                    }


                    $select = getSM('ads_table')->getAllSort($where, $itemCount, $page, 1, $keywordSearch, $categorySearch, $baseTypeId, $fields_table, $order);
                    $this->viewModel->setTerminal(false);
                    $this->viewModel->setVariables(array(
                        'flagViewType' => $flagViewType,
                        'select' => $select['dataArray'],
                        'paginate' => $select['data'],
                        'baseTypeId' => $baseTypeId,
                        'sortSecondType' => $sortSecondType,
                        'secondType' => $secondType,
                        'smallImgConfig' => $smallImgConfig,
                        'keywords' => $select['keyword'],
                        'category' => $select['category'],
                        'showKeyword' => $showKeyword,
                        'showCategory' => $showCategory,
                        'showCreateDate' => $showCreateDate,
                        'showHits' => $showHits,
                        'starCount' => $starCount,
                        'baseTypeMachineName' => $baseTypeMachineName,
                        'sortOptions' => $sortOptions,
                        'error' => false,
                        'query'=>$this->params()->fromQuery(),
                    ));
                    /* $fields_table = $this->getFieldsApi()->init('ads_directory');
                     $fields = $this->getFieldsTable()->getByEntityType('ads_directory')->toArray();
                     var_dump($fields);
                     die;*/
                }
            } else {
                if (isset($adsConfig['baseType']))
                    $baseType = $adsConfig['baseType'];
                $this->viewModel->setVariables(array(
                    'baseType' => $baseType,
                    'flagViewType' => $flagViewType,
                    //'baseTypeMachineName' => $baseTypeMachineName,
                ));
            }
        } else {
            $this->flashMessenger()->addErrorMessage('ADS_NOT_SET_CONFIG');
            $this->viewModel->setVariables(array(
                'error' => true,
            ));
        }


        $resolver = $this->getEvent()
            ->getApplication()
            ->getServiceManager()
            ->get('Zend\View\Resolver\TemplatePathStack');
        $template = 'ads/client/list-' . $baseTypeId . '-' . $isRequest;
        if ($resolver->resolve($template))
            $this->viewModel->setTemplate($template);
        else
            $this->viewModel->setTemplate('ads/client/list');
        return $this->viewModel;
    }

    public function viewAction()
    {
        $isRequest = 0;
        $adId = $this->params()->fromRoute('adId', null); //ads Id
        $baseTypeRoute = $this->params()->fromRoute('baseType', 0);
        if ($adId) {
            $adSelect = getSM('ads_table')->get($adId);
            if ($adSelect) {
                $isRequest = $adSelect->regType;
                $showStatusType = 0;
                $advanceConfig = getConfig('ads_advance_config')->varValue;
                if (isset($advanceConfig['showStatusType']))
                    $showStatusType = $advanceConfig['showStatusType'];
                $fields_api = $this->getFieldsApi();
                $fields_table = $this->getFieldsApi()->init('ads_' . $adSelect->baseType . '_' . $adSelect->regType);
                $fields = $this->getFieldsTable()->getByEntityType('ads_' . $adSelect->baseType . '_' . $adSelect->regType)->toArray();

                if (current_user()->id != 0 && current_user()->id == $adSelect->userId)
                    $flagShowAllInfo = true;
                else
                    $flagShowAllInfo = getSM('payment_entity_api')->search($adId, 'ads_' . $adSelect->baseType . '_' . $adSelect->regType, current_user()->id);

                if (isset($_COOKIE['ads_view_data'])) {
                    $dataCookie = json_decode($_COOKIE['ads_view_data']);
                    if (in_array($adId, $dataCookie))
                        $flagShowAllInfo = true;
                }

                foreach ($fields as $f) {
                    $validFilters['field'][] = $f['fieldMachineName'];
                }
                $adsConfig = getSM('ads_api')->loadCache($adSelect->baseType);
                if (isset($adsConfig[$isRequest]))
                    $adsConfig = $adsConfig[$isRequest];
                $secondType = null;
                $starCount = 7;
                $baseTypeAmountAllInfo = 0;
                $baseTypeMachineName = null;
                $baseTypeRate = 0;
                if (isset($adsConfig['secondType']))
                    $secondType = $adsConfig['secondType'];
                if (isset($adsConfig['starCount']))
                    $starCount = $adsConfig['starCount'];
                if (isset($adsConfig['baseTypeMachineName']))
                    $baseTypeMachineName = $adsConfig['baseTypeMachineName'];
                if (isset($adsConfig['baseTypeAmountAllInfo']))
                    $baseTypeAmountAllInfo = (int)$adsConfig['baseTypeAmountAllInfo'];
                if (isset($adsConfig['baseTypeRate']))
                    $baseTypeRate = (int)$adsConfig['baseTypeRate'];

                $select = getSM('ads_table')->getAd($adSelect->baseType, $adId, $fields_table);
                $selectImage = getSM('file_table')->getByEntityType('ads_' . $baseTypeRoute . '_' . $isRequest, $adId);
                getSM('ads_table')->update(array('hits' => ++$select['dataArray']->hits), array('id' => $adId));
                $this->viewModel->setVariables(array(
                    'select' => $select['dataArray'],
                    'secondType' => $secondType,
                    'keywords' => $select['keyword'],
                    'category' => $select['category'],
                    'starCount' => $starCount,
                    'fields' => $fields,
                    'baseTypeMachineName' => $baseTypeMachineName,
                    'baseTypeAmountAllInfo' => $baseTypeAmountAllInfo,
                    'flagShowAllInfo' => $flagShowAllInfo,
                    'showStatusType' => $showStatusType,
                    'selectImage' => $selectImage,
                    'baseTypeRate' => $baseTypeRate,
                ));


            } else
                $this->flashMessenger()->addErrorMessage('ADS_NOT_FOUND');
        }
        $resolver = $this->getEvent()
            ->getApplication()
            ->getServiceManager()
            ->get('Zend\View\Resolver\TemplatePathStack');
        $template = 'ads/client/view-' . $baseTypeRoute . '-' . $isRequest;
        if ($resolver->resolve($template))
            $this->viewModel->setTemplate($template);
        else
            $this->viewModel->setTemplate('ads/client/view');
        return $this->viewModel;
    }

    public function searchAction()
    {
        $baseType = $this->params()->fromRoute('baseType', 0);
        $isRequest = $this->params()->fromQuery('isRequest', 0);
        if ($baseType) {
            $resolver = $this->getEvent()
                ->getApplication()
                ->getServiceManager()
                ->get('Zend\View\Resolver\TemplatePathStack');
            $template = 'ads/client/ads-search-' . $baseType . '-' . $isRequest;
            if ($resolver->resolve($template))
                $this->viewModel->setTemplate($template);
        } else
            $this->viewModel->setTemplate('ads/client/search');
        return $this->viewModel;
    }

    public function newValidateAction()
    {
        $params = $this->params()->fromRoute('params', false);
        $paymentId = $this->params()->fromRoute('paymentId', false);

        //the received parameters from payment module is not correct
        if (!$params || !$paymentId) {
            return $this->invalidRequest('app/ad');
        }

        $params = unserialize(base64_decode($params));

        if (!isset($params['id']))
            return $this->invalidRequest('app/ad');

        getSM('ads_table')->update(array('payerStatus' => 1), array('id' => $params['id']));

        $model = getSM('ads_table')->get($params['id']);
        //notify user about successful new Ad
        if ($notifyApi = getNotifyApi()) {
            //region Notify Attendance
            if (isset($model->mail) && has_value($model->mail)) {
                $email = $notifyApi->getEmail();
                $email->to = array($model->mail => $model->name);
                $email->from = Mail::getFrom();
                $email->subject = t('ADS_NEW_SUCCESS');
                $email->entityType = 'ads_' . $model->baseType . '_' . $model->regType;
                $email->queued = 0;
            }

            if (isset($model->mobile) && has_value($model->mobile)) {
                $sms = $notifyApi->getSms();
                $sms->to = $model->mobile;
            }

            $notifyApi->notify('Ads', 'ads_new_validate', array(
                '__AD_CODE__' => $params['id'],
                '__PAYMENT_CODE__' => $paymentId,
                '__SITE_URL__' => App::siteUrl(),
            ));
            //endregion
        }

        $notifyTypeR = null;
        $notifyId = null;
        if (isset($params['notifyTypeR']) && $params['notifyTypeR'] && isset($params['notifyId']) && $params['notifyId']) {
            $notifyTypeR = $params['notifyTypeR'];
            $notifyId = explode(',', $params['notifyId']);
            $select = getSM('ads_table')->getAll(array('id' => $notifyId));
            if ($select) {
                foreach ($select as $row) {
                    if ($notifyApi = getNotifyApi()) {
                        //region Notify Attendance
                        if ($params['notifyTypeR'] == 2 || $params['notifyTypeR'] == 3) {
                            if (isset($row->mail) && has_value($row->mail)) {
                                $email = $notifyApi->getEmail();
                                $email->to = array($row->mail => $row->name);
                                $email->from = Mail::getFrom();
                                $email->subject = t('ADS_LIKE_NEW');
                                $email->entityType = 'ads_' . $row->baseType . '_' . $row->regType;
                                $email->queued = 0;
                            }
                        }
                        if ($params['notifyTypeR'] == 1 || $params['notifyTypeR'] == 3) {
                            if (isset($row->mobile) && has_value($row->mobile)) {
                                $sms = $notifyApi->getSms();
                                $sms->to = $row->mobile;
                            }
                        }
                        $AdUrl = App::siteUrl().url('app/ad/view',array('baseType'=>$model->baseType,'adId'=>$params['id']));
                        $notifyApi->notify('Ads', 'ads_send_like_request', array(
                            '__AD_CODE__' => $params['id'],
                            '__SITE_URL__' => App::siteUrl(),
                            '__AD_URL__'=>$AdUrl,
                        ));
                        //endregion
                    }
                }
            }
        }

        $this->flashMessenger()->addSuccessMessage(t('ADS_SUCCESS_PAYMENT'));

        $this->viewModel->setTemplate('ads/client/new-validate');
        $this->viewModel->setVariables(array(
            'payerCode' => $paymentId,
            'orderCode' => $params['id'],
            'notifyTypeR' => $notifyTypeR,
            'notifyId' => $notifyId,
        ));
        return $this->viewModel;
    }

    public function viewDataValidateAction()
    {
        $params = $this->params()->fromRoute('params', false);
        $paymentId = $this->params()->fromRoute('paymentId', false);

        //the received parameters from payment module is not correct
        if (!$params || !$paymentId) {
            return $this->invalidRequest('app/ad');
        }

        $params = unserialize(base64_decode($params));

        if (!isset($params['id']))
            return $this->invalidRequest('app/ad');

        $model = getSM('ads_table')->get($params['id']);
        if (isset($params['userId']) && $params['userId']) {
            $userModel = getSM('user_table')->get($params['userId']);
            if ($userModel) {
                //notify user about successful view all data Ad
                if ($notifyApi = getNotifyApi()) {
                    //region Notify Attendance
                    if (isset($userModel->email) && has_value($userModel->email)) {
                        $email = $notifyApi->getEmail();
                        $email->to = array($userModel->email => $userModel->username);
                        $email->from = Mail::getFrom();
                        $email->subject = t('ADS_SHOW_ALL_DATA');
                        $email->entityType = 'ads_' . $model->baseType . '_' . $model->regType;
                        $email->queued = 0;
                    }

                    if (isset($userModel->mobile) && has_value($userModel->mobile)) {
                        $sms = $notifyApi->getSms();
                        $sms->to = $userModel->mobile;
                    }

                    $notifyApi->notify('Ads', 'ads_view_data_validate', array(
                        '__NAME__' => $userModel->username,
                        '__AD_CODE__' => $model->id,
                        '__PAYMENT_CODE__' => $paymentId,
                        '__AD_LINK__' => App::siteUrl() . url('app/ad/view', array('adId' => $model->id, 'adTitle' => $model->title)),
                        '__SITE_URL__' => App::siteUrl(),
                    ));
                    //endregion
                }
            }
        } else {
            $config = getConfig('ads_advance_config')->varValue;
            $cookieTime = 300;//1 year , 300 day
            if (isset($config['cookieTime']) && $config['cookieTime'])
                $cookieTime = $config['cookieTime'];

            $dataCookie = array();
            if (isset($_COOKIE['ads_view_data'])) {
                $dataCookie = json_decode($_COOKIE['ads_view_data']);
            }
            $dataCookie[] = $model->id;

            setcookie('ads_view_data', json_encode($dataCookie), time() + ((int)$cookieTime * 86400),'/');

        }

        $this->flashMessenger()->addSuccessMessage(t('ADS_SUCCESS_PAYMENT'));

        return $this->redirect()->toUrl(url('app/ad/view', array('baseType'=>$model->baseType,'adId' => $model->id, 'adTitle' => $model->title)));
    }

    public function baseAction()
    {
        return $this->viewModel->setTemplate('ads/client/base');
    }
}
