<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 1:21 PM
 */

namespace Links\API;


use Application\API\App;
use Menu\Form\MenuItem;
use SiteMap\Model\Url;
use Theme\API\Common;
use Zend\EventManager\Event;

class EventManager
{
    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $form->menuTypes['links'] = array(
            'label' => 'Links',
            'note' => "A list of links to friends site's",
            'params' => array(array('route' => 'app/links')),
        );

        $form->menuTypes['links_category'] = array(
            'label' => 'Links Category',
            'note' => "A list of links to friends site's in a specific category",
            'data-url' => url('admin/menu-links-category-list'),
            'params' => array(array('route' => 'app/links/category'), 'catId', 'catName'),
            'template' => '[catId] - [catName]',
        );
    }

    public function OnSiteMapGeneration(Event $e)
    {
        $sitemap = $e->getParam('sitemap');
        $config = $e->getParam('config');

        $categoryItemTable = getSM()->get('category_item_table');
        $links = getSM('links_table')->getAllTranslated(array('itemStatus' => 1));
        if ($links) {
            foreach ($links as $link) {
                if (isset($config['Links']['links']['html']) && $config['Links']['links']['html'])
                    $sitemap->tree['/']['children']['Links']['children']['links']['children'][] = Common::Link($link->itemName, $link->itemLink);

                if (isset($config['Links']['links']['xml']) && $config['Links']['links']['xml'])
                    $sitemap->addUrl(new Url($link->itemLink));
            }
        }
        if (isset($config['Links']['links']['html']) && $config['Links']['links']['html'])
            $sitemap->tree['/']['children']['Links']['children']['links']['data'] = t('LINKS_ALL_LINKS');

        if (isset($config['Links']['category']['html']) && $config['Links']['category']['html']) {
            $categoryItemArray = $categoryItemTable->getItemsForSitemap('links_category', 0);
            $categoryItemForView = getSM('links_table')->createLinkItems($categoryItemArray, 0);
            $sitemap->tree['/']['children']['Links']['children']['category']['children'] = $categoryItemForView;
        }

        if (isset($config['Links']['category']['xml']) && $config['Links']['category']['xml']) {
            $categoryItems = $categoryItemTable->getItemsByMachineName('links_category');
            if ($categoryItems) {
                foreach ($categoryItems as $item)
                    if (isset($item->itemName) && $item->itemName)
                        $sitemap->addUrl(new Url(Common::Link($item->itemName, App::siteUrl() . url('app/links/category', array('catId' => $item->id, 'catName' => $item->itemName), array())))) ;
            }
        }


        if (((isset($config['Links']['links']['html']) && $config['Links']['links']['html'])) ||
            ((isset($config['Links']['category']['html']) && $config['Links']['category']['html']))
        ) {
            $sitemap->tree['/']['children']['Links']['data'] = t('Links');
            if ((isset($config['Links']['category']['html']) && $config['Links']['category']['html']))
                $sitemap->tree['/']['children']['Links']['children']['category']['data'] = t('LINK_CATEGORIES');
        }


    }
} 