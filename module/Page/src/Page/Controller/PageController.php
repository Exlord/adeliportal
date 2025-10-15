<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Page\Controller;

use Application\API\App;
use Application\API\Breadcrumb;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use File\API\File;
use Localization\API\Date;
use Page\Form;
use Page\Model;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;
use DataView\Lib\DataGrid;
use Zend\View\Model\ViewModel;


class PageController extends BaseAbstractActionController
{
    public function indexAction($type = null)
    {
        $selectFilters = array();
        if (!$type)
            $type = $this->params()->fromRoute('type');

        $isStaticPage = 0;
        if ($type == 'page')
            $isStaticPage = 1;
        // $isStaticPage = $type == 'page';

        $grid = new DataGrid('page_table');
        // $grid->getSelect()->where(array('isStaticPage' => $isStaticPage));

        $this->getPageTable()->getPageList($grid->getSelect(), $isStaticPage);

        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $pageTitle = new Column('pageTitle', 'Title');

        // $domain = new Column('domains', 'Domains');

        if (getSM()->has('comment_table')) {
            $showCommentIcon = new Button('Comments', function (Button $col) use ($type) {
                $col->route = 'admin/comment';
                $col->routeOptions['query'] =
                    array(
                        'grid_filter_entityId' => $col->dataRow->id,
                        'grid_filter_entityType' => $type,
                        'title' => base64_encode($col->dataRow->pageTitle));
                $col->icon = 'glyphicon glyphicon-comment';
            }, array(
                'headerAttr' => array('width' => '34px'),
                'attr' => array('align' => 'center'),
                'contentAttr' => array('class' => array('ajax_page_load', 'btn', 'btn-default'))
            ));
        }

        $edit = new EditButton();
        $delete = new DeleteButton();

        $view = new Button('Show', function (Button $col) use ($type, $isStaticPage) {
            if ($isStaticPage)
                $col->route = 'app/page-view';
            else
                $col->route = 'app/single-content';
            $col->icon = 'glyphicon glyphicon-eye-open';
            $col->routeParams = array('id' => $col->dataRow->id, 'title' => App::prepareUrlString($col->dataRow->pageTitle));
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('target' => '_blank', 'class' => array('btn', 'btn-default'))
        ));

        $columnsArray = array($id, $pageTitle);

        if ($isStaticPage) {
            $grid->setRoute('admin/page');
        } else {
            $grid->setRoute('admin/content');

            $catName = new Column('itemName', 'Categories', array(
                'headerAttr' => array('width' => '100px'),
                'attr' => array('align' => 'center'),
            ));
            $columnsArray[] = $catName;

            $publish = new Custom('status', 'Publish', function (Column $col) {
                $dateFormat = getSM()->get('viewhelpermanager')->get('DateFormat');

                $status = 'unknown';
                switch ($col->dataRow->status) {
                    case 0:
                        $status = 'unknown';
                        break;
                    case 1:
                        $status = 'published';
                        if (!empty($col->dataRow->publishDown) && $col->dataRow->publishDown != 0 && $col->dataRow->publishDown < time())
                            $status = 'unpublished';
                        elseif (!empty($col->dataRow->publishUp) && $col->dataRow->publishUp != 0 && $col->dataRow->publishUp > time())
                            $status = 'future-publish';

                        break;
                    case 2:
                        $status = 'unpublished';
                        break;
                    case 3:
                        $status = 'archived';
                        break;
                    case 4:
                        $status = 'deleted';
                        break;
                }

                switch ($status) {
                    case 'future-publish':
                        $class = 'glyphicon glyphicon-calendar text-info grid-icon';
                        $status_text = t('Future Publish');
                        break;
                    case 'unpublished':
                        $status_text = t('UnPublished');
                        $class = 'glyphicon glyphicon-remove-circle text-danger grid-icon';
                        break;
                    case 'archived':
                        $status_text = t('Archived');
                        $class = 'glyphicon glyphicon-book text-muted grid-icon';
                        break;
                    case 'deleted':
                        $status_text = t('Deleted');
                        $class = 'glyphicon glyphicon-ban-circle text-danger grid-icon';
                        break;
                    case 'published':
                        $class = 'glyphicon glyphicon-ok-circle text-success grid-icon';
                        $status_text = t('Published');
                        break;
                    default :
                        $status_text = t('Unknown');
                        $class = 'glyphicon glyphicon-exclamation-sign text-warning grid-icon';
                        break;

                }

                // $col->attr['class'][] =$class;
                if ($col->dataRow->publishDown == 0)
                    $date = t('PAGE_UNLIMITED');
                else
                    $date = $dateFormat($col->dataRow->publishDown, 4);

                $html = '<div class=tooltip-publish>
                    <div>
                    <label>' . t('Status') . ' : ' . $status_text . '</label>
                    </div>
                    <div>
                    <label>' . t('Publish Up') . ' : ' . $dateFormat($col->dataRow->publishUp, 4) . '</label>
                    <br/>
                    <label>' . t('Publish Down') . ' : ' . $date . '</label>
                    </div>
            </div>';
                return '<div data-tooltip="' . $html . '" class="' . $class . '" ></div>';
            }, array(
                'headerAttr' => array('width' => '34px'),
                'attr' => array('align' => 'center'),
            ), true);
            $columnsArray[] = $publish;


            $publish->selectFilterData = array(
                '1' => t('Published'),
                '2' => t('UnPublished'),
                '3' => t('Archive'),
                '4' => t('Recycle')
            );
            $selectFilters[] = $publish;


            $categoryItems = $this
                ->getServiceLocator()
                ->get('category_item_table')
                ->getItemsTreeByMachineName('article');
            $tags = new Column('tagsId', 'Tags');
            $tags->selectFilterData = $categoryItems;
            $tags->setTableName('tbl_tags_page');
            $selectFilters[] = $tags;

            $grid
                ->getSelect()
                ->join('tbl_tags_page', $grid->getTableGateway()->getTable() . '.id=tbl_tags_page.pageId', array('tagsId'), 'LEFT');
        }
        if (getSM()->has('comment_table')) {
            $columnsArray[] = $showCommentIcon;
        }
        $columnsArray[] = $view;
        $columnsArray[] = $edit;
        $columnsArray[] = $delete;
        $grid->addColumns($columnsArray);

        if ($isStaticPage)
            $grid->addNewButton('New', 'New Page');
        else {
            $grid->addNewButton('New', 'New Content');
            $grid->addButton('Publish', 'Publish selected items', '/update', false, 'btn btn-default', false, 'publishPages', array(), array(), array(), 'glyphicon glyphicon-ok-circle text-success');
            $grid->addButton('Unpublish', 'Unpublish selected items', '/update', false, 'btn btn-default', false, 'unpublishPages', array(), array(), array(), 'glyphicon glyphicon-remove-circle text-danger');
        }

        $grid->addDeleteSelectedButton();
        $grid->setSelectFilters($selectFilters);
        $grid->defaultSort = $id;
        $grid->defaultSortDirection = 'DESC';

        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        $this->viewModel->setTemplate('page/page/index');
        return $this->viewModel;
    }

