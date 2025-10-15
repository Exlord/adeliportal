<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Gallery\Controller;

use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use File\API\File;
use System\Controller\BaseAbstractActionController;
use Theme\API\Themes;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class GalleryItemController extends BaseAbstractActionController
{
    public function indexAction($type = null)
    {
        $groupArray = getSM()->get('gallery_table')->getGroupsArray();
        if (!$type)
            $type = $this->params()->fromRoute('type');
        $grid = new DataGrid('gallery_item_table');
        $grid->getSelect()->where(array('type' => $type));
        $grid->route = 'admin/' . $type . '/item';
        $grid->routeParams = array('type' => $type);
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
       // $url = new Column('url', 'Url');
        $order = new Column('order', 'Order', array('headerAttr' => array('width' => '80px', 'align' => 'center'), 'attr' => array('align' => 'center')));
        $order->hasTextFilter = false;
        $hits = new Column('hits', 'Hits', array('headerAttr' => array('width' => '80px', 'align' => 'center'), 'attr' => array('align' => 'center')));
        $hits->hasTextFilter = false;
        $appHits = new Column('appHits', 'gallery_items_software_hits', array('headerAttr' => array('width' => '80px', 'align' => 'center'), 'attr' => array('align' => 'center')));
        $appHits->hasTextFilter = false;
        $alt = new Column('alt', 'Alt Text');
        $title = new Column('title', 'Title');
        $groupId = new Column('groupId', 'groupId');
        $groupId->visible = false;

        $image = new Custom('image', 'Image', function (Column $col) {
            $fileType = $col->dataRow->fileType;
            if (strpos($fileType, 'flash') != true) {
                $image = $col->dataRow->image;

                if ($image) {
                    if (strpos($fileType, 'gif') == true) {
                        $html = sprintf('<img src="%s" width="100" height="100" />', $image);
                    } else {
                        $image = getThumbnail()->resize($image, 100, 100);
                        $html = sprintf('<img src="%s" />', $image);
                    }
                } else
                    $html = '';
            } else
                $html = '<span class="flash-icon"></span>';
            return $html;
        }, array('attr' => array('align' => 'center')));

        $status = new Select('status', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $groupFillter = new Column('groupId', 'Groups');
        $groupFillter->selectFilterData = $groupArray;


        $edit = new EditButton();
        $delete = new DeleteButton();
        $grid->addColumns(array($id, $image/*, $url*/, $alt, $title, $groupId, $order, $hits, $appHits, $status, $edit, $delete));
        $grid->setSelectFilters(array($groupFillter));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('gallery/gallery-item/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
            'type' => $type,
        ));
        return $this->viewModel;

    }

    public function newAction($model = false)
    {
        ini_set('memory_limit', 64 * 1024 * 1024);
        $oldImage = '';
        $galleryItemId = '';
        $type = $this->params()->fromRoute('type');

        $groupSelect = getSM()->get('gallery_table')->getGroupsArray(array('type' => $type));
        $form = new \Gallery\Form\GalleryItem($type, $groupSelect);


        if (!$model) {
            $model = new \Gallery\Model\GalleryItem();
            $form->setAttribute('action', url('admin/' . $type . '/item/new', array('type' => $type)));
        } else {
            $form->setAttribute('action', url('admin/' . $type . '/item/edit', array('type' => $type, 'id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
            $oldImage = $model->image;
            if ($form->getInputFilter()->has('image')) {
                $form->getInputFilter()->get('image')->setRequired(false);
            }
            $galleryItemId = $model->id;
        }

        $form->bind($model);

        if ($this->request->isPost()) {
            $post = array_merge(
                $this->request->getPost()->toArray(),
                $this->request->getFiles()->toArray()
            );


            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction($type);
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($post);

                if ($form->isValid()) {

                    $post = (array)$form->getData();

                    $imgFile = $model->image;
                    if (!empty($imgFile['name'])) {
                        $model->image = File::MoveUploadedFile($imgFile['tmp_name'], PUBLIC_FILE . '/Gallery/' . $type, $imgFile['name']);
                        $model->fileType = $imgFile['type'];
                        if ($oldImage)
                            @unlink(PUBLIC_PATH . $oldImage);
                    } else
                        $model->image = $oldImage;

                    $model->type = $type;

                    if ($type == 'banner') {
                        $size = array(
                            'width' => 50,
                            'height' => 50,
                        );
                        $selectGallery = getSM('gallery_table')->get($model->groupId);
                        if ($selectGallery->position)
                            $size = getSM('banner_size_table')->getSize($selectGallery->position);

                        $model->width = $size['width'];
                        $model->height = $size['height'];
                    }
                    $id = getSM()->get('gallery_item_table')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction($type);
                    } else
                        $form->setData(array());
                } else {
                    $this->formHasErrors();
                }
            }
        }


        $this->viewModel->setTemplate('gallery/gallery-item/new');
        $this->viewModel->setVariables(array(
            'form' => $form,
            'oldImage' => $oldImage,
            'galleryItemId' => $galleryItemId,
            'type' => $type,
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('gallery_item_table')->get($id);
        return $this->newAction($model);
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $field && has_value($value)) {
                if ($field == 'status') {
                    $this->getServiceLocator()->get('gallery_item_table')->update(array($field => $value), array('id' => $id));
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
                $select = $this->getServiceLocator()->get('gallery_item_table')->get($id);
                foreach ($select as $row)
                    if (isset($row->image) && $row->image)
                        @unlink(PUBLIC_PATH . $row->image);
                $this->getServiceLocator()->get('gallery_item_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function bannerLoaderAction()
    {
        $data = $this->params()->fromPost();
        if (empty($data))
            $data = $this->params()->fromQuery();
        $hitsType = 'app';
        if (isset($data['hitsType']) && $data['hitsType'])
            $hitsType = $data['hitsType'];
        $selectGallery = getSM()->get('gallery_table')->get($data['groupId']);
        $selectGallery->config = unserialize($selectGallery->config);
        $getBannerImage = new \Gallery\API\GetBannerImage();
        $select = $getBannerImage->getBannerImage($data['groupId'], $data['type'], $data['displayType']);
        $dataArray[] = (array)$select;
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('gallery/gallery/banner');
        $view->setVariables(array(
            'select' => $dataArray,
            'selectGallery' => $selectGallery,
            'pageType' => 1, // baraye inke dobare script load nashavad
            'type' => $data['type'],
            'hitsType' => $hitsType,
        ));
        return $view;
    }

    public function galleryCounterAction()
    {
        $id = $this->params()->fromRoute('id');
        $hitsType = $this->params()->fromRoute('hitsType');
        $type = $this->params()->fromPost('type');
        $select = getSM()->get('gallery_item_table')->updateBannerItemHit($id, $hitsType);
        if (!$type) { //agara az safhe photo gallery amad redirect nashavad
            if ($select->url)
                return $this->redirect()->toUrl($select->url);
            else
                return $this->redirect()->toRoute('app/front-page');
        } else
            return new JsonModel(array(
                'status' => 1,
            ));
    }

    public function deleteImgAction()
    {
        $itemId = $this->params()->fromPost('itemId');
        if ($itemId) {
            $selectItem = getSM('gallery_item_table')->get($itemId);
            if ($selectItem) {
                $image = $selectItem->image;
                @unlink(ROOT . $image);
                getSM('gallery_item_table')->update(array('image' => null, 'alt' => null), array('id' => $itemId));
                return new JsonModel(array(
                    'status' => 1
                ));
            }
        }
        return new JsonModel(array(
            'status' => 0
        ));
    }

}
