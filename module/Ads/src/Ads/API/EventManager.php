<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 11:51 AM
 */

namespace Ads\API;


use Ads\Model\AdsTable;
use Application\API\App;
use Application\API\Widgets;
use Application\Model\Config;
use Components\Form\NewBlock;
use Cron\API\Cron;
use Mail\API\Mail;
use SiteMap\Model\Url;
use Theme\API\Common;
use Zend\EventManager\Event;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;

class EventManager
{
    public function onFieldsEntityTypesLoad(Event $e)
    {
        $config = getSM('ads_api')->loadCache(null);
        $target = $e->getTarget();
        $target->entityTypes[''] = 'ADS_AD';
        if ($config && is_array($config)) {
            foreach ($config as $row) {
                $target->entityTypes['ads_' . $row['id'] . '_0'] = t('ADS_AD') . ' ' . $row['name'];
                if ($row['isRequest'])
                    $target->entityTypes['ads_' . $row['id'] . '_' . $row['isRequest']] = t('ADS_AD') . ' ' . t('ADS_REQUEST') . ' ' . $row['name'];
            }
        }
    }

    public function onComponentsBlockTypesLoad(Event $e)
    {
        $config = getSM('ads_api')->loadCache(null);
        if ($config && is_array($config)) {
            foreach ($config as $row) {
                $e->getTarget()->AddBlockType('adsSearch_' . $row['id'] . '_0', t('ADS_SEARCH_TYPE') . ' ' . $row['name'], 'ads_search_block', $row['name']);
                $e->getTarget()->AddBlockType('adsCategories_' . $row['id'] . '_0', t('ADS_CATEGORIES_TYPE') . ' ' . $row['name'], 'ads_categories_block', $row['name']);
                if ($row['isRequest']) {
                    $e->getTarget()->AddBlockType('adsSearch_' . $row['id'] . '_' . $row['isRequest'], t('ADS_SEARCH_TYPE') . ' ' . t('ADS_REQUEST') . ' ' . $row['name'], 'ads_search_block', t('ADS_REQUEST') . ' ' . $row['name']);
                    $e->getTarget()->AddBlockType('adsCategories_' . $row['id'] . '_' . $row['isRequest'], t('ADS_CATEGORIES_TYPE') . ' ' . t('ADS_REQUEST') . ' ' . $row['name'], 'ads_categories_block', t('ADS_REQUEST') . ' ' . $row['name']);
                }

            }
        }
        //name,label,helper,description
    }

    public function onCategoryItemUrlGeneration(Event $e)
    {
        $machineName = $e->getParam('machineName');
        $data = $e->getParam('data');

        if (strpos($machineName, 'ads_category_') !== false) {
            $baseType = str_replace('ads_category_', '', $machineName);
            $adsConfig = getSM('ads_api')->loadCache($baseType);
            $baseTypeMachineName = '';
            if (isset($adsConfig['baseTypeMachineName'][$baseType]))
                $baseTypeMachineName = $adsConfig['baseTypeMachineName'][$baseType];
            foreach ($data->data as &$items) {
                if (isset($items['countChild']) && $items['countChild'] > 0) {
                    $url = url('app/category-list',
                        array('catId' => $items['catId'], 'catItemId' => $items['id'],)
                    );
                } else {
                    $url = url('app/ad/list', array('baseType' => $baseType, 'baseTitle' => $baseTypeMachineName), array('query' => array('table' => array('category' => $items['title']))));
                }
//                $adCount = getSM('ads_table')->adCountByCatId($items['catId']);
                $items['url'] = $url;
            }
        }
    }

