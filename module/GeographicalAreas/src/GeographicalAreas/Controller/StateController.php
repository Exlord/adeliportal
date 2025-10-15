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
use GeographicalAreas\Model\Country;
use System\Controller\BaseAbstractActionController;
use GeographicalAreas\Form;
use GeographicalAreas\Model;
use Zend\View\Model\JsonModel;

class StateController extends BaseAbstractActionController
{

    public function indexAction()
    {
        $countries = $this
            ->getServiceLocator()
            ->get('country_table')->getArray();

        $countryId = $this->params()->fromQuery('grid_filter_countryId', 0);
        $grid = new DataGrid('state_table');
        $grid->route = 'admin/geographical-areas/state';
        if ($countryId)
            $grid->routeParams = array('countryId' => $countryId);
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $title = new Column('stateTitle', 'Title');

        $status = new Select('itemStatus', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $showCity = new Button('City', function (Button $col) {
            $col->route = 'admin/geographical-areas/city';
            $col->routeOptions['query'] = array('grid_filter_stateId' => $col->dataRow->id);
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('grid_button', 'search_button', 'ajax_page_load'))
        ));

        $edit = new EditButton();
        $delete = new DeleteButton();

        $groupCountry = new Column('countryId', 'Country');
        $groupCountry->selectFilterData = $countries;

        $grid->addColumns(array($id, $title, $status, $showCity, $edit, $delete));
        $grid->setSelectFilters(array($groupCountry));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('geographical-areas/state/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $countries = $this
            ->getServiceLocator()
            ->get('country_table')->getArray();

        $form = new Form\State();

        if (!$model) {
            $country_id = $this->params()->fromRoute('countryId');
            $model = new Model\State();
            $model->countryId = $country_id;
            $form->setAttribute('action', url('admin/geographical-areas/state/new', array('countryId' => $country_id)));
        } else {
            $form->setAttribute('action', url('admin/geographical-areas/state/edit', array('countryId' => $model->countryId, 'id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
            $country_id = $model->countryId;
        }
        $form->setAttribute('data-cancel', url('admin/geographical-areas/state', array('countryId' => $country_id), array('query' => array('grid_filter_countryId' => $country_id))));

        $form->bind($model);
        $form->get('countryId')->setValueOptions($countries);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['cancel'])) {
                return $this->redirect()->toRoute('admin/geographical-areas/state', array('countryId' => $country_id), array('query' => array('grid_filter_countryId' => $country_id)));
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {

                $form->setData($post);

                if ($form->isValid()) {

                    $id = getSM()->get('state_table')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->redirect()->toRoute('admin/geographical-areas/state', array('countryId' => $country_id), array('query' => array('grid_filter_countryId' => $country_id)));
                    }
                }
            }
        }

        $this->viewModel->setTemplate('geographical-areas/state/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('state_table')->get($id);
        $this->viewModel->setTemplate('geographical-areas/state/new');
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
                    $this->getServiceLocator()->get('state_table')->update(array($field => $value), array('id' => $id));
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
                $this->getServiceLocator()->get('state_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function getListAction()
    {
        $countryId = $this->params('countryId', false);
        $data = array();
        if ($countryId) {
            $data = $this
                ->getServiceLocator()
                ->get('state_table')
                ->getArray($countryId);
        }
        $this->viewModel->setTerminal(true);

        $this->viewModel->setTemplate('geographical-areas/state/get-list');
        $this->viewModel->setVariables(
            array(
                'list' => $data,
            )
        );
        return $this->viewModel;
    }

}
