<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace OrgChart\Controller;

use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use OrgChart\Form\Config;
use OrgChart\Model\OrgChartTable;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;

class ChartAdmin extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('org_chart_table');
        $grid->route = 'admin/org-chart';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $title = new Column('name', 'Name');
        $description = new Column('description', 'Description');
        $status = new Select('status', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $description, $title, $status, $edit, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('OrgChart_NEWCHART');

        $this->viewModel->setTemplate('org-chart/chart-admin/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
    }

    public function newAction($model = null)
    {

        $fields_list = $this->getFieldsTable()->getArray('user_profile');
        $form = new \OrgChart\Form\OrgChart($fields_list);
        if (!$model) {
            $model = new \OrgChart\Model\OrgChart();
            $form->setAction(url('admin/org-chart/new'));
        } else {
            $form->setAction(url('admin/org-chart/edit', array('id' => $model->id)));
            $form = prepareForm($form, array('submit-new'));
            $model->config = unserialize($model->config);
        }
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($post);
                if ($form->isValid()) {
                    if (isset($post['config']))
                        $model->config = serialize($post['config']);
                    $id = $this->getTable()->save($model);
                    db_log_info("New Chart Created Successfully. id = " . $id . "");
                    $this->flashMessenger()->addSuccessMessage('New Chart Created Successfully.');
                    return $this->indexAction();
                } else
                    $this->formHasErrors();
            } elseif (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit-new'])) {
                $model = new \OrgChart\Model\OrgChart();
                $form->bind($model);
            }
        }

        $this->viewModel->setTemplate('org-chart/chart-admin/new');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $model = $this->getTable()->get($id);
            if ($model)
                return $this->newAction($model);
        }
        return $this->invalidRequest('admin/org-chart');
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);

            if ($id && $field && has_value($value)) {
                $this->getTable()->update(array($field => $value), array('id' => $id));
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getTable()->remove($id);
                getSM('chart_node_table')->delete(array('chartId'=>$id));
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    public function configAction()
    {
        /* @var $config Config */
        $config = getConfig('OrgChart');
        $form = prepareConfigForm(new \OrgChart\Form\Config());
        $form->setData($config->varValue);


        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getConfigTable()->save($config);
                    db_log_info("Org Chart Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Org Chart configs saved successfully');
                }
            }
        }
        $this->viewModel->setTemplate('org-chart/chart-admin/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function chartListAction()
    {
        $term = $this->params()->fromQuery('term');
        $data = $this->getTable()->search($term);
        $json = array();
        foreach ($data as $row) {
            $json[] = array(
                'chartId' => $row->id,
                'title' => $row->name,
            );
        }
        return new JsonModel($json);
    }

    /**
     * @return OrgChartTable
     */
    private function getTable()
    {
        return getSM('org_chart_table');
    }
}
