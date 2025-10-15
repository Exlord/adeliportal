<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/12/13
 * Time: 10:15 AM
 */

namespace Menu\View\Helper;


use Components\Model\Block;
use Menu\Navigation\Service\DynamicNavigationFactory;
use System\API\BaseAPI;
use System\View\Helper\BaseHelper;
use Theme\API\Common;
use Zend\Navigation\Service\ConstructedNavigationFactory;
use Zend\Navigation\Page\Mvc as MvcPage;

class MenuBlock extends BaseHelper
{
    private $menu_items;
    private $routeMatch;
    private $router;

    public function __invoke($block)
    {
        $this->menu_items = null;
        $this->routeMatch = getSM()->get('Application')->getMvcEvent()->getRouteMatch();
        $this->router = getSM('Router');
        $html = '';


        $template = 'menu/template/simple';
        $menuId = 0;
        $responsive = 0;
        if (is_scalar($block)) {
            $menuName = $block;
            $menu = getSM()->get('menu_table')->getByName($menuName);
            $menuId = $menu->id;
        }
        if ($block instanceof Block) {
            $menuId = $block->data['menu_block']['menuId'];
            if (isset($block->data['menu_block']['responsive']))
                $responsive = $block->data['menu_block']['responsive'];
            $menuName = 'menu-block-' . $block->id;
            $block->data['class'] .= ' menu-block';
            $block->blockId = $menuName;
            if (isset($block->data['menu_block']['template']))
                $template = $block->data['menu_block']['template'];
        }
        if (!$block)
            $menu_items = getSM()->get('menu_item_table')->getAllTranslated(array('status' => 1));
        else
            $menu_items = getSM()->get('menu_item_table')->getSimpleItems($menuId);
        if (!$menu_items->count())
            return $html;

//        $menu_items = $menu_items->toArray();
//        foreach ($menu_items as $item) {
//            $this->menu_items[$item['id']] = $item;
//        }

        $parents = array();
        foreach ($menu_items as $item) {
            $parents[$item->parentId][] = (array)$item;
        }

        $this->menu_items = $parents;

        $nav = $this->makePages(0);

        $nav = $this->render($nav);
//        $navigation = new DynamicNavigationFactory($menuName, $nav);
//        if (!$this->getServiceManager()->has($menuName))
//            $this->getServiceManager()->setService($menuName, $navigation->createService($this->getServiceManager()));
//
//        $navigation_helper = getSM()->get('ViewHelperManager')->get('navigation');
//        /* @var $menu \Zend\View\Helper\Navigation\Menu */
//        $menu = $navigation_helper()->menu($menuName);


        // find deepest active
//        $found = $menu->findActive($menu->getContainer());
//        if ($found) {
//            $foundPage = $found['page'];
//            $foundDepth = $found['depth'];
//        } else {
//            $foundPage = null;
//        }


//        $nav = array();

//        $html = $this->view->render($template, array(
//            'nav' => $nav,
//        ));
        $class = array();
        $class['class'] = 'navigation';
        if ($responsive)
            $class['class'] = 'nav navbar-nav';
        $html = $this->__render_simple_menu($nav, $class, $responsive);
        if ($html && $responsive)
            $html = $this->__renderMenuResponsive($html,$block->id);
        return $html;
    }

    private function render($nav)
    {
        $menu = array();
        if (count($nav)) {
            /* @var $page \Zend\Navigation\Page\AbstractPage */
            foreach ($nav as $page) {
                $item = array();
                $attr = array();
                $active = $page->isActive(true);
                $attr['target'] = $page->get('target');
                if ($page->get('nofollow') != null)
                    $attr['rel'] = 'nofollow';
                if ($itemTitle = $page->get('itemTitle'))
                    $attr['title'] = $itemTitle;

                $label = '';
                if ($image = $page->get('image'))
                    $label = Common::Img($image, $page->get('imgAlt'), $page->get('imgTitle'), array('class' => 'img-responsive'));
                if ($page->get('showTitle'))
                    $label .= '<span>' . $page->getLabel() . '</span>';
                $item['data'] = Common::Link($label, $page->getHref(), $attr);
                if ($active)
                    $item['attributes']['class'][] = 'active';
                if ($page->hasPages()) {
                    $pages = $this->render($page->getPages());
                    $columns = $page->get('columns');
                    $isMega = $page->get('isMega');
                    $pageCount = count($pages);

                    if ($isMega === true) {
                        if ($pageCount) {
                            $chunk = (int)$pageCount / $columns;
                            if ($chunk < 1)
                                $chunk = 1;
                            $item['mega-children'] = array_chunk($pages, $chunk);
                        }
                    } else {
                        $item['children'] = $pages;
                    }
                }
                $menu[] = $item;
            }
        }

        return $menu;
    }

