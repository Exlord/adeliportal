<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace GeographicalAreas\Controller;

use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use System\Controller\BaseAbstractActionController;
use GeographicalAreas\Form;
use GeographicalAreas\Model;
use Zend\View\Model\JsonModel;

class CountryController extends BaseAbstractActionController
{

    public function indexAction()
    {
        $grid = new DataGrid('country_table');
        $grid->route = 'admin/geographical-areas/country';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $title = new Column('countryTitle', 'Title');

        $status = new Select('itemStatus', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $showState = new Button('State', function (Button $col) {
            $col->route = 'admin/geographical-areas/state';
            $col->routeOptions['query'] = array('grid_filter_countryId' => $col->dataRow->id);
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('grid_button', 'search_button', 'ajax_page_load'))
        ));

        $edit = new EditButton();
        $delete = new DeleteButton();

        $grid->addColumns(array($id, $title, $status, $showState, $edit, $delete));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('geographical-areas/country/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $form = new Form\Country();

        if (!$model) {
            $model = new Model\Country();
            $form->setAttribute('action', url('admin/geographical-areas/country/new'));
        } else {
            $form->setAttribute('action', url('admin/geographical-areas/country/edit', array('id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
        }
        $form->setAttribute('data-cancel', url('admin/geographical-areas/country'));

        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {

                $form->setData($post);

                if ($form->isValid()) {

                    $id = getSM()->get('country_table')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction();
                    }
                }
            }
        }

        $this->viewModel->setTemplate('geographical-areas/country/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('country_table')->get($id);
        $this->viewModel->setTemplate('geographical-areas/country/new');
        return $this->newAction($model);
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $field && has_value($value)) {
                if ($field == 'itemStatus') {
                    $this->getServiceLocator()->get('country_table')->update(array($field => $value), array('id' => $id));
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
                $this->getServiceLocator()->get('country_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

}
