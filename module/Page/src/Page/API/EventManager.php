<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 3:25 PM
 */

namespace Page\API;


use Application\API\App;
use Application\Model\Config;
use Components\Form\NewBlock;
use Cron\API\Cron;
use Menu\Form\MenuItem;
use Page\Model\PageTable;
use SiteMap\Model\Url;
use Theme\API\Common;
use Zend\EventManager\Event;
use Zend\Form\Fieldset;

class EventManager
{

    public function onNewsLetterGetInformation(Event $e)
    {
        $data = $e->getParam('data');

        $data->data[] = array(
            'namespace' => __NAMESPACE__,
            'displayName' => 'Page',
            'desc' => 'Page_config_descNewsLetter',
            'apiName' => 'page_api',
        );
    }

    public function onCronRun(Config $last_run)
    {
        $start = microtime(true);
        $interval = '+ 1 day';
        $last = @$last_run->varValue['Page_last_run'];
        if (Cron::ShouldRun($interval, $last)) { //TODO Convert to notify
            $page = $this->getPageTable()->getExpired();
            if ($page->count()) {
                $dataId = array();
                foreach ($page as $row) {
                    $dataId[] = $row->id;
                }
                $this->getPageTable()->update(
                    array('status' => 2, 'publishDown' => null, 'publishUp' => null),
                    array('id' => $dataId)
                );
            }
            db_log_info(sprintf(t('Page expire cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
            $last_run->varValue['Page_last_run'] = time();
        }
    }

    public function onSearch(Event $e)
    {
        $search = $e->getParam('search');

        $pageTable = getSM('page_table');
        $result = $pageTable->systemSearch($search->keyword);
        if ($result && $result->count()) {
            foreach ($result as $row) {
                if ($row->isStaticPage == '1') {
                    $search->data['Static Page'][] = Common::Link($row->pageTitle,
                        url('app/page-view', array(
                            'id' => $row->id,
                            'title' => App::prepareUrlString($row->pageTitle)
                        )),
                        array('class' => 'search-item static-page'));
                } else {
                    $search->data['Contents'][] = Common::Link($row->pageTitle,
                        url('app/single-content', array(
                            'id' => $row->id,
                            'title' => App::prepareUrlString($row->pageTitle)
                        )),
                        array('class' => 'search-item article'));
                }
            }
        }

        $pageTagsTable = getSM('category_item_table');
        $result = $pageTagsTable->systemSearch('article', $search->keyword);
        if ($result && $result->count()) {
            foreach ($result as $row) {
                $link = Common::Link($row->itemName,
                    url('app/content', array(
                        'tagId' => $row->id,
                        'tagName' => App::prepareUrlString($row->itemName)
                    )),
                    array('class' => 'search-item content-tag'));
                $search->data['Content Category'][] = $link;
            }
        }
    }

    public function onCategoryItemUrlGeneration(Event $e)
    {
        $machineName = $e->getParam('machineName');
        $data = $e->getParam('data');

        if ($machineName == 'article') {
            foreach ($data->data as &$items) {
                $url = url('app/content',
                    array('tagId' => $items['id'], 'tagName' => App::prepareUrlString($items['title']))
                );
                $items['url'] = $url;
            }
        }
    }

    public function onLoadBlockConfigs(Event $e)
    {
        /* @var $form NewBlock */
        $form = $e->getParam('form');
        $type = $e->getParam('type');
        if ($type == 'content_block') {
            $dataFieldset = $form->get('data');
//            $form->extraScripts[] = "/js/page-block-setting.js";
            // $blockInfo = Block::getBlockInfo($type);
            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel('Latest Content Settings');
            $dataFieldset->add($fiedlset);
            $group = getSM('category_item_table')->getItemsTreeByMachineName('article');
            $fiedlset->add(array(
                'name' => 'tagId',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Groups',
                    'value_options' => $group,
                    'description' => 'Witch category you want to load the articles from.'
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));
            $fiedlset->add(array(
                'name' => 'customType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Type',
                    'value_options' => array(
                        0 => 'content',
                        1 => 'Page_MESSAGE',
                        2 => 'Page_BRAND',
                        3 => 'External Link'
                    ),
                    'description' => ''
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));
            $fiedlset->add(array(
                'name' => 'count',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Count',
                    'description' => 'Page_ARTICLES_COUNT_DESC'
                ),
                'attributes' => array(
                    'value' => 5
                ),
            ));
            $fiedlset->add(array(
                'name' => 'viewType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Page_VIEW_TYPE',
                    'description' => '',
                    'value_options' => array(
                        'normal' => 'Normal',
                        'slider' => 'Slider',
                    ),
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));
//            $fiedlset->add(array(
//                'name' => 'visibleCount',
//                'type' => 'Zend\Form\Element\Text',
//                'options' => array(
//                    'label' => 'Visible Count in slider',
//                    'description' => 'how many of the articles should be visible at any given time in slide view.'
//                ),
//                'attributes' => array(
//                    'value' => 2
//                ),
//            ));
//            $fiedlset->add(array(
//                'name' => 'scrollingItems',
//                'type' => 'Zend\Form\Element\Text',
//                'options' => array(
//                    'label' => 'Scrolling Items Count',
//                    'description' => 'how many item should be scrolled each time in slide view.'
//                ),
//                'attributes' => array(),
//            ));
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
                'name' => 'showPublished',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Page_SHOW_PUBLISHED',
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
                'name' => 'showHits',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Page_SHOW_HITS',
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
                'name' => 'showImage',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Page_SHOW_IMAGE',
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
                'name' => 'responsiveImage',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Page_RESPONSIVE_IMAGE',
                    'description' => 'Page_RESPONSIVE_IMAGE_DESC',
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
                'name' => 'resizeType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Page_RESIZE_TYPE',
                    'description' => "fix:don't keep the images ratio,resize to the exact given width and height<br/>relative:keep the images original ratio while resizing ",
                    'value_options' => array(
                        'fix' => 'fix',
                        'relative' => 'relative',
                    ),
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));
            $fiedlset->add(array(
                'name' => 'titleType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Page_TITLE_TYPE',
                    'description' => 'Page_TITLE_TYPE_DESC',
                    'value_options' => array(
                        'normal' => 'Normal',
                        'caption' => 'Caption',
                        'titleHidden' => 'Hidden',
                    ),
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));
            $fiedlset->add(array(
                'type' => 'Text',
                'name' => 'externalLink',
                'options' => array(
                    'label' => 'External link',
                    'description' => 'if provided , all the items will be linked to this link. this is required when Type==External Link'
                ),
                'attributes' => array(
                    'class' => 'text-left',
                )
            ));
//            $fiedlset->add(array(
//                'type' => 'Checkbox',
//                'name' => 'readMore',
//                'options' => array(
//                    'label' => 'Show read more link',
//                    'description' => ''
//                )
//            ));

            $slider = new Fieldset('slider');
            $slider->setLabel('Page_SLIDER_SITTINGS');
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
                        '1' => 'Visible on hover',
                        '2' => 'Hidden',
                        '3' => 'Always Visible'
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
                    'value' => 'latest_content',
                )
            ));
        }
    }

    public function OnSiteMapGeneration(Event $e)
    {
        $sitemap = $e->getParam('sitemap');
        $config = $e->getParam('config');

        /* @var $pageTable PageTable */
        $pageTable = getSM()->get('page_table');
        $categoryItemTable = getSM()->get('category_item_table');
        $pages = $pageTable->getAllTranslated(array('status' => 1));
        if ($pages) {
            foreach ($pages as $page) {
                $url = App::siteUrl() . $pageTable->getUrl($page);

                if ($page->isStaticPage && isset($config['Page']['static-pages']['html']) && $config['Page']['static-pages']['html'])
                    $sitemap->tree['/']['children']['Page']['children']['static-pages']['children'][] = Common::Link($page->pageTitle, $url);
                elseif (!$page->isStaticPage && isset($config['Page']['articles']['html']) && $config['Page']['articles']['html'])
                    $sitemap->tree['/']['children']['Page']['children']['dynamic-pages']['children'][] = Common::Link($page->pageTitle, $url);

                if ($page->isStaticPage && isset($config['Page']['static-pages']['xml']) && $config['Page']['static-pages']['xml'])
                    $sitemap->addUrl(new Url($url));
                elseif (!$page->isStaticPage && isset($config['Page']['articles']['xml']) && $config['Page']['articles']['xml'])
                    $sitemap->addUrl(new Url($url));
            }
        }
        if (isset($config['Page']['static-pages']['html']) && $config['Page']['static-pages']['html'])
            $sitemap->tree['/']['children']['Page']['children']['static-pages']['data'] = t('PAGE_CONSTANT_PAGES');
        if (isset($config['Page']['articles']['html']) && $config['Page']['articles']['html'])
            $sitemap->tree['/']['children']['Page']['children']['dynamic-pages']['data'] = Common::Link(t('PAGE_DYNAMIC_PAGES'), url('app/content'));

        if (isset($config['Page']['article-categories']['xml']) && $config['Page']['article-categories']['xml']) {
            $categoryItem = $categoryItemTable->getItemsTreeByMachineName('article');
            if ($categoryItem)
                foreach ($categoryItem as $catId => $catName) {
                    $url = App::siteUrl() . $pageTable->getUrlCategoryArticle($catId, $catName);
                    $sitemap->addUrl(new Url($url));
                }
        }

        if (isset($config['Page']['article-categories']['html']) && $config['Page']['article-categories']['html']) {
            $categoryItemArray = $categoryItemTable->getItemsForSitemap('article', 0);
            $categoryItemForView = $pageTable->createLinkItems($categoryItemArray, 0);
            $sitemap->tree['/']['children']['Page']['children']['category']['children'] = $categoryItemForView;
        }


        if (((isset($config['Page']['static-pages']['html']) && $config['Page']['static-pages']['html'])) ||
            ((isset($config['Page']['articles']['html']) && $config['Page']['articles']['html'])) ||
            ((isset($config['Page']['article-categories']['html']) && $config['Page']['article-categories']['html']))
        ) {
            $sitemap->tree['/']['children']['Page']['data'] = t('Pages');
            if ((isset($config['Page']['article-categories']['html']) && $config['Page']['article-categories']['html']))
                $sitemap->tree['/']['children']['Page']['children']['category']['data'] = t('PAGE_CATEGORIES');
        }
    }

    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $form->menuTypes['singlePage'] = array(
            'label' => 'Single page',
            'note' => 'Link to a single static page',
            'data-url' => url('admin/menu-page-list', array('type' => 'single-page')),
            'params' => array(
                array('route' => 'app/page-view'),
                'id',
                'title',
            ),
            'template' => '[id] - [title]',
        );

        $form->menuTypes['single-content'] = array(
            'label' => 'Single Article',
            'note' => 'Link to a single article page',
            'data-url' => url('admin/menu-page-list', array('type' => 'article')),
            'params' => array(
                array('route' => 'app/single-content'),
                'id',
                'title',
            ),
            'template' => '[id] - [title]',
        );

        $form->menuTypes['content'] = array(
            'label' => 'Articles List',
            'note' => 'A list of articles for a single category',
            'data-url' => url('admin/menu-page-tag-list'),
            'params' => array(
                array('route' => 'app/content'),
                'tagId',
                'tagName',
            ),
            'template' => '[tagId] - [tagName]',
        );
    }

    /**
     * @return \Page\Model\PageTable
     */
    private function getPageTable()
    {
        return getSM('page_table');
    }
} 