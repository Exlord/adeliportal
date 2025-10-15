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
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use Gallery\Model\BannerSize;
use System\Controller\BaseAbstractActionController;
use Theme\API\Themes;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class BannerSizeController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('banner_size_table');
        $grid->route = 'admin/banner/size';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $width = new Column('width', 'Width');
        $height = new Column('height', 'Height');
        $price = new Column('price', 'Price');
        $addPrice = new Column('addPrice', 'Per price addition image');

        $position = new Select('position', 'Position', Themes::getBlockPositions(),
            array(), array('headerAttr' => array('width' => '50px')));

        $status = new Select('status', 'Status',
            array('0' => t('Inactive'), '1' => t('Active')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px')));

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $width, $height, $price,$addPrice,$position, $status, $edit, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('GALLERY_BANNER_SIZE_NEW');

        $this->viewModel->setTemplate('gallery/banner-size/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        $form = new \Gallery\Form\BannerSize(Themes::getBlockPositions());
        if (!$model) {
            $model = new \Gallery\Model\BannerSize();
            $form->setAction(url('admin/banner/size/new'));
        } else {
            $form->setAction(url('admin/banner/size/edit', array('id' => $model->id)));
            $form = prepareForm($form, array('submit-new'));
        }
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($post);
                if ($form->isValid()) {
                    $this->getTable()->save($model);
                    $this->flashMessenger()->addSuccessMessage('New Banner Size Created Successfully.');
                    return $this->indexAction();
                } else
                    $this->formHasErrors();
            } elseif (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit-new'])) {
                $model = new \Gallery\Model\BannerSize();
                $form->bind($model);
            }
        }

        $this->viewModel->setTemplate('gallery/banner-size/new');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $model = $this->getTable()->get($id);
            if ($model)
                return $this->newAction($model);
        }
        return $this->invalidRequest('admin/banner/size');
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);

            if ($id && $field && has_value($value)) {
                if ($field == 'status') {
                    $this->getTable()->update(array($field => $value), array('id' => $id));
                    return new JsonModel(array('status' => 1));
                }
                if ($field == 'position') {
                    $this->getTable()->update(array($field => $value), array('id' => $id));
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
                $this->getTable()->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    /**
     * @return BannerSize
     */
    private function getTable()
    {
        return getSM('banner_size_table');
    }
}
