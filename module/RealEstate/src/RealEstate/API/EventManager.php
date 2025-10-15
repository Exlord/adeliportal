<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 3:48 PM
 */

namespace RealEstate\API;


use Application\API\App;
use Components\Form\NewBlock;
use Cron\API\Cron;
use Mail\API\Mail;
use Menu\Form\MenuItem;
use RealEstate\Model\RealEstateTable;
use SiteMap\Model\Url;
use Theme\API\Common;
use Zend\EventManager\Event;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\Form\Fieldset;

class EventManager
{
    public function onNewsLetterGetInformation(Event $e)
    {
        $data = $e->getParam('data');

        $data->data[] = array(
            'namespace' => __NAMESPACE__,
            'displayName' => 'Real Estate',
            'desc' => '',
            'apiName' => 'real_estate_api',
        );
    }

    public function onSearch(Event $e)
    {
        $search = $e->getParam('search');

        $pageTable = getSM('real_estate_table');
        $result = $pageTable->systemSearch($search->keyword);
        $regTypeArray = getSM('category_item_table')->getItemsArrayByCatName('estate_reg_type');
        if ($result && $result->count()) {
            foreach ($result as $row) {
                $reg_type = '';
                if (isset($regTypeArray[$row->regType]))
                    $reg_type = t($regTypeArray[$row->regType]);
                $estate_type = $row->estateTypeName;
                $meter = $row->estateArea . ' ' . t('real_estate_Meter');
                $title = App::prepareUrlString($reg_type . ' ' . $estate_type . ' ' . $meter);
                $url = url('app/real-estate/view', array('id' => $row->itemId, 'title' => $title));
                $search->data['Real Estate'][] = Common::Link($title, $url, array('class' => 'search-item real-estate-link'));
            }
        }
    }

    public function onLoadBlockConfigs(Event $e)
    {
        $type = $e->getParam('type');

        $blockTypes = array('latest_real_estate_block', 'real_estate_block', 'statistics_real_estate_block', 'real_estate_search_block');
        if (in_array($type, $blockTypes)) {

            /* @var $form NewBlock */
            $form = $e->getParam('form');
            $dataFieldset = $form->get('data');

            $regTypeArray = getSM('category_item_table')->getItemsArrayByCatName('estate_reg_type');
            switch ($type) {
                case 'latest_real_estate_block':
                    $fiedlset = new Fieldset($type);
                    $dataFieldset->setLabel('Latest RealEstate Settings');
                    $dataFieldset->add($fiedlset);
                    $estate_type = getSM('category_item_table')->getItemsTreeByMachineName('estate_type');
                    foreach ($regTypeArray as $key => $val)
                        $estate_reg_type[$key] = t($val);

                    $fiedlset->add(array(
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'multiple' => 'multiple',
                            'class' => 'select2',
                            'size' => 10,
                        ),
                        'name' => 'estate_type',
                        'options' => array(
                            'disable_inarray_validator' => true,
                            'label' => 'Estate Type',
                            'empty_option' => '-- Select --',
                            'value_options' => $estate_type,
                        ),
                    ));

                    $fiedlset->add(array(
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'multiple' => 'multiple',
                            'class' => 'select2',
                            'size' => 10,
                        ),
                        'name' => 'estate_reg_type',
                        'options' => array(
                            'disable_inarray_validator' => true,
                            'label' => 'Estate Reg Type',
                            'empty_option' => '-- Select --',
                            'value_options' => $estate_reg_type,
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
                        'name' => 'type',
                        'type' => 'Zend\Form\Element\Hidden',
                        'attributes' => array(
                            'value' => 'latest_real_estate',
                        )
                    ));
                    break;
                case 'real_estate_block':
                    $fiedlset = new Fieldset($type);
                    $dataFieldset->setLabel('RealEstate Settings');
                    $dataFieldset->add($fiedlset);
                    $estate_type = getSM('category_item_table')->getItemsTreeByMachineName('estate_type');
                    $estate_reg_type = getSM('category_item_table')->getItemsArrayByCatName('estate_reg_type');

                    $fiedlset->add(array(
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'multiple' => 'multiple',
                            'class' => 'select2',
                            'size' => 10,
                        ),
                        'name' => 'estate_type',
                        'options' => array(
                            'disable_inarray_validator' => true,
                            'label' => 'Estate Type',
                            'empty_option' => '-- Select --',
                            'value_options' => $estate_type,
                        ),
                    ));

                    $fiedlset->add(array(
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'multiple' => 'multiple',
                            'class' => 'select2',
                            'size' => 10,
                        ),
                        'name' => 'estate_reg_type',
                        'options' => array(
                            'disable_inarray_validator' => true,
                            'label' => 'Estate Reg Type',
                            'empty_option' => '-- Select --',
                            'value_options' => $estate_reg_type,
                        ),
                    ));

