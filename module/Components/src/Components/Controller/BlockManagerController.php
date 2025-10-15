<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Components\Controller;

use Components\Form\NewBlock;
use Components\Model\Block;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use System\Controller\BaseAbstractActionController;
use Theme\API\Themes;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;

class BlockManagerController extends BaseAbstractActionController
{
    /**
     * @return array
     */
    private function getThemeSettings()
    {
        return Themes::getClientThemeConfig();
    }

    public function indexAction()
    {
//        $theme = $this->getThemeSettings();
        $grid = new DataGrid('block_table');
        $grid->route = 'admin/block';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $title = new Column('title', 'Title');
        $description = new Column('description', 'Description');

        $components = getSM('Config');
        $components = $components['components'];
        $type = new Custom('type', 'Type', function (Column $col) use ($components) {
            $typeName = $col->dataRow->type;
            if (isset($components[$typeName]['label']))
                return t($components[$typeName]['label']);
            else
                return $typeName;
        });

        $positions = array('' => t('-- Select --')) + Themes::getBlockPositions();
        $position = new Select('position', 'Position', $positions,
            array(), array('headerAttr' => array('width' => '50px')));

        $status = new Select('enabled', 'Status',
            array('0' => t('Inactive'), '1' => t('Active')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px')));

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $description, $title, $type, $position, $status, $edit, $del));
        $grid->addNewButton('New Block');
        $grid->addDeleteSelectedButton();
        $grid->addButton('COMPONENT_DISABLE_ALL', 'COMPONENT_DISABLE_ALL', '/update', false,
            array('ajax_page_load', 'btn-default'), false, '', array(), array('query' => array('disableAll' => 1)), array(), 'glyphicon glyphicon-eye-close');

        $this->viewModel->setTemplate('components/block-manager/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
    }

    public function newAction($block = null)
    {
        $patchSamples = $this->render('components/block-manager/patch-examples');
//        $domainSamples = $this->render('application/domain/domain-examples');
        if ($block)
            $type = $block->type;
        else
            $type = $this->params()->fromRoute('type', false);

        if (!$type) {
            $components = getSM('block_api')->LoadBlockTypes();
            $this->viewModel->setVariables(array('components' => $components));
            $this->viewModel->setTemplate('components/block-manager/new');
        } else {
            $blockInfo = \Components\API\Block::getBlockInfo($type);
            $label = $blockInfo['label'];
//                $themeSet = $this->getThemeSettings();
            $form = new NewBlock($type, Themes::getBlockPositions(), $patchSamples);
            $form = prepareForm($form, array('submit-new'));

            if (!$block) {
                $form->setAction(url('admin/block/new', array('type' => $type)));
                $block = new Block();
                $block->type = $type;
            } else {
                $form->setAction(url('admin/block/edit', array('id' => $block->id)));
            }
            $form->bind($block);

            if ($this->request->isPost()) {
                $post = $this->request->getPost();
                if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new']) || isset($post['buttons']['submit-close'])) {
                    $form->setData($post);
                    if ($form->isValid()) {
                        $block->serializeData();
                        $this->getBlockTable()->save($block);
                        $this->flashMessenger()->addSuccessMessage('Block Saved Successfully.');

                        if (isset($post['buttons']['submit-close']))
                            return $this->indexAction();
                        elseif (isset($post['buttons']['submit-new'])) {
                            $block = new Block();
                            $block->type = $type;
                            $form->bind($block);
                        }
                    } else
                        $this->formHasErrors();

                } elseif (isset($post['buttons']['cancel'])) {
                    return $this->indexAction();
                }
            }

            $this->viewModel->setTemplate('components/block-manager/new-block');
            $this->viewModel->setVariables(array('form' => $form, 'label' => $label));
        }
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->invalidRequest('admin/block');

//        $patchSamples = $this->render('components/block-manager/patch-examples');
////        $domainSamples = $this->render('application/domain/domain-examples');
//        /* @var $block Block */
        $block = $this->getBlockTable()->get($id);
        $block->unserializeData();
        return $this->newAction($block);
//        $blockInfo = \Components\API\Block::getBlockInfo($block->type);
//        $label = $blockInfo['label'];
////            $themeSet = $this->getThemeSettings();
//        $form = new NewBlock($block->type, Themes::getBlockPositions(), $patchSamples);
//        $form->setAction(url('admin/block/edit', array('id' => $id)));
//        $form = prepareForm($form, array('submit-new'));
//
//        $form->bind($block);
//
//        if ($this->request->isPost()) {
//            $post = $this->request->getPost();
//            if (isset($post['buttons']['submit'])) {
//                $form->setData($post);
//                if ($form->isValid()) {
//                    $block->serializeData();
//                    $this->getBlockTable()->save($block);
//                    $this->flashMessenger()->addSuccessMessage('Block edited Successfully.');
//                    return $this->indexAction();
//                } else
//                    $this->formHasErrors();
//            } elseif (isset($post['buttons']['cancel'])) {
//                return $this->indexAction();
//            }
//        }
//
//        $this->viewModel->setTemplate('components/block-manager/new-block');
//        $this->viewModel->setVariables(array('form' => $form, 'label' => $label));
//        return $this->viewModel;
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                if ($field && has_value($value)) {
                    if (in_array($field, array('enabled', 'position'))) {
                        getSM('block_table')->update(array($field => $value), array('id' => $id));
                        return new JsonModel(array('status' => 1));
                    }
                }
            }
        } elseif ($this->params()->fromQuery('disableAll')) {
            $disableAll = (int)$this->params()->fromQuery('disableAll');
            if ($disableAll) {
                getSM('block_table')->update(array('enabled' => 0));
                return $this->indexAction();
            }
        }

        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $ids = array();
                $locked = array();
                $block = $this->getBlockTable()->getAll(array('id' => $id));
                foreach ($block as $row) {
                    if (isset($row->locked) && $row->locked == '1')
                        $locked[] = $row->id;
                    else
                        $ids[] = $row->id;
                }
                $lockedMsg = 'The selected blocks are locked and cannot be deleted.';
                if (count($ids)) {
                    getSM('block_table')->remove($ids);
                    if (count($locked))
                        return new JsonModel(array('status' => 1, 'msg' => t($lockedMsg)));
                    else
                        return new JsonModel(array('status' => 1));
                } else
                    return new JsonModel(array('status' => 0, 'msg' => t($lockedMsg)));
            }
        }
        return $this->unknownAjaxError();
    }

    /**
     * @return \Components\Model\BlockTable
     */
    private function getBlockTable()
    {
        return getSM('block_table');
    }
}