    public function onLoadBlockConfigs(Event $e)
    {
        /* @var $form NewBlock */
        $form = $e->getParam('form');
        $type = $e->getParam('type');
        $dataFieldset = $form->get('data');


        $typeArraySearch = array();
        $typeArrayCategories = array();
        $config = getSM('ads_api')->loadCache(null);
        if ($config && is_array($config)) {
            foreach ($config as $row) {
                $typeArraySearch[] = 'adsSearch_' . $row['id'] . '_0';
                $typeArrayCategories[] = 'adsCategories_' . $row['id'] . '_0';
                if ($row['isRequest']) {
                    $typeArraySearch[] = 'adsSearch_' . $row['id'] . '_' . $row['isRequest'];
                    $typeArrayCategories[] = 'adsCategories_' . $row['id'] . '_' . $row['isRequest'];
                }
            }
        }

        if (in_array($type, $typeArrayCategories)) {
            $offset = strpos($type, '_');
            $type2 = substr($type, $offset + 1, strlen($type));
            $offset2 = strpos($type2, '_');
            $baseType = substr($type, $offset + 1, $offset2);
            $isRequest = substr($type2, $offset2 + 1, strlen($type2));

            $form->extraScripts[] = '/js/geographical.js';

            $country_list = getSM()->get('country_table')->getArray();
            $state_list = getSM()->get('state_table')->getArray(1);

            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel('ADS_CATEGORIES_BLOCK_CONFIG');
            $dataFieldset->add($fiedlset);

            $table = new Fieldset('table');
            $fiedlset->add($table);
            $field = new Fieldset('field');
            $fiedlset->add($field);

            $stateId = new Checkbox('stateId');
            $stateId->setLabel('State');
            $table->add($stateId);

            $countryIdSelect = new Select('countryIdSelect');
            //$countryIdSelect->setLabel('State');
            $countryIdSelect->setValueOptions($country_list);
            $table->add($countryIdSelect);

            $countState = new Text('countState');
            $countState->setLabel('Count');
            $table->add($countState);

            $cityId = new Checkbox('cityId');
            $cityId->setLabel('City');
            $table->add($cityId);

            $stateIdSelect = new Select('stateIdSelect');
            //$stateIdSelect->setLabel('City');
            $stateIdSelect->setValueOptions($state_list);
            $table->add($stateIdSelect);

            $countCity = new Text('countCity');
            $countCity->setLabel('Count');
            $table->add($countCity);

            $fields = getSM('fields_table')->getByEntityType('ads_' . $baseType . '_' . $isRequest);
            foreach ($fields as $f) {

                $name = $f->id . ',' . $f->fieldMachineName;
                switch ($f->fieldType) {
                    case 'text':
                        $el = new Checkbox($name);
                        $el->setAttributes(array(
                            'disabled' => 'disabled',
                        ));
                        break;
                    default:
                        $el = new Checkbox($name);
                        break;
                }

                $el->setLabel($f->fieldName);
                $field->add($el);
            }

            $fiedlset->add(array(
                'name' => 'type',
                'type' => 'Zend\Form\Element\Hidden',
                'attributes' => array(
                    'value' => 'adsCategories_' . $baseType . '_' . $isRequest,
                )
            ));
        }

        if (in_array($type, $typeArraySearch)) {
            $offset = strpos($type, '_');
            $type2 = substr($type, $offset + 1, strlen($type));
            $offset2 = strpos($type2, '_');
            $baseType = substr($type, $offset + 1, $offset2);
            $isRequest = substr($type2, $offset2 + 1, strlen($type2));
            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel('ADS_SEARCH_BLOCK_CONFIG');
            $dataFieldset->add($fiedlset);

            $form->extraScripts[] = '/__/js/filterCounter.js';

            $table = new Fieldset('table');
            $fiedlset->add($table);
            $field = new Fieldset('field');
            $fiedlset->add($field);

            $title = new Checkbox('title');
            $title->setLabel('Title');
            $table->add($title);

            $stateId = new Checkbox('stateId');
            $stateId->setLabel('State');
            $table->add($stateId);

            $cityId = new Checkbox('cityId');
            $cityId->setLabel('City');
            $table->add($cityId);

            $category = new Checkbox('category');
            $category->setLabel('Categories');
            $table->add($category);

            $name = new Checkbox('name');
            $name->setLabel('Name');
            $table->add($name);

            $mobile = new Checkbox('mobile');
            $mobile->setLabel('Mobile');
            $table->add($mobile);

            $address = new Checkbox('address');
            $address->setLabel('Address');
            $table->add($address);

            $fields = getSM('fields_table')->getByEntityType('ads_' . $baseType . '_' . $isRequest);
            foreach ($fields as $f) {
                $flagCounter = false;
                $name = $f->id . ',' . $f->fieldMachineName;
                switch ($f->fieldType) {
                    case 'text':
                        $el = new Select($name);
                        $el->setValueOptions(array(
                            '0' => 'None',
                            'text' => 'Normal TextBox',
                            'spinner' => 'Spinner TextBox',
                            'slider' => 'Slider',
                            'select'=>'Select Field',
                        ));
                        $el->setAttributes(array(
                            'class'=>'f_select'
                        ));
                        $flagCounter = true;
                        break;
                    default:
                        $el = new Checkbox($name);
                        break;
                }

                $el->setLabel($f->fieldName);
                $field->add($el);
                if($flagCounter)
                {
                    $el = new Text($name.'_counter');
                    $el->setAttributes(array(
                        'class'=>'f_counter'
                    ));
                    $el->setLabel('Counter');
                    $field->add($el);
                }
            }

            $fiedlset->add(array(
                'name' => 'type',
                'type' => 'Zend\Form\Element\Hidden',
                'attributes' => array(
                    'value' => 'adsSearch_' . $baseType . '_' . $isRequest,
                )
            ));
        }

        if ($type == 'ads_block') {
            if ($adsConfig = getSM('ads_api')->loadCache(null)) {
                $adsDataArray = array();
                foreach ($adsConfig as $row)
                    $adsDataArray[$row['id']] = $row['name'];
                $form->extraScripts[] = '/__/js/ads-block.js';
                $fiedlset = new Fieldset($type);
                $dataFieldset->setLabel('Ads Settings');
                $dataFieldset->add($fiedlset);
                $baseTypeAdsArray = array();
                $secondTypeAdsArray = array();
                $starCountArray = array();

                $fiedlset->add(array(
                    'type' => 'Zend\Form\Element\Select',
                    'attributes' => array(
                        'class' => 'select2',
                        'size' => 10,
                    ),
                    'name' => 'baseType',
                    'options' => array(
                        'disable_inarray_validator' => true,
                        'label' => 'ADS_BASE_TYPE',
                        'value_options' => $adsDataArray,
                        'empty_option' => '-- Select --',
                    ),
                ));

                $fiedlset->add(array(
                    'type' => 'Zend\Form\Element\Select',
                    'attributes' => array(
                        'class' => 'select2',
                        'size' => 10,
                    ),
                    'name' => 'secondType',
                    'options' => array(
                        'disable_inarray_validator' => true,
                        'label' => 'ADS_SECOND_TYPE',
                        'value_options' => array(),
                        'empty_option' => '-- Select --',
                    ),
                ));

                $fiedlset->add(array(
                    'type' => 'Zend\Form\Element\Select',
                    'attributes' => array(
                        'class' => 'select2',
                        'size' => 10,
                    ),
                    'name' => 'starCount',
                    'options' => array(
                        'disable_inarray_validator' => true,
                        'label' => 'ADS_STAR_COUNT',
                        'value_options' => array(),
                        'empty_option' => '-- Select --',
                    ),
                ));

                $fiedlset->add(array(
                    'type' => 'Zend\Form\Element\Select',
                    'attributes' => array(
                        'class' => 'select2',
                        'size' => 10,
                    ),
                    'name' => 'showImage',
                    'options' => array(
                        'disable_inarray_validator' => true,
                        'label' => 'ADS_SHOW_IMAGE',
                        'value_options' => array(
                            '0' => 'ADS_NO',
                            '1' => 'ADS_YES',
                        ),
                        // 'empty_option' => '-- Select --',
                    ),
                ));

                $fiedlset->add(array(
                    'name' => 'count',
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Count ?',
                        'description' => 'count : 10'
                    ),
                    'attributes' => array(
                        'value' => 5
                    ),
                ));

                $fiedlset->add(array(
                    'name' => 'showLoading',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'label' => 'Page_SHOW_LOADING_ICON',
                        'description' => '',
                        'value_options' => array(
                            '0' => 'No',
                            '1' => 'Yes',
                        ),
                    ),
                    'attributes' => array(
                        'class' => 'select2',
                    )
                ));

                $fiedlset->add(array(
                    'name' => 'imageWidth',
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Width',
                        'description' => 'Vertical:image/container width<br/>Horizontal:image width'
                    ),
                    'attributes' => array(),
                ));

