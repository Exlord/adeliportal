<?php

namespace Contact\Controller;

use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class ContactController extends BaseAbstractActionController
{

    public function configAction()
    {
        /* @var $config Config */
        $config = getConfig('contact');
        $allUser = getSM('contact_user_table')->getArray();
        $form = prepareConfigForm(new \Contact\Form\Config($allUser));
        if ($config->varValue)
            $form->setData($config->varValue);
        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($post);
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Contact Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Contact configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('contact/contact/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function contactsAction()
    {
        $grid = new DataGrid('contact_table');
        //  if (!isAllowed(\Comment\Module::ADMIN_COMMENT_ALL))
        //      $grid->getSelect()->where(array('userId' => current_user()->id));
        $grid->route = 'admin/contact/contacts';
        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '40px', 'align' => 'right'),
            'attr' => array('align' => 'right')
        ));
        $grid->setIdCell($id);
        $name = new Column('name', 'Name');
        $email = new Column('email', 'Email');
        $mobile = new Column('mobile', 'Mobile');

        $date = new Date('date', 'Date');
        /* $status = new Select('status', 'Status',
             array('0' => t('Not Approved'), '1' => t('Approved')),
             array('0' => 'inactive', '1' => 'active'),
             array('headerAttr' => array('width' => '50px'))
         );*/

        $showCommentIcon = new Custom('description', 'Show', function (Column $col) {
            return '<span data-tooltip="' . $col->dataRow->description . '" class=" glyphicon glyphicon-envelope grid-icon text-primary" ></span>';
        }, array('headerAttr' => array('width' => '34px'), 'attr' => array('align' => 'center')));


        //  $edit = new EditButton();
        $delete = new DeleteButton();
        $grid->addColumns(array($id, $name, $email, $mobile, $date, $showCommentIcon, $delete));
        // $grid->setSelectFilters(array($entityTypeFilter));
        $grid->addNewButton('User', 'User', null, 'admin/contact/user');
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('contact/contact/contacts');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                getSM('contact_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

}
