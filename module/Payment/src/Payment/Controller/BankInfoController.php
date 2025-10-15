<?php
namespace Payment\Controller;

use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;


class BankInfoController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('bank_info_table');
        $grid->route = 'admin/payment/bank-info';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $name = new Column('name', 'Bank Name');
        $userName = new Column('userName', 'User Name');
      //  $PassWord = new Column('passWord', 'PassWord');
        $terminalId = new Column('terminalId', 'Terminal Id');
        $status = new Select('status', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );
        $edit = new EditButton();
        $delete = new DeleteButton();
        $grid->addColumns(array($id, $name, $userName, $terminalId, $status, $edit, $delete));
        //  $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('payment/bank-info/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $form = new \Payment\Form\BankInfo();

        if (!$model) {
            $form->setAttribute('action', url('admin/payment/bank-info/new'));
            $model = new \Payment\Model\BankInfo();
        } else {
            $form->setAttribute('action', url('admin/payment/bank-info/edit', array('id' => $model->id)));
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

                    $id = getSM()->get('bank_info_table')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction();
                    }
                }
            }
        }

        $this->viewModel->setTemplate('payment/bank-info/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('bank_info_table')->get($id);
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
                    $this->getServiceLocator()->get('bank_info_table')->update(array($field => $value), array('id' => $id));
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
                $this->getServiceLocator()->get('bank_info_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }
}