                    $fiedlset->add(array(
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'class' => 'select2',
                            'size' => 10,
                        ),
                        'name' => 'view_type',
                        'options' => array(
                            'disable_inarray_validator' => true,
                            'label' => 'Type',
                            'value_options' => array(
                                1 => 'All Estate',
                                2 => 'Special Estate',
                            ),
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
                            'value' => 'real_estate',
                        )
                    ));
                    break;
                case 'statistics_real_estate_block':
                    $fiedlset = new Fieldset($type);
                    $dataFieldset->setLabel('RealEstate Statistics Settings');
                    $dataFieldset->add($fiedlset);
                    $estate_type = getSM('category_item_table')->getItemsTreeByMachineName('estate_type');

                    $estate_reg_type = array();
                    foreach ($regTypeArray as $key => $val)
                        $estate_reg_type[$key] = t($val);

                    $fiedlset->add(array(
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'multiple' => 'multiple',
                            'class' => 'select2',
                            'size' => 10,
                        ),
                        'name' => 'estate_type',
                        'options' => array(
                            'disable_inarray_validator' => true,
                            'label' => 'Estate Type',
                            'empty_option' => '-- Select --',
                            'value_options' => $estate_type,
                        ),
                    ));

                    $fiedlset->add(array(
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'multiple' => 'multiple',
                            'class' => 'select2',
                            'size' => 10,
                        ),
                        'name' => 'estate_reg_type',
                        'options' => array(
                            'disable_inarray_validator' => true,
                            'label' => 'Estate Reg Type',
                            'empty_option' => '-- Select --',
                            'value_options' => $estate_reg_type,
                        ),
                    ));

                    $fiedlset->add(array(
                        'type' => 'Zend\Form\Element\Select',
                        'attributes' => array(
                            'class' => 'select2',
                            'size' => 10,
                        ),
                        'name' => 'statistic_type',
                        'options' => array(
                            // 'disable_inarray_validator' => true,
                            'label' => 'REALESTATE_STATISTIC_TYPE',
                            // 'empty_option' => '-- Select --',
                            'value_options' => array(
                                1 => 'RealEstate',
                                2 => 'Real Estate Agent',
                                3 => 'Region',
                            ),
                        ),
                    ));

                    $fiedlset->add(array(
                        'name' => 'type',
                        'type' => 'Zend\Form\Element\Hidden',
                        'attributes' => array(
                            'value' => 'statistics_real_estate',
                        )
                    ));
                    break;
                case 'real_estate_search_block':
                    $fiedlset = new Fieldset($type);
                    $dataFieldset->setLabel('RealEstate Search Settings');
                    $dataFieldset->add($fiedlset);

                    $table = new Fieldset('table');
                    $fiedlset->add($table);
                    $field = new Fieldset('field');
                    $fiedlset->add($field);

                    $estateType = new Select('estateType');
                    $estateType->setLabel('Estate Type');
                    $estateType->setValueOptions(array(
                        '0' => 'None',
                        'select' => 'Select',
                        'multi-select' => 'MultiSelect',
                        'checkbox' => 'Checkbox List',
                        'radio' => 'Radio Buttons'
                    ));
                    $table->add($estateType);

                    $regType = new Select('regType');
                    $regType->setLabel('Register Type');
                    $regType->setValueOptions(array(
                        '0' => 'None',
                        'select' => 'Select',
                        'multi-select' => 'MultiSelect',
                        'checkbox' => 'Checkbox List',
                        'radio' => 'Radio Buttons'
                    ));
                    $table->add($regType);

                    $isRequest = new Select('isRequest');
                    $isRequest->setLabel('Type');
                    $isRequest->setValueOptions(array(
                        '0' => 'None',
                        'select' => 'Select',
                        'multi-select' => 'MultiSelect',
                        'checkbox' => 'Checkbox List',
                        'radio' => 'Radio Buttons'
                    ));
                    $table->add($isRequest);