                $fiedlset->add(array(
                    'name' => 'imageHeight',
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Height',
                        'description' => 'Vertical:image height<br/>Horizontal:image/container height'
                    ),
                    'attributes' => array(),
                ));

                $fiedlset->add(array(
                    'name' => 'textLength',
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Page_TEXT_LENGTH',
                        'description' => 'Page_TEXT_LENGTH_DESC',
                    ),
                    'attributes' => array(
                        'class' => 'spinner',
                        'data-min' => 0,
                        'data-max' => 1000,
                        'data-step' => 10,
                        'value' => 0
                    )
                ));

                $slider = new Fieldset('slider');
                $slider->setLabel('SLIDER_SITTINGS');
                $slider->add(array(
                    'name' => 'orientation',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'label' => 'Page_SLIDE_TYPE',
                        'description' => 'Page_SLIDE_TYPE_DESC',
                        'value_options' => array(
                            'vertical' => 'Vertical',
                            'horizontal' => 'Horizontal',
                        ),
                    ),
                    'attributes' => array(
                        'class' => 'select2',
                    )
                ));
                $slider->add(array(
                    'name' => 'direction',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'label' => 'Page_SLIDER_DIRECTION',
                        'description' => '',
                        'value_options' => array(
                            'right' => 'Right',
                            'left' => 'Left',
                            'top' => 'Top',
                            'bottom' => 'Bottom',
                        ),
                    ),
                    'attributes' => array(
                        'class' => 'select2',
                    )
                ));
                $slider->add(array(
                    'name' => 'autoscroll',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'label' => 'Page_AUTO_SCROLL',
                        'description' => '',
                        'value_options' => array(
                            'yes' => 'Yes',
                            'no' => 'No',
                        ),
                    ),
                    'attributes' => array(
                        'class' => 'select2',
                    )
                ));
                $slider->add(array(
                    'name' => 'interval',
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Page_AUTO_SCROLL_DELAY',
                        'description' => 'Page_AUTO_SCROLL_DELAY_DESC',
                    ),
                ));
                $slider->add(array(
                    'name' => 'speed',
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Page_ANIMATION_SPEED',
                        'description' => 'how long should each slide animation take in milliseconds (default:500)',
                    ),
                ));
                $slider->add(array(
                    'name' => 'directional_nav',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'label' => 'Page_STATUS_DIRECTIONAL_NAV',
                        'description' => '',
                        'value_options' => array(
                            '1' => 'Show',
                            '2' => 'Hide'
                        )
                    ),
                    'attributes' => array(
                        'class' => 'select2'
                    )
                ));
                $slider->add(array(
                    'name' => 'visible_count',
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Page_VISIBLE_COUNT',
                        'description' => 'Page_VISIBLE_COUNT_DESC',
                    ),
                    'attributes' => array(
                        'value' => 1
                    )
                ));
                $slider->add(array(
                    'name' => 'slide_count',
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Page_SLIDE_COUNT',
                        'description' => 'Page_SLIDE_COUNT_DESC',
                    ),
                    'attributes' => array(
                        'value' => 1,

                    )
                ));

                $fiedlset->add($slider);


                $fiedlset->add(array(
                    'name' => 'type',
                    'type' => 'Zend\Form\Element\Hidden',
                    'attributes' => array(
                        'value' => 'ads_block',
                    )
                ));
                $fiedlset->add(array(
                    'name' => 'url',
                    'type' => 'Zend\Form\Element\Hidden',
                    'attributes' => array(
                        'value' => url('admin/ad/get-data-block'),
                    )
                ));
            } else {
                $this->flashMessenger()->addErrorMessage(t('ADS_NOT_SET_CONFIG'));
            }
        }

    }

    public function OnSiteMapGeneration(Event $e)
    {
        $sitemap = $e->getParam('sitemap');
        $config = $e->getParam('config');

        /* @var $adsTable AdsTable */
        $adsTable = getSM()->get('ads_table');
        $categoryItemTable = getSM()->get('category_item_table');
        $selectAds = $adsTable->getAllTranslated(array('status' => array(1, 2)));
        $baseTypeArray = getSM('ads_api')->loadCache(null);

        if ($selectAds) {
            foreach ($selectAds as $ad) {
                $urlTitle = 'ad';
                if ($ad->title)
                    $urlTitle = $ad->title;
                elseif (isset($baseTypeArray[$ad->baseType]['name']))
                    $urlTitle = $baseTypeArray[$ad->baseType]['name'];


                $url = App::siteUrl() . $adsTable->getUrl($ad, $ad->baseType, $urlTitle);

                if (isset($config['Ads']['ads']['html']) && $config['Ads']['ads']['html'])
                    $sitemap->tree['/']['children']['Ads']['children'][$ad->baseType]['children']['all']['children'][] = Common::Link($urlTitle, $url);

                if (isset($config['Ads']['ads']['xml']) && $config['Ads']['ads']['xml']) {
                    $freq = Url::NEVER;
                    if (isset($config['Ads']['ads']['freq']) && $config['Ads']['ads']['freq'])
                        $freq = $config['Ads']['ads']['freq'];
                    $sitemap->addUrl(new Url($url, null, null, $freq));
                }

            }
        }

        $sitemap->tree['/']['children']['Ads']['data'] = Common::Link(t('ADS_AD'), url('app/ad'));

        foreach ($baseTypeArray as $row) {
            $val = $row['name'];
            $key = $row['id'];
            if (isset($config['Ads']['ads']['html']) && $config['Ads']['ads']['html']) {
                $sitemap->tree['/']['children']['Ads']['children'][$key]['data'] = Common::Link($val, url('app/ad/list', array('baseType' => $key, 'baseTitle' => $val)));
                $sitemap->tree['/']['children']['Ads']['children'][$key]['children']['all']['data'] = Common::Link(t('ADS_ALL_IN_TYPE'), url('app/ad/list', array('baseType' => $key, 'baseTitle' => $val)));
                $categoryBaseType = getSM('category_table')->getByMachineName('ads_category_' . $key);
                if (isset($categoryBaseType->id)) {
                    if (isset($config['Ads']['ads-categories']['html']) && $config['Ads']['ads-categories']['html'])
                        $sitemap->tree['/']['children']['Ads']['children'][$key]['children']['category']['data'] = Common::Link(t('ADS_CATEGORIES'), url('app/category-list', array('catId' => $categoryBaseType->id)));
                    $categoryItem = $categoryItemTable->getItemsTreeByMachineName('ads_category_' . $key);
                    if ($categoryItem)
                        foreach ($categoryItem as $catName) {
                            $link = url('app/ad/list', array('baseType' => $key, 'baseTitle' => $val), array('query' => array('table' => array('category' => $catName))));
                            $url = App::siteUrl() . $link;
                            if (isset($config['Ads']['ads-categories']['xml']) && $config['Ads']['ads-categories']['xml']) {
                                $freq = Url::NEVER;
                                if (isset($config['Ads']['ads-categories']['freq']) && $config['Ads']['ads-categories']['freq'])
                                    $freq = $config['Ads']['ads-categories']['freq'];
                                $sitemap->addUrl(new Url($url, null, null, $freq));
                            }

                            if (isset($config['Ads']['ads-categories']['html']) && $config['Ads']['ads-categories']['html'])
                                $sitemap->tree['/']['children']['Ads']['children'][$key]['children']['category']['children'][] = Common::Link($catName, $url);
                        }
                } else
                    $sitemap->tree['/']['children']['Ads']['children'][$key]['children']['category']['data'] = t('ADS_CATEGORIES');

                $keywordBaseType = getSM('category_table')->getByMachineName('ads_keyword_' . $key);
                if (isset($keywordBaseType->id)) {
                    if (isset($config['Ads']['ads_keywords']['html']) && $config['Ads']['ads_keywords']['html'])
                        $sitemap->tree['/']['children']['Ads']['children'][$key]['children']['keyword']['data'] = Common::Link(t('ADS_KEYWORDS'), url('app/ad/list', array('baseType' => $key, 'baseTitle' => $val), array('query' => array('table' => array('keyword' => '')))));
                    $categoryItem = $categoryItemTable->getItemsTreeByMachineName('ads_keyword_' . $key);
                    if ($categoryItem)
                        foreach ($categoryItem as $catName) {
                            $link = url('app/ad/list', array('baseType' => $key, 'baseTitle' => $val), array('query' => array('table' => array('keyword' => $catName))));
                            $url = App::siteUrl() . $link;
                            if (isset($config['Ads']['ads_keywords']['xml']) && $config['Ads']['ads_keywords']['xml']) {
                                $freq = Url::NEVER;
                                if (isset($config['Ads']['ads_keywords']['freq']) && $config['Ads']['ads_keywords']['freq'])
                                    $freq = $config['Ads']['ads_keywords']['freq'];
                                $sitemap->addUrl(new Url($url, null, null, $freq));
                            }

                            if (isset($config['Ads']['ads_keywords']['html']) && $config['Ads']['ads_keywords']['html'])
                                $sitemap->tree['/']['children']['Ads']['children'][$key]['children']['keyword']['children'][] = Common::Link($catName, $url);
                        }
                } else
                    $sitemap->tree['/']['children']['Ads']['children'][$key]['children']['keyword']['data'] = t('ADS_KEYWORDS');


            }
        }
    }

    public function onCronRun(Config $last_run)
    {
        $start = microtime(true);
        $interval = '+ 1 second';
        //$interval = get your own modules cron interval config
        $last = @$last_run->varValue['Ads_last_run'];

        if (Cron::ShouldRun($interval, $last)) { //TODO Convert to notify
            $ads = getSM('ads_table')->getExpired(1);
            foreach ($ads as $model) {
                //notify user about successful new Ad
                if ($notifyApi = getNotifyApi()) {
                    //region Notify Attendance
                    if (isset($model->mail) && has_value($model->mail)) {
                        $email = $notifyApi->getEmail();
                        $email->to = array($model->mail => getUserDisplayName($model->name));
                        $email->from = Mail::getFrom();
                        $email->subject = t('ADS_WILL_EXPIRE');
                        $email->entityType = 'ads_' . $model->baseType . '_' . $model->regType;
                        $email->queued = 0;
                    }

                    if (isset($model->mobile) && has_value($model->mobile)) {
                        $sms = $notifyApi->getSms();
                        $sms->to = $model->mobile;
                    }

                    $notifyApi->notify('Ads', 'ads_will_expire', array(
                        '__AD_CODE__' => $model->id,
                        '__NAME__' => $model->name,
                        '__SITE_URL__' => App::siteUrl(),
                        '__VIEW_LINK__' => App::siteUrl() . url('app/ad/view', array('adId' => $model->id, 'adTitle' => $model->title)),
                        '__ADMIN_LINK__' => App::siteUrl() . url('app/user/login'),
                    ));
                    //endregion
                }
            }
            $adsExpire = getSM('ads_table')->getExpired();
            foreach ($adsExpire as $model) {
                getSM('ads_table')->update(array('status' => 2), array('id' => $model->id));
                //notify user about successful new Ad
                if ($notifyApi = getNotifyApi()) {
                    //region Notify Attendance
                    if (isset($model->mail) && has_value($model->mail)) {
                        $email = $notifyApi->getEmail();
                        $email->to = array($model->mail => getUserDisplayName($model->name));
                        $email->from = Mail::getFrom();
                        $email->subject = t('ADS_EXPIRED_AD');
                        $email->entityType = 'ads_' . $model->baseType . '_' . $model->regType;
                        $email->queued = 0;
                    }

                    if (isset($model->mobile) && has_value($model->mobile)) {
                        $sms = $notifyApi->getSms();
                        $sms->to = $model->mobile;
                    }

                    $notifyApi->notify('Ads', 'ads_expired', array(
                        '__AD_CODE__' => $model->id,
                        '__NAME__' => $model->name,
                        '__SITE_URL__' => App::siteUrl(),
                        '__VIEW_LINK__' => App::siteUrl() . url('app/ad/view', array('adId' => $model->id, 'adTitle' => $model->title)),
                        '__ADMIN_LINK__' => App::siteUrl() . url('app/user/login'),
                    ));
                    //endregion
                }
            }
            db_log_info(sprintf(t('Ads expire cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
            $last_run->varValue['Ads_last_run'] = time();
        }
    }

    public function onDashboardLoad(Event $e)
    {
        /* @var $widget Widgets */
        $widget = $e->getTarget();

        $data = $widget->getAction('Ads\Controller\Admin', 'index');
        $widget->data[] = $data;

    }
} 