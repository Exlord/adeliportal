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
use GeographicalAreas\Model\State;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;
use GeographicalAreas\Form;
use GeographicalAreas\Model;

class CityController extends BaseAbstractActionController
{

    public function indexAction()
    {
        $state = new State();
        $state->id = 0;
        $state_id = $this->params()->fromQuery('grid_filter_stateId', 0);
        if ($state_id) {
            $state = $this
                ->getServiceLocator()
                ->get('state_table')->get($state_id);
            $country = $this
                ->getServiceLocator()
                ->get('country_table')->get($state->countryId);
        } else {
            $country = new Model\Country();
            $country->id = 0;
            $country_id = $this->params()->fromQuery('grid_filter_countryId', 0);
            if ($country_id) {
                $country = $this
                    ->getServiceLocator()
                    ->get('country_table')->get($country_id);
            }
        }

        $states = $this
            ->getServiceLocator()
            ->get('state_table')->getArray();

        $grid = new DataGrid('city_table');
        $grid->route = 'admin/geographical-areas/city';
        if ($state_id)
            $grid->routeParams = array('stateId' => $state_id);
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $title = new Column('cityTitle', 'Title');

        $status = new Select('itemStatus', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $showArea = new Button('GEO_AREA', function (Button $col) {
            $col->route = 'admin/geographical-areas/area';
            $col->routeOptions['query'] = array('grid_filter_cityId' => $col->dataRow->id);
            $col->icon = 'glyphicon glyphicon-th-list';
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('btn', 'btn-default', 'ajax_page_load'))
        ));

        $edit = new EditButton();
        $delete = new DeleteButton();

        $groupState = new Column('stateId', 'State');
        $groupState->selectFilterData = $states;

        $grid->addColumns(array($id, $title, $status, $showArea, $edit, $delete));
        $grid->setSelectFilters(array($groupState));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('geographical-areas/city/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
            'country' => $country,
            'state' => $state,
        ));
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $states = $this
            ->getServiceLocator()
            ->get('state_table')->getArray();

        $form = new Form\City();

        if (!$model) {
            $state_id = $this->params()->fromRoute('stateId');
            $model = new Model\City();
            $model->stateId = $state_id;
            $form->setAttribute('action', url('admin/geographical-areas/city/new', array('stateId' => $state_id)));
        } else {
            $form->setAttribute('action', url('admin/geographical-areas/city/edit', array('stateId' => $model->stateId, 'id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
            $state_id = $model->stateId;
        }
        $form->setAttribute('data-cancel', url('admin/geographical-areas/city', array('stateId' => $state_id), array('query' => array('grid_filter_stateId' => $state_id))));

        $form->bind($model);
        $form->get('stateId')->setValueOptions($states);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['cancel'])) {
                return $this->redirect()->toRoute('admin/geographical-areas/city', array('stateId' => $state_id), array('query' => array('grid_filter_stateId' => $state_id)));
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {

                $form->setData($post);

                if ($form->isValid()) {

                    $id = getSM()->get('city_table')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->redirect()->toRoute('admin/geographical-areas/city', array('stateId' => $state_id), array('query' => array('grid_filter_stateId' => $state_id)));
                    }
                }
            }
        }

        $this->viewModel->setTemplate('geographical-areas/city/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('city_table')->get($id);
        $this->viewModel->setTemplate('geographical-areas/city/new');
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
                    $this->getServiceLocator()->get('city_table')->update(array($field => $value), array('id' => $id));
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
                $this->getServiceLocator()->get('city_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function getListAction()
    {
        $state = $this->params('stateId', false);
        $data = array();
        if ($state) {
            $data = $this
                ->getServiceLocator()
                ->get('city_table')
                ->getArray($state);
        }
        $this->viewModel->setTerminal(true);

        $this->viewModel->setTemplate('geographical-areas/city/get-list');
        $this->viewModel->setVariables(
            array(
                'list' => $data,
            )
        );
        return $this->viewModel;
    }

}
