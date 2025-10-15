<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Form\Alias;
use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use System\Controller\BaseAbstractActionController;
use \Application\Model;
use Zend\View\Model\JsonModel;

class AliasController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('alias_url_table');
        $grid->route = 'admin/alias';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $url = new Column('url', 'Url', array('attr' => array('class' => 'left-align')));
        $alias = new Column('alias', 'Alias', array('attr' => array('class' => 'left-align')));

        $edit = new EditButton();
        $delete = new DeleteButton();

        $grid->addColumns(array($id, $url, $alias, $edit, $delete));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();
        $this->viewModel->setTemplate('application/alias/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        if (!$model) {
            $form = new Alias();
            $model = new Model\AliasUrl();
            $form->setAttribute('action', url('admin/alias/new'));
        } else {
            $form = new Alias($model->alias);
            $form->setAttribute('action', url('admin/alias/edit', array('id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
        }

        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($post);
                if ($form->isValid()) {
                    $id = getSM()->get('alias_url_table')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    db_log_info("new alias url with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction();
                    }
                }
            }
        }

        $this->viewModel->setTemplate('application/alias/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('alias_url_table')->get($id);
        return $this->newAction($model);
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getServiceLocator()->get('alias_url_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }
}