//                $estateArea = new Select('estateArea');
//                $estateArea->setLabel('Estate Area');
//                $estateArea->setValueOptions(array(
//                    '0' => 'None',
//                    'select' => 'Select',
//                    'slider' => 'Slider'
//                ));
//                $table->add($estateArea);

                    $stateId = new Checkbox('stateId');
                    $stateId->setLabel('State');
                    $table->add($stateId);

                    $cityId = new Checkbox('cityId');
                    $cityId->setLabel('City');
                    $table->add($cityId);

                    $areaId = new Checkbox('areaId');
                    $areaId->setLabel('Region');
                    $table->add($areaId);

                    $parentAreaId = new Checkbox('parentAreaId');
                    $parentAreaId->setLabel('Parent Area Region');
                    $table->add($parentAreaId);

                    $userId = new Checkbox('userId');
                    $userId->setLabel('REALESTATE_AGENT');
                    $table->add($userId);

                    $cityId = new Checkbox('totalPrice');
                    $cityId->setLabel('Price');
                    $table->add($cityId);

                    $cityId = new Checkbox('mortgagePrice');
                    $cityId->setLabel('Mortgage Price');
                    $table->add($cityId);

                    $cityId = new Checkbox('rentalPrice');
                    $cityId->setLabel('Rental Price');
                    $table->add($cityId);

                    $fields = getSM('fields_table')->getByEntityType('real_estate');
                    foreach ($fields as $f) {

                        $name = $f->id . ',' . $f->fieldMachineName;
                        switch ($f->fieldType) {
                            case 'text':
                                $el = new Select($name);
                                $el->setValueOptions(array(
                                    '0' => 'None',
                                    'text' => 'Normal TextBox',
                                    'spinner' => 'Spinner TextBox',
                                    'slider' => 'Slider'
                                ));
                                break;
                            default:
                                $el = new Checkbox($name);
                                break;
                        }

                        $el->setLabel($f->fieldName);
                        $field->add($el);
                    }

                    break;
            }
        }
    }

    public function OnSiteMapGeneration(Event $e)
    {
        $sitemap = $e->getParam('sitemap');
        $config = $e->getParam('config');
        $where = array(
            'tbl_realestate.expire > ?' => time(),
            'tbl_realestate.status' => array('1', '3', '4'),
        );
        $fields_table = getSM()->get('fields_api')->init('real_estate');
        $realEstate = getSM('real_estate_table')->getAll($fields_table, null, $where);
        if ($realEstate) {
            foreach ($realEstate as $row) {
                $url = null;
                $dataArray = getSM('real_estate_table')->getUrl($row);
                if (isset($dataArray['url']))
                    $url = App::siteUrl() . $dataArray['url'];

                if (isset($config['RealEstate']['real-estates']['xml']) && $config['RealEstate']['real-estates']['xml']) {
                    if ($url)
                        $sitemap->addUrl(new Url($url));
                }

                if (isset($config['RealEstate']['real-estates']['html']) && $config['RealEstate']['real-estates']['html']) {
                    if (isset($dataArray['title']))
                        $sitemap->tree['/']['children']['RealEstate']['children']['allRealEstate']['children'][] = Common::Link($dataArray['title'], $url);
                }


            }
            if (isset($config['RealEstate']['real-estates']['html']) && $config['RealEstate']['real-estates']['html']) {
                if (isset($dataArray['title']))
                    $sitemap->tree['/']['children']['RealEstate']['children']['allRealEstate']['data'] = t('REALESTATE_ALL');
            }
        }

        if (isset($config['RealEstate']['estate-types']['html']) && $config['RealEstate']['estate-types']['html']) {
            $categoryEstateTypeArray = getSM('category_item_table')->getItemsForSitemap('estate_type', 0);
            $categoryEstateTypeForView = getSM('real_estate_table')->createLinkItems($categoryEstateTypeArray, 0, 'estate_type');
            $sitemap->tree['/']['children']['RealEstate']['children']['category']['children']['category-estate-type']['data'] = t('Estate Type');
            $sitemap->tree['/']['children']['RealEstate']['children']['category']['children']['category-estate-type']['children'] = $categoryEstateTypeForView;
        }

        if (isset($config['RealEstate']['reg-types']['html']) && $config['RealEstate']['reg-types']['html']) {
            $categoryRegTypeArray = getSM('category_item_table')->getItemsForSitemap('estate_reg_type', 0);
            $categoryRegTypeForView = getSM('real_estate_table')->createLinkItems($categoryRegTypeArray, 0, 'estate_reg_type');
            $sitemap->tree['/']['children']['RealEstate']['children']['category']['children']['category-reg-type']['data'] = t('Register Type');
            $sitemap->tree['/']['children']['RealEstate']['children']['category']['children']['category-reg-type']['children'] = $categoryRegTypeForView;
        }

        if (isset($config['RealEstate']['estate-types']['xml']) && $config['RealEstate']['estate-types']['xml']) {
            $categoryEstateType = getSM('category_item_table')->getItemsByMachineName('estate_type');
            if ($categoryEstateType) {
                $freq = Url::NEVER;
                if (isset($config['RealEstate']['estate-types']['freq']) && $config['RealEstate']['estate-types']['freq'])
                    $freq = $config['RealEstate']['estate-types']['freq'];
                foreach ($categoryEstateType as $item)
                    $sitemap->addUrl(new Url(App::siteUrl() . url('app/real-estate/list', array(), array('query' => array('table' => array('estateId' => $item->id)))), null, null, $freq));

            }
        }

        if (isset($config['RealEstate']['reg-types']['xml']) && $config['RealEstate']['reg-types']['xml']) {
            $categoryRegType = getSM('category_item_table')->getItemsByMachineName('estate_reg_type');
            if ($categoryRegType) {
                $freq = Url::NEVER;
                if (isset($config['RealEstate']['reg-types']['freq']) && $config['RealEstate']['reg-types']['freq'])
                    $freq = $config['RealEstate']['reg-types']['freq'];
                foreach ($categoryRegType as $item)
                    $sitemap->addUrl(new Url(App::siteUrl() . url('app/real-estate/list', array(), array('query' => array('table' => array('regType' => $item->id)))), null, null, $freq));
            }
        }

        if (((isset($config['RealEstate']['real-estates']['html']) && $config['RealEstate']['real-estates']['html'])) ||
            ((isset($config['RealEstate']['estate-types']['html']) && $config['RealEstate']['estate-types']['html'])) ||
            ((isset($config['RealEstate']['reg-types']['html']) && $config['RealEstate']['reg-types']['html']))
        ) {
            $sitemap->tree['/']['children']['RealEstate']['data'] = t('RealEstate');
            if (((isset($config['RealEstate']['estate-types']['html']) && $config['RealEstate']['estate-types']['html'])) ||
                ((isset($config['RealEstate']['reg-types']['html']) && $config['RealEstate']['reg-types']['html']))
            )
                $sitemap->tree['/']['children']['RealEstate']['children']['category']['data'] = t('REALESTATE_CATEGORIES');
        }
    }

    public function onCategoryItemUrlGeneration(Event $e)
    {
        $machineName = $e->getParam('machineName');
        $data = $e->getParam('data');

        if ($machineName == 'estate_type') {
            foreach ($data->data as &$items) {
                $url = url('app/real-estate',
                    array(),
                    array(
                        'query' => array(
                            'filter_data' => array(
                                'filter_estateType' => $items['id']
                            )
                        )
                    )
                );
                $items['url'] = $url;
            }
        }
    }

    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $form->menuTypes['real-estate'] = array(
            'label' => 'RealEstate',
            'note' => 'RealEstate search form',
            'params' => array(array('route' => 'app/real-estate'),),
        );

        $form->menuTypes['new-request'] = array(
            'label' => 'New RealEstate Request',
            'note' => 'New RealEstate Request',
            'params' => array(array('route' => 'app/real-estate/new-request'),),
        );

        $form->menuTypes['new-transfer'] = array(
            'label' => 'New RealEstate Transfer',
            'note' => 'New RealEstate Transfer',
            'params' => array(array('route' => 'app/real-estate/new-transfer'),),
        );

        $form->menuTypes['edit-user'] = array(
            'label' => 'Edit RealEstate',
            'note' => 'Allow guests to edit their own real estates',
            'params' => array(array('route' => 'app/real-estate/edit-user'),),
        );

        $form->menuTypes['agent'] = array(
            'label' => 'Real Estate Agents',
            'note' => 'A list of real estate agents',
            'params' => array(array('route' => 'app/real-estate/agent'),),
        );

        $form->menuTypes['search-by-map'] = array(
            'label' => 'REALESTATE_SEARCH_BY_MAP',
            'note' => 'REALESTATE_SEARCH_BY_MAP_DESC',
            'params' => array(array('route' => 'app/real-estate/search-by-map'),),
        );

        $form->menuTypes['statistic'] = array(
            'label' => 'REALESTATE_STATISTIC',
            'note' => '',
            'params' => array(array('route' => 'app/real-estate/statistic'),),
        );

        $form->menuTypes['app-download'] = array(
            'label' => 'Application Download',
            'note' => 'Link for download real estate application . Folder address is : /clients/melkyab/files/APP/realestate.rar',
            'params' => array(array('route' => 'http://melkyab.org/clients/melkyab/files/APP/realestate.rar'),),
        );
    }

    public function onCronRun(Event $e)
    {
        $last_run = $e->getParam('last_run');
        $start = microtime(true);
        $interval = '+ 1 second';
        //$interval = get your own modules cron interval config
        $last = @$last_run->varValue['RealEstate_last_run'];

        if (Cron::ShouldRun($interval, $last)) {
           //$config = getSM('config_table')->getByVarName('real_estate_config')->varValue;
            if ($notifyApi = getNotifyApi()) {
                $realEstate = getSM('real_estate_table')->getExpired(1);
                foreach ($realEstate as $row) {
                    if (isset($row->ownerEmail) && has_value($row->ownerEmail)) {
                        $email = $notifyApi->getEmail();
                        $email->to = $row->ownerEmail;
                        $email->from = Mail::getFrom();
                        $email->subject = t('REALESTATE_EXPIRE_SOON');
                        $email->entityType = \RealEstate\Module::ENTITY_TYPE;
                        $email->queued = 0;
                    }

                    if (isset($row->ownerMobile) && has_value($row->ownerMobile)) {
                        $sms = $notifyApi->getSms();
                        $sms->to = $row->ownerMobile;
                    }

                    $notifyApi->notify('RealEstate', 'realestate_expire_soon', array(
                        '__RE_CODE__' => $row->id,
                        '__NAME__' => $row->ownerName,
                        '__SITE_URL__' => App::siteUrl(),
                    ));
                }

            }
            //$realEstate = getSM('real_estate_table')->getExpired();
            /*foreach ($realEstate as $row) {
                getSM('real_estate_table')->update(array('status' => 0), array('id' => $row->id));
            }*/
            /*if ($notifyApi = getNotifyApi()) {
                $realEstate = getSM('real_estate_table')->getSpecialExpired(1);
                foreach ($realEstate as $row) {
                    if (isset($row->ownerEmail) && has_value($row->ownerEmail)) {
                        $email = $notifyApi->getEmail();
                        $email->to = $row->ownerEmail;
                        $email->from = Mail::getFrom();
                        $email->subject = t('REALESTATE_EXPIRE_SOON');
                        $email->entityType = \RealEstate\Module::ENTITY_TYPE;
                        $email->queued = 0;
                    }

                    if (isset($row->ownerMobile) && has_value($row->ownerMobile)) {
                        $sms = $notifyApi->getSms();
                        $sms->to = $row->ownerMobile;
                    }

                    $notifyApi->notify('RealEstate', 'realestate_expire_soon', array(
                        '__RE_CODE__' => $row->id,
                        '__NAME__' => $row->ownerName,
                        '__SITE_URL__' => App::siteUrl(),
                    ));
                }
            }*/
            $realEstate = getSM('real_estate_table')->getSpecialExpired();
            foreach ($realEstate as $row) {
                getSM('real_estate_table')->update(array('isSpecial' => 0), array('id' => $row->id));
            }


            /*$realEstate = getSM('real_estate_table')->getShowInfoExpired(1);
            foreach ($realEstate as $row) {
                if ($row->ownerMobile) {
                    if (isset($config->varValue['text4-sms-template'])) {
                        $smsTemplateId = $config->varValue['text4-sms-template'];
                        $msg = App::RenderTemplate($smsTemplateId, array(
                            '__CODE__' => $row->id,
                        ));
                    } else {
                        $msg = t('Ad Show Info (estate) you with code __CODE__ up to 3 days expires.');
                        $msg = str_replace('__CODE__', $row->id, $msg);
                    }
                    $resultSms = getSM('sms_api')->send_sms($row->ownerMobile, $msg);
                }
            }*/
            $realEstate = getSM('real_estate_table')->getSpecialExpired();
            foreach ($realEstate as $row) {
                getSM('real_estate_table')->update(array('showInfo' => 0), array('id' => $row->id));
            }


            db_log_info(sprintf(t('RealEstate expire cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
            $last_run->varValue['RealEstate_last_run'] = time();
        }

    }

    public function onLoadAnalyzerStatisticsData(Event $e)
    {
        $json = $e->getParam('json');
        $month = (int)$e->getParam('month');
        $dateLow = $e->getParam('dateLow');
        $dateHigh = $e->getParam('dateHigh');

        /* @var $table RealEstateTable */
        $table = getSM('real_estate_table');
        $table->swapResultSetPrototype();
        $select = $table->getSql()->select();
        $select->where(array('created >= ?' => $dateLow, 'created < ?' => $dateHigh))
            ->columns(array('created', 'isRequest'));
        $result = $table->selectWith($select);
        $table->swapResultSetPrototype();

        if ($result) {
            $y = $m = $d = null;
            foreach ($result as $row) {
                $date = $row['created'];
                $date = explode(',', date('Y,m,d,H', $date));
                $date = mktime($date[3], 0, 0, $date[1], $date[2], $date[0]);

                if (!isset($json->data[$date]))
                    $json->data[$date] = array('real_estate_request' => 0, 'real_estate_transfer' => 0);
                else {
                    if (!isset($json->data[$date]['real_estate_request']))
                        $json->data[$date]['real_estate_request'] = 0;

                    if (!isset($json->data[$date]['real_estate_transfer']))
                        $json->data[$date]['real_estate_transfer'] = 0;
                }

                $type = $row['isRequest'] == '1' ? 'real_estate_request' : 'real_estate_transfer';
                $json->data[$date][$type] += 1;
            }
        }
        if ($month == 0) {
            $json->series['real_estate_request'] = t('Requested RealEstates');
            $json->series['real_estate_transfer'] = t('Registered RealEstates');
        }
    }
} 