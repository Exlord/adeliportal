<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 1:49 PM
 */

namespace Menu\API;


use Application\API\App;
use Components\API\Block;
use Components\Form\NewBlock;
use Menu\Form\MenuItem;
use Menu\Model\MenuTable;
use SiteMap\Model\Url;
use Theme\API\Common;
use Zend\EventManager\Event;
use Zend\Form\Fieldset;

class EventManager
{
    public function OnSiteMapGeneration(Event $e)
    {
        $sitemap = $e->getParam('sitemap');
        $config = $e->getParam('config');

        /* @var $menuTable MenuTable */
        $menuItemTable = getSM()->get('menu_item_table');
        $menuItemParents = $menuItemTable->getMenuItemForSitemap();
        $items = array();
        $menuItemTable->sortMenuItemForSiteMap($menuItemParents, 0, $items);
        if ($items) {
            if (isset($config['Menu']['menu-items']['html']) && $config['Menu']['menu-items']['html'])
                $sitemap->tree['/']['children']['Menu']['children'] = $items;
        }
        if (isset($config['Menu']['menu-items']['xml']) && $config['Menu']['menu-items']['xml'])
            if ($menuItemParents) {
                foreach ($menuItemParents as $row)
                    if (isset($row->itemUrlTypeParams)) {
                        /* @var $api BaseAPI */
                        $api = 'Menu\API\Menu';
                        if (isset($row->itemUrlTypeParams[$row->itemUrlType]['api']))
                            $api = $row->itemUrlTypeParams[$row->itemUrlType]['api'];
                        $params = unserialize($row->itemUrlTypeParams);
                        $page = $api::makeMenuUrl($params);
                        // var_dump($page);
                        $label = $row->itemName;
                        $link = Common::Link($label, App::siteUrl() . $page->getHref());
                        $url = $link;
                        $sitemap->addUrl(new Url($url));
                    }
            }
        if (isset($config['Menu']['menu-items']['html']) && $config['Menu']['menu-items']['html'])
            $sitemap->tree['/']['children']['Menu']['data'] = t('Menu');

    }

    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $externalUriDescription = "exp : http://ipt24.ir, be advised that external urls should have the 'http://' part otherwise they will be considered a internal url";
        $form->menuTypes['externalUrl'] = array(
            'label' => 'External Url',
            'note' => $externalUriDescription,
            'fields' => array(
                array(
                    'name' => 'url',
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Url',
                    ),
                    'attributes' => array(
                        'class' => 'left-align',
                        'size' => 50
                    )
                )
            ),
        );

        $form->menuTypes['systemUrl'] = array(
            'label' => 'System Url',
            'note' => "A url relative to site's full url. exp : /fa/front-page",
            'fields' => array(
                array(
                    'name' => 'uri',
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Url',
                    ),
                    'attributes' => array(
                        'class' => 'left-align',
                        'size' => 50
                    )
                )
            ),
        );

//        $externalUri = new Fieldset('externalUrl', array('label' => 'External Url'));
//        $externalUri->setAttribute('id', 'externalUrl');
//        $externalUri->add(array(
//            'name' => 'url',
//            'type' => 'Zend\Form\Element\Text',
//            'options' => array(
//                'label' => 'Url',
//                'description' => $externalUriDescription
//            ),
//            'attributes' => array(
//                'class' => 'left-align',
//                'size' => 50
//            )
//        ));

//        $systemUri = new Fieldset('systemUrl', array('label' => 'System Url'));
//        $systemUri->setAttribute('id', 'systemUrl');
//        $systemUri->add(array(
//            'name' => 'uri',
//            'type' => 'Zend\Form\Element\Text',
//            'options' => array(
//                'label' => 'Url',
//                'description' => 'exp : /fa/front-page'
//            ),
//            'attributes' => array(
//                'class' => 'left-align',
//                'size' => 50
//            )
//        ));
//        $systemUri->add(array(
//            'type' => 'Zend\Form\Element\Hidden',
//            'name' => 'api',
//            'options' => array(),
//            'attributes' => array(
//                'value' => '\Menu\API\Menu'
//            )
//        ));
//
//
//        $paramsFieldset->add($externalUri);
//        $paramsFieldset->add($systemUri);
//        $form->menuTypes['systemUrl'] = array(
//            'label' => 'System Url',
//
//        );
//
//        $form->menuTypes['externalUrl'] = array(
//            'label' => 'External Url',
//            'note' => $externalUriDescription
//        );
    }

    public function onLoadBlockConfigs(Event $e)
    {
        /* @var $form NewBlock */
        $form = $e->getParam('form');
        $type = $e->getParam('type');

        if ($type == 'menu_block') {
            $dataFieldset = $form->get('data');
            $blockInfo = Block::getBlockInfo($type);
            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel('Menu Block Settings');
            $dataFieldset->add($fiedlset);

            $menus = getSM('menu_table')->getArray();
            $fiedlset->add(array(
                'name' => 'menuId',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Menu',
                    'value_options' => $menus,
                    'description' => 'Select the menu you want to be loaded in this block'
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));

            $fiedlset->add(array(
                'name' => 'responsive',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'MENU_RESPONSIVE',
                    'value_options' => array(
                        '0' => 'MENU_IS_NOT',
                        '1' => 'MENU_IS',
                    ),
                    // 'description' => ''
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));

            $fiedlset->add(array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'template',
                'options' => array(
                    'description' => 'the template file used in rendering the form.default is menu/template/simple'
                )
            ));
        }
    }
} 