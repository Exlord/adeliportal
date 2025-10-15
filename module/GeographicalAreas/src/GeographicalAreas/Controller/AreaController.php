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

class AreaController extends BaseAbstractActionController
{

    public function indexAction()
    {
        $where = array();
        $parentId = $this->params('parentId', 0);
        $where['parentId'] = $parentId;
        $city = new Model\City();
        $city->id = 0;

        $city_id = $this->params()->fromQuery('grid_filter_cityId', 0);
        if ($city_id) {
            $city = $this
                ->getServiceLocator()
                ->get('city_table')->get($city_id);
            $state = $this
                ->getServiceLocator()
                ->get('state_table')->get($city->stateId);
        } else {
            $state = new Model\State();
            $state->id = 0;
            $state_id = $this->params()->fromQuery('grid_filter_stateId', 0);
            if ($state_id) {
                $state = $this
                    ->getServiceLocator()
                    ->get('state_table')->get($state_id);
            }
        }

        $cities = $this
            ->getServiceLocator()
            ->get('city_table')->getArray();

        if ($city_id)
            $where['cityId'] = $city_id;
        $grid = new DataGrid('city_area_table');
        $grid->getSelect()->where($where)->order('id ASC');
        $grid->route = 'admin/geographical-areas/area';
        $grid->routeParams = array('cityId' => $city_id, 'parentId' => $parentId);
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $title = new Column('areaTitle', 'Title');

        $status = new Select('itemStatus', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $view = new Button('Items', function (Button $col) {
            $col->route = 'admin/geographical-areas/area';
            $col->routeParams = array('cityId' => $col->dataRow->cityId, 'parentId' => $col->dataRow->id);
            $col->icon = 'glyphicon glyphicon-th-list text-info';
        }, array(
            'headerAttr' => array('width' => '34px', 'title' => t('View this items childes')),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('ajax_page_load', 'btn', 'btn-default'))
        ));

        $edit = new EditButton();
        $delete = new DeleteButton();

        $groupState = new Column('cityId', 'City');
        $groupState->selectFilterData = $cities;

        $grid->addColumns(array($id, $title, $status, $view, $edit, $delete));
        $grid->setSelectFilters(array($groupState));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('geographical-areas/area/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
            'state' => $state,
            'city' => $city,
        ));
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $cites = $this
            ->getServiceLocator()
            ->get('city_table')->getArray();

        $parentId = $this->params('parentId', 0);
        $city_id = $this->params()->fromRoute('cityId');
        $areaParentId = getSM('city_area_table')->getArray($city_id, 0);
        $form = new Form\Area($areaParentId);

        if (!$model) {
            $model = new Model\CityArea();
            $model->cityId = $city_id;
            $form->setAttribute('action', url('admin/geographical-areas/area/new', array('cityId' => $city_id, 'parentId' => $parentId)));
        } else {
            $form->setAttribute('action', url('admin/geographical-areas/area/edit', array('cityId' => $model->cityId, 'parentId' => $model->parentId, 'id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
            $city_id = $model->cityId;
        }
        $form->setAttribute('data-cancel', url('admin/geographical-areas/area', array('cityId' => $city_id, 'parentId' => $parentId), array('query' => array('grid_filter_cityId' => $city_id))));

        $form->bind($model);
        $form->get('cityId')->setValueOptions($cites);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['cancel'])) {
                return $this->redirect()->toRoute('admin/geographical-areas/area', array('cityId' => $city_id, 'parentId' => $parentId), array('query' => array('grid_filter_cityId' => $city_id)));
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {

                $form->setData($post);

                if ($form->isValid()) {

                    if (!$model->parentId && !$parentId)
                        $model->parentId = 0;
                    if ($parentId && !$model->parentId)
                        $model->parentId = $parentId;
                    $id = getSM()->get('city_area_table')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->redirect()->toRoute('admin/geographical-areas/area/new', array('cityId' => $city_id, 'parentId' => $parentId));
                    }
                }
            }
        }

        $this->viewModel->setTemplate('geographical-areas/area/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('city_area_table')->get($id);
        $this->viewModel->setTemplate('geographical-areas/area/new');
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
                    $this->getServiceLocator()->get('city_area_table')->update(array($field => $value), array('id' => $id));
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
                $this->getServiceLocator()->get('city_area_table')->remove($id);
                $this->getServiceLocator()->get('city_area_table')->removeByParentId($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function getListAction()
    {
        $city = $this->params('cityId', false);
        $data = array();
        if ($city) {
            $data = $this
                ->getServiceLocator()
                ->get('city_area_table')
                ->getArray($city, 0);
        }

        $this->viewModel->setTerminal(true);
        $this->viewModel->setTemplate('geographical-areas/area/get-list');
        $this->viewModel->setVariables(array('list' => $data,));
        return $this->viewModel;
    }

    public function getSubListAction()
    {
        $areaId = $this->params()->fromRoute('areaId', false);
        $data = array();
        if ($areaId) {
            $data = $this
                ->getServiceLocator()
                ->get('city_area_table')
                ->getSubArray($areaId);
        }

        $this->viewModel->setTerminal(true);
        $this->viewModel->setTemplate('geographical-areas/area/get-list');
        $this->viewModel->setVariables(array('list' => $data,));
        return $this->viewModel;
    }

}
