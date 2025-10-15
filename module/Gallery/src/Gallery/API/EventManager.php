<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 1:07 PM
 */

namespace Gallery\API;


use Application\API\App;
use Application\Model\Config;
use Components\API\Block;
use Components\Form\NewBlock;
use Cron\API\Cron;
use Menu\Form\MenuItem;
use Zend\EventManager\Event;
use Zend\Form\Fieldset;

class EventManager
{
    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $form->menuTypes['order-banner'] = array(
            'label' => 'Banner Order',
            'note' => "Link to a page for ordering banners",
            'params' => array(array('route' => 'app/order-banner')),
        );

        $form->menuTypes['gallery'] = array(
            'label' => 'Gallery',
            'note' => "Link to photo gallery page",
            'params' => array(array('route' => 'app/photo-galleries')),
        );

        $form->menuTypes['single-gallery'] = array(
            'label' => 'Single Gallery',
            'note' => 'Link to a single gallery page',
            'data-url' => url('admin/gallery/gallery-page-list'),
            'params' => array(
                array('route' => 'app/photo-gallery'),
                'id',
                'title',
            ),
            'template' => '[id] - [title]',
        );

    }

    public function onCronRun(Config $last_run)
    {
        $start = microtime(true);
        $interval = '+ 1 day';
        $last = @$last_run->varValue['Gallery_last_run'];

        if (Cron::ShouldRun($interval, $last)) {
            $banner = getSM('banner_table')->getExpired(1);
            $config = getSM('config_table')->getByVarName('banner_config')->varValue;
            foreach ($banner as $row) {
                if ($row->mobile) {
                    if (isset($config->varValue['orderBannerExpired'])) {
                        $smsTemplateId = $config->varValue['orderBannerExpired'];
                        $msg = App::RenderTemplate($smsTemplateId, array(
                            '__BANNER_CODE__' => $row->groupId,
                        ));
                    } else {
                        $msg = t('banner with code __BANNER_CODE__ to 3 days expires.');
                        $msg = str_replace('__BANNER_CODE__', $row->groupId, $msg);
                    }
                    $resultSms = getSM('sms_api')->send_sms($row->mobile, $msg);
                }
            }

            $cache_key = 'banner_name_';
            getCache()->clearByPrefix($cache_key);

            db_log_info(sprintf(t('Expired banners cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
            $last_run->varValue['Gallery_last_run'] = time();
        }

    }
    
    public function onLoadBlockConfigs(Event $e)
    {
        /* @var $form NewBlock */
        $form = $e->getParam('form');
        $type = $e->getParam('type');


        if (in_array($type, array('gallery_block', 'banner_block', 'slider_block', 'image_box_block'))) {
            $dataFieldset = $form->get('data');
            $data = array();
            $where = array();
            switch ($type) {
                case 'gallery_block':
                    $data = array('label' => 'Gallery Block Settings', 'desc' => 'Select the Gallery you want to be loaded in this block');
                    $where = array('type' => 'gallery');
                    break;
                case 'banner_block':
                    $data = array('label' => t('Banner Block Settings'), 'desc' => t('Select the Banner you want to be loaded in this block'));
                    $where = array('type' => 'banner');
                    break;
                case 'slider_block':
                    $data = array('label' => 'Slider Block Settings', 'desc' => 'Select the Slider you want to be loaded in this block');
                    $where = array('type' => 'slider');
                    break;
                case 'image_box_block':
                    $data = array('label' => 'Image Box Block Settings', 'desc' => 'Select the Image Box you want to be loaded in this block');
                    $where = array('type' => 'imageBox');
                    break;
            }
            $blockInfo = Block::getBlockInfo($type);
            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel($data['label']);
            $dataFieldset->add($fiedlset);
            $where['status'] = 1;
            $gallery = getSM('gallery_table')->getGroupsArray($where);
            if ($type != 'gallery_block' && $type != 'banner_block')
                $fiedlset->add(array(
                    'name' => 'groupId',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'label' => 'Groups',
                        'value_options' => $gallery,
                        'description' => $data['desc']
                    ),
                    'attributes' => array(
                        'class' => 'select2',
                    )
                ));
            elseif ($type == 'gallery_block')
                $fiedlset->add(array(
                    'type' => 'Zend\Form\Element\Select',
                    'attributes' => array(
                        'multiple' => 'multiple',
                        'size' => 10,
                        'class' => 'select2',
                    ),
                    'name' => 'groupId',
                    'options' => array(
                        'disable_inarray_validator' => true,
                        'label' => 'Groups',
                        // 'empty_option' => '-- Select --',
                        'value_options' => $gallery,
                    ),
                ));
            if ($type == 'banner_block') {
                $fiedlset->add(array(
                    'name' => 'site',
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Where the banner is loaded?',
                        'description' => 'Foe Example : http://azaript.com'
                    ),
                    'attributes' => array(),
                ));
            }
            if ($type == 'slider_block') {
                $fiedlset->add(array(
                    'name' => 'sliderType',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'label' => 'Slider Type',
                        'value_options' => array(
                            1 => 'Nivo Slider',
                            2 => 'Flyout Slider',
                        ),
                    ),
                    'attributes' => array(
                        'class' => 'select2',
                    )
                ));
            }
            $fiedlset->add(array(
                'name' => 'type',
                'type' => 'Zend\Form\Element\Hidden',
                'attributes' => array(
                    'value' => $where['type'],
                )
            ));
        }
    }
} 