    /**
     * @param Model\Page $item
     * @return \Zend\Http\Response|ViewModel
     */
    public function newAction($item = null)
    {
        $tags = null;
        $type = $this->params()->fromRoute('type', 0);
        if ($type == 0) {
            $url = 'admin/content/new';
            $tags = getSM('category_item_table')->getItemsTreeByMachineName('article');
        } else
            $url = 'admin/page/new';

        $galleryArray = array();
        if (getSM()->has('gallery_table'))
            $galleryArray = getSM('gallery_table')->getGroupsArray(array('status' => 1, 'type' => 'gallery'));

        $form = new Form\Page($tags, $type, '', $galleryArray);
        $oldImage = '';
        $id = 0;
        if ($item) {
            $id = $item->id;
            $item->image = unserialize($item->image);
            if (isset($item->image['image']))
                $oldImage = $item->image['image'];

            $item->config = unserialize($item->config);

            $dateFormat = getSM()->get('viewhelpermanager')->get('DateFormat');
            if (isset($item->publishUp) && !empty($item->publishUp))
                $item->publishUp = $dateFormat($item->publishUp, 3);
            if (isset($item->publishDown) && !empty($item->publishDown))
                $item->publishDown = $dateFormat($item->publishDown, 3);
            if (isset($item->refGallery) && !empty($item->refGallery))
                $item->refGallery = unserialize($item->refGallery);

            if ($type == 0)
                $url = 'admin/content/edit';
            else
                $url = 'admin/page/edit';

            $form->get('buttons')->remove('submit-new');

            //TODO getArray
            $selectPageTags = $this->getServiceLocator()->get('page_tags_table')->getAll(array('pageId' => $id));
            if ($selectPageTags) {
                $tagsArray = array();
                foreach ($selectPageTags as $row) {
                    $tagsArray[] = $row->tagsId;
                }
                $item->tags = $tagsArray;
            }

            if (!$item->tags)
                $item->tags['0'] = '';
        } else {
            $item = new Model\Page();
            $item->isStaticPage = $type;
            if ($type == 0)
                $url = 'admin/content/new';
            else
                $url = 'admin/page/new';
        }


        $form->setAction($this->url()->fromRoute($url, array('type' => $type, 'id' => $id)));
        $form->bind($item);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $file = $this->request->getFiles()->toArray();

            if (isset($post['buttons']['cancel'])) {
                if ($type == 1)
                    return $this->indexAction('page');
                elseif ($type == 0)
                    return $this->indexAction('content');
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) { //TODO file has no validation
                    $item->createdBy = current_user()->id;

                    if (!empty($item->publishUp))
                        $item->publishUp = Date::jalali_to_gregorian($item->publishUp);

                    if ($item->status == '1' && empty($item->publishUp))
                        $item->publishUp = time();

                    if (!empty($item->publishDown))
                        $item->publishDown = Date::jalali_to_gregorian($item->publishDown);

                    if (!empty($item->refGallery))
                        $item->refGallery = serialize($item->refGallery);

                    $image = '';
                    if (isset($file['image']['image']['name']) && !empty($file['image']['image']['name'])) {
                        $image = File::MoveUploadedFile($file['image']['image']['tmp_name'], PUBLIC_FILE . '/page/' . $type, $file['image']['image']['name']);
                    } else
                        $image = $oldImage;
                    if (isset($post->image)) {
                        $post->image['image'] = $image;
                        $item->image = serialize($post->image);
                    }

                    $item->config = serialize($item->config);

                    if (isset($post->refGallery))
                        $item->refGallery = serialize($post->refGallery);

                    $id = getSM('page_table')->save($item);
                    // if (!$id)
                    if ($item->id)
                        $id = $item->id;
                    if ($type == 0) { //TODO multi insert
                        getSM('page_tags_table')->delete(array('pageId' => $id));
                        foreach ($item->tags as $value) {
                            getSM('page_tags_table')->insert(array(
                                'pageId' => $id,
                                'tagsId' => $value,
                            ));
                        }
                    }
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");
                    if (!isset($post['buttons']['submit-new'])) {
                        if ($type == 1)
                            return $this->indexAction('page');
                        elseif ($type == 0)
                            return $this->indexAction('content');
                    } else {
                        $item = new Model\Page();
                        $item->isStaticPage = $type;
                        $form->bind($item);
                    }
                } else
                    $this->formHasErrors();
            }
        }

        $this->viewModel->setTemplate('page/page/new');
        $this->viewModel->setVariables(array(
            'form' => $form,
            'srcImageOld' => $oldImage,
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        $type = $this->params()->fromRoute('type', 0);
        if (!$id) {
            $redirect = $type ? 'admin/content' : 'admin/page';
            return $this->invalidRequest($redirect);
        }

        $item = $this->getServiceLocator()->get('page_table')->get($id);
        return $this->newAction($item);
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $status = $this->params()->fromPost('status', false);
            $id = $this->params()->fromPost('id', 0);
            if ($status !== false) {
                if (!$id) {
                    $this->flashMessenger()->addErrorMessage('Required parameter, `ID` is not provided !');
                } else {
//                    $item = $this->getServiceLocator()->get('page_table')->get($id);
//                    $item->status = $status;
                    $data = array('status' => $status);
                    if ($status == '1') {
                        $data['publishUp'] = time();
                        $data['publishDown'] = null;

                    } else {
                        $data['publishUp'] = null;
                        $data['publishDown'] = null;
                    }
                    $this->getPageTable()->update($data, array('id' => $id));
                }
            }
        }

        return $this->indexAction();
    }

    public function deleteAction()
    {
        $id = $this->params()->fromPost('id', 0);
        $type = $this->params()->fromRoute('type', 0);
        if ($id) {
            $imageUrl = $this->getPageTable()->remove($id);
            if (is_array($imageUrl))
                foreach ($imageUrl as $img)
                    @unlink($img);
            return new JsonModel(array('status' => 1));
        } else
            return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function viewAction()
    {
        $type = $this->params()->fromRoute('type');
        $id = $this->params()->fromRoute('id', 0);
        $ajaxLoad = $this->params()->fromPost('ajaxLoad', 0);
        if (!$id) {
            if ($type == 'content')
                return $this->invalidRequest('app/content');
            else
                return $this->invalidRequest('app/front-page');
        }

        /* @var $page \Page\Model\Page */
        $page = $this->getPageTable()->getById($id);

        if ($page && ($page->isStaticPage == '0' || $page->status == '1')) {

//            if (App::matchDomain($page->domains, $page->domainVisibility)) {
            $config = $this->getServiceLocator()->get('config_table')->getByVarName('page_config')->varValue;
            $page->config = unserialize($page->config);
            if ($page->isStaticPage) {
                Breadcrumb::AddMvcPage($page->pageTitle,
                    'app/page-view', array('id' => $page->id, 'title' => App::prepareUrlString($page->pageTitle)));

                $entityType = \Page\Module::PAGE_ENTITY_TYPE;

                $this->viewModel->setVariables(array(
                    'page' => $page,
                    'isStaticPage' => $page->isStaticPage,
                    'entityType' => $entityType,
                    'config' => $config
                ));
                $this->viewModel->setTemplate('page/page/view');
                return $this->viewModel;
            } else {

                Breadcrumb::AddMvcPage(t('Contents'), 'app/content');

                Breadcrumb::AddMvcPage($page->pageTitle,
                    'app/single-content', array('id' => $page->id, 'title' => App::prepareUrlString($page->pageTitle)));

                $entityType = \page\Module::CONTENT_ENTITY_TYPE;
                $this->getPageTable()->update(array('hits' => ++$page->hits), array('id' => $id));
                $profile = getSM()->get('user_table')->get($page->createdBy);
                $selectTags = getSM()->get('page_tags_table')->getTags($id);
                if ($ajaxLoad)
                    $this->viewModel->setTerminal(true);
                $this->viewModel->setVariables(array(
                    'page' => $page,
                    'isStaticPage' => $page->isStaticPage,
                    'profile' => $profile,
                    'selectTags' => $selectTags,
                    'entityType' => $entityType,
                    'config' => $config,
                    'ajaxLoad' => $ajaxLoad,
                ));
                $this->viewModel->setTemplate('page/page/view');
                return $this->viewModel;
            }
//            }
        }
        $this->viewModel->setTemplate('page/page/not-found');
        return $this->viewModel;
    }

//    public function contentListAction()
//    {
//        $grid = new DataGrid('page_table');
//        $grid->setRoute('admin/content');
//        $grid->getSelect()->where(array('isStaticPage' => 0));
//        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
//        $grid->setIdCell($id);
//        $pageTitle = new Column('pageTitle', 'Title');
//
//        $showCommentIcon = new Button('Comments', function (Button $col) {
//            $col->route = 'admin/comment';
//            $col->routeOptions['query'] = array('grid_filter_entityId' => $col->dataRow->id, 'grid_filter_entityType' => \Page\Module::CONTENT_ENTITY_TYPE, 'title' => base64_encode($col->dataRow->pageTitle));
//        }, array(
//            'headerAttr' => array('width' => '34px'),
//            'attr' => array('align' => 'center'),
//            'contentAttr' => array('class' => array('grid_button', 'mail_button', 'ajax_page_load'))
//        ));
//
//
//        $edit = new EditButton();
//        $delete = new DeleteButton();
//
//        $view = new Button('Show', function (Button $col) {
//            $col->route = 'app/page-view';
//            $col->routeParams = array('id' => $col->dataRow->id, 'title' => App::prepareUrlString($col->dataRow->pageTitle));
//        }, array(
//            'headerAttr' => array('width' => '34px'),
//            'attr' => array('align' => 'center'),
//            'contentAttr' => array('target' => '_blank', 'class' => array('grid_button', 'search_button'))
//        ));
//
//
//        $grid->addColumns(array($id, $pageTitle, $showCommentIcon, $view, $edit, $delete));
//
//        $grid->addNewButton('New Content');
//        $this->viewModel->setVariables(array(
//            'grid' => $grid->render(),
//            'buttons' => $grid->getButtons(),
//        ));
//        $this->viewModel->setTemplate('page/page/content-list');
//        return $this->viewModel;
//    }

    public function autoCompleteAction()
    {
        $txt = $this->params()->fromPost('name_startsWith');
        $pagination = $this
            ->getServiceLocator()
            ->get('category_item_table')
            ->getAll(array('itemName like ?' => "" . $txt . "%"));
        foreach ($pagination as $row) {
            $tags[] = array(
                'name' => $row->itemName,
                'id' => $row->id,
            );
        }
        return new JsonModel(array(
            'tags' => $tags,
        ));

    }

    public function viewContentAction()
    {
        Breadcrumb::AddMvcPage(t('Contents'), 'app/content');

        $catItemArray = array();
        $fileArray = array();
        $showTags = false;
        $oldTag = null;
        $ajaxLoaded = false;
        $flagShowAllInfo = false;
        $frontPage = false; //dar avvalin safhe ke tamame tag ha ra miavarad page haii biayand ke ejaze namayesh dar safhe avval ra dashte bashand ya na
        $config = getConfig('page_config')->varValue;
        if (isset($config['showTags']) && $config['showTags'])
            $showTags = (int)$config['showTags'];

        $tagName = $this->params()->fromRoute('tagName', '');
        $tagId = (int)$this->params()->fromRoute('tagId', 0);
        $page = $this->params()->fromQuery('page', 1);
        $pageCount = $this->params()->fromPost('page', 0);
        if ($pageCount) {
            $showTags = false;
            $page = $pageCount;
            $ajaxLoaded = true;
        }

        $tagsId = array();
        $configItemId = null;
        if ((int)$tagId == 0) {
            $frontPage = true;
            if (isset($config['pageTags']))
                $configItemId = $config['pageTags'];
            $tagsId = $configItemId;
        } else
            $tagsId[] = $tagId;

        $selectCat = getSM('category_table')->getAll(array('catMachineName' => 'article'))->current();

        $selectCatItem = null;
        $tagsPageCount = null;

        if ($showTags && isset($selectCat->id)) {
            $catItemIdArray = array();
            $selectCatItemAll = getSM('category_item_table')->getItemsByParentId($tagId, $selectCat->id, $configItemId);
            if ($selectCatItemAll->count()) {
                foreach ($selectCatItemAll as $row) {
                    $catItemArray[] = array(
                        'id' => $row->id,
                        'itemName' => $row->itemName,
                        'itemText' => $row->itemText,
                    );
                    $catItemIdArray[] = $row->id;
                }
            } else
                $flagShowAllInfo = true;

            $selectCatItem = getSM('category_item_table')->getById($tagId);
            if ($selectCatItem) {
                $oldTag[] = array(
                    'id' => $selectCatItem->id,
                    'itemName' => $selectCatItem->itemName,
                    'itemText' => $selectCatItem->itemText,
                );
                $catItemIdArray[] = $selectCatItem->id;
            }


            if ($showTags == 2) {
                $fileSelect = getSM('file_table')->getByEntityType('category-item', $catItemIdArray);
                if ($fileSelect->count()) {
                    foreach ($fileSelect as $row) {
                        if (!isset($fileArray[$row->entityId]))
                            $fileArray[$row->entityId] = array(
                                'fPath' => $row->fPath,
                                'alt' => $row->fAlt,
                                'title' => $row->fTitle,
                            );
                    }
                }
            }
        }

        if ($tagId)
            $configItemId = (array)$tagId;

        $selectTags = getSM('category_item_table')->getItems((int)$selectCat->id, $configItemId);
        if ($selectTags)
            foreach ($selectTags as $key => $val)
                $tagsId[] = $key;

        $tagsPageCount = getSM('page_tags_table')->getPageCount();

        $paginator = $this->getPageTable()->getPages($tagsId, $page, $frontPage, false, 24);

        $route = 'app/content';
        $routeParams = array(
            'tagId' => $tagId,
            'tagName' => $tagName,
        );
        $this->viewModel->setTemplate('page/page/view-content');

        $resolver = $this->getEvent()
            ->getApplication()
            ->getServiceManager()
            ->get('Zend\View\Resolver\TemplatePathStack');

        if ($tagId) {

            $tagsParents = array_reverse($this->getCategoryItemTable()->getParents($tagId));
            foreach ($tagsParents as $pTag) {
                if ($pTag) {
                    Breadcrumb::AddMvcPage($pTag->itemName, 'app/content', array('tagId' => $pTag->id, 'tagName' => $pTag->itemName));
                    $template = 'page/page/view-content-tags-' . $pTag->id;
                    if ($resolver->resolve($template))
                        $this->viewModel->setTemplate($template);
                }
            }
        }

        $this->viewModel->setVariables(
            array(
                'tags' => $catItemArray,
                'oldTag' => $oldTag,
                'pageCount' => $tagsPageCount,
                'showTags' => $showTags,
                'paginator' => $paginator,
                'route' => $route,
                'routeParams' => $routeParams,
                'fileArray' => $fileArray,
                'flagShowAllInfo' => $flagShowAllInfo,
                'ajaxLoaded' => $ajaxLoaded,
                'config' => $config,
            ));
        if ($ajaxLoaded) {
            $html = $this->render($this->viewModel);
            return new JsonModel(array(
                'html' => $html,
            ));
        } else
            return $this->viewModel;
    }

    /**
     * @return \Page\Model\PageTable
     */
    private function getPageTable()
    {
        return getSM('page_table');
    }

    public function menuPageListAction()
    {
        $term = $this->params()->fromQuery('term');
        $type = $this->params()->fromRoute('type');
        if ($type == 'single-page')
            $isStatic = 1;
        else
            $isStatic = 0;
        $data = $this->getPageTable()->search($term, $isStatic);

        $json = array();
        foreach ($data as $row) {
            $json[] = array(
                'id' => $row->id,
                'title' => $row->pageTitle,
            );
        }
        return new JsonModel($json);
    }

    public function menuPageTagListAction()
    {
        $categoryItems = $this
            ->getServiceLocator()
            ->get('category_item_table')
            ->getItemsTreeByMachineName('article');

        $json = array();
        foreach ($categoryItems as $key => $item) {
            $json[] = array('tagId' => $key, 'tagName' => $item);
        }
        return new JsonModel($json);
    }

    public function configAction()
    {
        $config = $this->getServiceLocator()->get('config_table')->getByVarName('page_config');
        $selectTags = getSM('category_item_table')->getItemsFirstLevelByMachineName('article');
        $tags = array();
        foreach ($selectTags as $row)
            $tags[$row['id']] = $row['itemName'];
        $form = new \Page\Form\Config($tags);
        $form = prepareConfigForm($form);
        if (!empty($config->varValue))
            $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());

                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Page Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Page configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('page/page/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;

    }

    public function deleteImgAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost();
            if ($id) {
                getSM('page_table')->update(array('image' => null), array('id' => $id));
                return new JsonModel(array(
                    'status' => 1,
                ));
            }
        }
        return new JsonModel(array(
            'status' => 0,
        ));
    }

}
