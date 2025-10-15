<?php
namespace Contact\Controller;

use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use File\API\File;
use System\Controller\BaseAbstractActionController;
use Theme\API\Themes;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class ContactUserAdminController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $groupArray = getSM()->get('category_item_table')->getItemsTreeByMachineName('contact');
        $grid = new DataGrid('contact_user_table');
        $grid->route = 'admin/contact/user';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $name = new Column('name', 'Name');
        $email = new Column('email', 'Email');
        $mobile = new Column('mobile', 'Mobile');
        $phone = new Column('phone', 'Phone');
        $fax = new Column('fax', 'Fax');
        $role = new Column('role', 'Role');
        $status = new Select('status', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $groupFillter = new Column('catId', 'Groups');
        $groupFillter->selectFilterData = $groupArray;

        $edit = new EditButton();
        $delete = new DeleteButton();
        $grid->addColumns(array($id, $name, $email, $mobile, $phone, $fax, $role, $status, $edit, $delete));
        $grid->setSelectFilters(array($groupFillter));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('contact/contact-user-admin/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;

    }

    public function newAction($model = null, $typeArray = null)
    {
        $contactUserId = '';
        $countType = 1;
        $groupArray = getSM()->get('category_item_table')->getItemsTreeByMachineName('contact');
        if ($typeArray)
            $countType = count($typeArray);
        $form = new \Contact\Form\ContactUser($groupArray, $countType);
        $google = '';
        if (!$model) {
            $form->setAttribute('action', url('admin/contact/user/new'));
            $model = new \Contact\Model\ContactUser();
        } else {
            $contactUserId = $model->id;
            $form->setAttribute('action', url('admin/contact/user/edit', array('id' => $contactUserId)));
            $form->get('buttons')->remove('submit-new');
            $typeArray = array('select' => array('select' => $typeArray));
            $form->setData($typeArray);
            $google = $model->google;
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
                    if ($contactUserId) // edit page
                        getSM('contact_type_table')->removeAllTypeByContactUser($contactUserId); //avval tamame type ghabli ra delete mikonad va jadidha ra zakhire mikonad

                    $id = getSM()->get('contact_user_table')->save($model);
                    if ($id) // new page
                        $contactUserId = $id;
                    $typeArray = array();
                    if (is_array($post->select['select']) && count($post->select['select']) > 0) {
                        foreach ($post->select['select'] as $row) {
                            if (!empty($row['selectName'])) {
                                $contactType = new \Contact\Model\ContactType();
                                $contactType->title = $row['selectName'];
                                $contactType->contactUserId = $contactUserId;
                                $typeArray[] = $contactType;
                            }
                        }
                    }
                    getSM()->get('contact_type_table')->multiSave($typeArray);
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new user contact with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction();
                    }
                } else
                    $this->formHasErrors();
            }
        }

        $this->viewModel->setTemplate('contact/contact-user-admin/new');
        $this->viewModel->setVariables(array(
            'form' => $form,
            'google' => $google,
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM('contact_user_table')->get($id);
        $typeArray = getSM('contact_type_table')->getArray($id);

        return $this->newAction($model, $typeArray);
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $field) {
                if ($field == 'status') {
                    getSM('contact_user_table')->update(array($field => $value), array('id' => $id));
                    return new JsonModel(array('status' => 1));
                }
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    public function deleteAction()
    { //TODO Remove All
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                getSM('contact_user_table')->remove($id);
                getSM('contact_type_table')->removeAllTypeByContactUser($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function menuContactUserListAction()
    {
        $term = $this->params()->fromQuery('term');
        $data = getSM('contact_user_table')->search($term);

        $json = array();
        foreach ($data as $row) {
            $json[] = array(
                'contactId' => $row->id,
                'title' => $row->name,
            );
        }
        return new JsonModel($json);
    }

    public function menuContactCategoryListAction()
    {
        $term = $this->params()->fromQuery('term');
        $data = getSM('contact_user_table')->searchCategoryList($term);

        $json = array();
        foreach ($data as $row) {
            $json[] = array(
                'catId' => $row->id,
                'title' => $row->itemName,
            );
        }
        return new JsonModel($json);
    }

}
