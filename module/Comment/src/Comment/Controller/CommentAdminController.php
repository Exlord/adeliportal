<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Comment\Controller;

use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;
use DataView\API\GridColumn;

class CommentAdminController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $entityTypeArray = getSM('comment_table')->getEntityType();
        $data = $this->params()->fromQuery();
        $grid = new DataGrid('comment_table');
        if (!isAllowed(\Comment\Module::ADMIN_COMMENT_ALL))
            $grid->getSelect()->where(array('userId' => current_user()->id));
        $grid->route = 'admin/comment';
        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '40px', 'align' => 'right'),
            'attr' => array('align' => 'right')
        ));
        $grid->setIdCell($id);
        $name = new Column('name', 'Name');
        $email = new Column('email', 'Email');
        $entityId = new Column('entityId', 'Post Id', array(
            'headerAttr' => array('width' => '40px', 'align' => 'right'),
            'attr' => array('align' => 'right')
        ));
        $entityType = new Custom('entityType', 'Post Type', function (Column $col) {
            return t($col->dataRow->entityType);
        });
        if (isset($data['title'])) {
            $entityId->visible = false;
            $entityType->visible = false;
            $pageTitle = $data['title'];
        } else
            $pageTitle = '';

        $created = new Custom('created', 'Date', function (Column $column) {
            $dateFormat = getSM()->get('viewhelpermanager')->get('DateFormat');
            return $dateFormat($column->dataRow->created);
        });
        $status = new Select('status', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

//        $showCommentIcon = new Custom('comment', 'Show', function (Column $col) {
//            return '<span data-tooltip="' . $col->dataRow->comment . '" class="show-comment-icon" ></span>';
//        }, array('headerAttr' => array('width' => '34px'), 'attr' => array('align' => 'center')));

        $commentPreview = new Button('Preview', function ($col) {
            $col->route = '#view-comment';
            $col->text = '';
            $col->icon = 'glyphicon glyphicon-eye-open';
            $col->contentAttr['data-tooltip'] = $col->dataRow->comment;
            $col->contentAttr['class'][] = 'btn-default';
        }, array('headerAttr' => array('width' => '34px'), 'attr' => array('align' => 'center')));

        $entityTypeFilter = new Column('entityType', 'Groups');
        $entityTypeFilter->selectFilterData = $entityTypeArray;


        $edit = new EditButton();
        $delete = new DeleteButton();
        $grid->addColumns(array($id, $name, $email, $created, $entityId, $entityType, $status, $commentPreview, $edit, $delete));
        $grid->setSelectFilters(array($entityTypeFilter));
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('comment/comment-admin/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
            'title' => $pageTitle,
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        if ($id) {
            $select = getSM('comment_table')->get($id);
            if (!isAllowed(\Comment\Module::ADMIN_COMMENT_EDIT_ALL))
                if (current_user()->id != $select->userId)
                    return $this->accessDenied();
            /* @var $commentTable  \Comment\Model\commentTable */
            $form = new \Comment\Form\CommentForm('edit');
            $form->setAttribute('action', $this->url()->fromRoute('admin/comment/edit', array('id' => $id)));
            $commentModel = new \Comment\Model\comment();
            $commentTable = getSM()->get('comment_table');
            if ($this->request->isPost()) {
                $form->bind($commentModel);
                $data = $this->request->getPost()->toArray();
                $form->setData($data);
                if ($form->isValid()) {
                    unset($data['csrf_comment_form']);
                    unset($data['submitEdit']);
                    $commentTable->save($data);
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Updated');
                    return $this->indexAction();
                } else
                    $this->flashMessenger()->addErrorMessage('Error : Noting Save');
            }
            $selectComment = $commentTable->get($id);
            $form->bind($selectComment);
            $this->viewModel->setTemplate('comment/comment/new');
            $this->viewModel->setVariables(array(
                'form' => $form
            ));
            return $this->viewModel;
        } else {
            $this->flashMessenger()->addSuccessMessage('Invalid Request !');
            return $this->indexAction();
        }
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $field && has_value($value)) {
                $select = getSM('comment_table')->get($id);
                if (!isAllowed(\Comment\Module::ADMIN_COMMENT_UPDATE_ALL))
                    if (current_user()->id != $select->userId)
                        return new JsonModel(array('status' => 0, 'msg' => t('You don\'t have required permissions to access to this page.')));
                if ($field == 'status') {
                    $this->getServiceLocator()->get('comment_table')->update(array('created' => time(), $field => $value), array('id' => $id));
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
                $select = getSM('comment_table')->get($id);
                if (!isAllowed(\Comment\Module::ADMIN_COMMENT_UPDATE_ALL))
                    if (current_user()->id != $select->userId)
                        return new JsonModel(array('status' => 0, 'msg' => t('You don\'t have required permissions to access to this page.')));
                $this->getServiceLocator()->get('comment_table')->remove($id);
                getSM('rating_table')->removeRate($select->id, $select->entityType);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function configAction()
    {
        /* @var $config Config */
        $config = $this->getServiceLocator()->get('config_table')->getByVarName('comment');
        $form = prepareConfigForm(new \Comment\Form\Config());
        $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Comment Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Comment configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('comment/comment-admin/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }
}