    /**
     * @param $parentId
     * @param int $parentId
     * @param \Zend\Navigation\Page\AbstractPage $parent
     * @return array
     */
    private function makePages($parentId, $parent = null)
    {
        $nav = array();
        if (isset($this->menu_items[$parentId])) {
            /* @var $item \Menu\Model\MenuItem */
            foreach ($this->menu_items[$parentId] as $item) {
                $item['config'] = unserialize($item['config']);
                $columns = 1;
                if (isset($item['config']['megaMenu']['columns'])) {
                    $columns = (int)$item['config']['megaMenu']['columns'];
                    if (!$columns)
                        $columns = 1;
                }
                $isMega = false;
                if (isset($item['config']['megaMenu']['isMega'])) {
                    $isMega = (boolean)$item['config']['megaMenu']['isMega'];
                }

//                $page['title'] = $item->getItemTitle();
                $item['itemUrlTypeParams'] = unserialize($item['itemUrlTypeParams']);

                /* @var $api BaseAPI */
                $api = 'Menu\API\Menu';
                if (isset($item['itemUrlTypeParams'][$item['itemUrlType']]['api']))
                    $api = $item['itemUrlTypeParams'][$item['itemUrlType']]['api'];

                $image = '';
                if (isset($item['image'])) {
                    $image = $item['image'];
                }

                $showTitle = 1;
                if (isset($item['config']['options']['showTitle'])) {
                    $showTitle = $item['config']['options']['showTitle'];
                }

                $imgTitle = '';
                if (isset($item['config']['options']['imgTitle'])) {
                    $imgTitle = $item['config']['options']['imgTitle'];
                }

                $imgAlt = '';
                if (isset($item['config']['options']['imgAlt'])) {
                    $imgAlt = $item['config']['options']['imgAlt'];
                }

                $target = '_parent';
                if (isset($item['config']['options']['target'])) {
                    $target = $item['config']['options']['target'];
                }

                $nofollow = null;
                if (isset($item['config']['options']['nofollow'])) {
                    $nofollow = $item['config']['options']['nofollow'];
                }
                $page = $api::makeMenuUrl($item['itemUrlTypeParams']);
                $page->setLabel($item['itemName']);
                $page->setId($item['id']);
                $page->set('columns', $columns);
                $page->set('isMega', $isMega);
                $page->set('target', $target);
                $page->set('nofollow', $nofollow);
                $page->set('showTitle', $showTitle);
                $page->set('imgTitle', $imgTitle);
                $page->set('imgAlt', $imgAlt);
                $page->set('image', $image);
                $page->set('itemTitle', $item['itemTitle']);
                if ($page instanceof MvcPage) {
                    $page->setRouter($this->router);
                    if ($this->routeMatch)
                        $page->setRouteMatch($this->routeMatch);
                }

                if (isset($this->menu_items[$item['id']])) {
                    $page->setPages($this->makePages($item['id'], $page));
                }
                $nav[] = $page;
            }
        }
        return $nav;
    }

    private function __render_simple_menu($items, $attr = array(), $responsive = 0)
    {
        $html = '';
        if (count($items)) {
            $attr = \Theme\API\Common::Attributes($attr);
            $html .= "<ul $attr>";
            foreach ($items as $key => $item) {
                $data = '';
                if (!is_array($item)) {
                    $html .= "<li>" . $item . "</li>";
                } else {
                    $data = $item['data'];
                    $ch_html = '';
                    if (isset($item['mega-children'])) {
                        $children = array();
                        foreach ($item['mega-children'] as $chunk) {
                            $children[] = self::__render_simple_menu($chunk);
                        }
                        if (count($children) > 1) {
                            $ch_html .= "<ul class='mega-sub'>";
                            foreach ($children as $ch) {
                                $ch_html .= "<li class='column'>" . $ch . "</li>";
                            }
                            $ch_html .= "</ul>";
                        } elseif (count($children))
                            $ch_html = current($children);
                    } elseif (isset($item['children'])) {
                        $dropDownAttr = array();
                        if ($responsive) {
                            $dropDownAttr['class'] = 'dropdown-menu';
                            $item['attributes']['class'] = 'dropdown';
                        }
                        $ch_html = self::__render_simple_menu($item['children'], $dropDownAttr, $responsive);
                    }
                    $menuCaret = '';
                    if (!empty($ch_html) && $responsive)
                        $menuCaret = '<a href="#" class="dropdown-toggle link_caret" data-toggle="dropdown"><span class="caret"></span></a>';
                    $attr = '';
                    if (isset($item['attributes']))
                        $attr = \Theme\API\Common::Attributes($item['attributes']);
                    $html .= "<li $attr>" . $data . $menuCaret . $ch_html . "</li>";
                }
            }
            $html .= "</ul>";
        }
        return $html;
    }

    private function __renderMenuResponsive($html,$blockId)
    {
        /* <a class="navbar-brand" href="' . url('app') . '">' . t('Home Page') . '</a> */
        return '<nav class="navbar navbar-default" role="navigation">
                    <div class="container-fluid">
                        <div class="navbar-header">
                             <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                data-target="#bs-example-navbar-collapse-'.$blockId.'">
                                <span class="sr-only"> مشاهده منو ها </span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                 <span class="icon-bar"></span>
                             </button>
                        </div>
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-'.$blockId.'">
                            ' . $html . '
                        </div>
                    </div>
                </nav>';
    }
}