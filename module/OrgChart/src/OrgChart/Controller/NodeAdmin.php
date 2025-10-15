<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace OrgChart\Controller;

use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use OrgChart\Model\ChartNodeTable;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;

class NodeAdmin extends BaseAbstractActionController
{
    public function indexAction()
    {
        $chartId = $this->params('chartId', 0);
        $parentId = $this->params('parentId', 0);
        $grid = new DataGrid('chart_node_table');
        getSM('chart_node_table')->getNodeList($grid->getSelect(), $chartId, $parentId);
        $grid->route = 'admin/chart-node';
        $grid->routeParams = array('chartId' => $chartId, 'parentId' => $parentId);
        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $parentId = new Column('title', 'Title');

        $user = new Column('displayName', 'OrgChart_NODECHART');
        $user->setTableName('u');

        $chartName = new Column('name', 'OrgChart_CHARTNAME');
        $chartName->setTableName('ch');

        $status = new Select('status', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $view = new Button('Show', function (Button $col) {
            $col->route = 'admin/chart-node/items';
            $col->routeParams = array('chartId' => $col->dataRow->chartId, 'parentId' => $col->dataRow->id);
        }, array(
            'headerAttr' => array('width' => '34px', 'title' => t('View this items childes')),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('grid_button', 'search_button','ajax_page_load'))
        ));

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $user, $parentId, $chartName, $status, $view, $edit, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('OrgChart_NEWNODECHART');

        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        $chartId = getSM('org_chart_table')->getChart(2);
        $userId = getSM('user_table')->getUsers(2);
        $parentNode = getSM('chart_node_table')->getNode(2);
        $form = new \OrgChart\Form\ChartNode($userId,$parentNode, $chartId);
        if (!$model) {
            $model = new \OrgChart\Model\ChartNode();
            $form->setAction(url('admin/chart-node/new'));
        } else {
            $form->setAction(url('admin/chart-node/edit', array('id' => $model->id)));
            $form = prepareForm($form, array('submit-new'));
        }
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($post);
                if ($form->isValid()) {

                    $id = $this->getTable()->save($model);
                    db_log_info("New Chart Node Created Successfully. id = " . $id . "");
                    $this->flashMessenger()->addSuccessMessage('New Chart Node Created Successfully.');
                    return $this->indexAction();
                } else
                    $this->formHasErrors();
            } elseif (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit-new'])) {
                $model = new \OrgChart\Model\ChartNode();
                $form->bind($model);
            }
        }

        $this->viewModel->setTemplate('org-chart/node-admin/new');
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
        return $this->invalidRequest('admin/chart-node');
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
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    public function configAction()
    {
        /* @var $config Config */
        /* $config = getConfig('newsletter');
         $form = prepareConfigForm(new \RSS\Form\Config($tagId));
         $form->setData($config->varValue);


         if ($this->request->isPost()) {
             $post = $this->request->getPost();
             if (isset($post['buttons']['submit'])) {
                 $form->setData($this->request->getPost());
                 if ($form->isValid()) {
                     $config->setVarValue($form->getData());
                     $this->getConfigTable()->save($config);
                     db_log_info("Newsletter Configs changed");
                     $this->flashMessenger()->addSuccessMessage('Newsletter Text configs saved successfully');
                 }
             }
         }

         $this->viewModel->setVariables(array('form' => $form));
         return $this->viewModel;*/
    }

    public function getParentNodeAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            if (isset($data['chartId']) && $data['chartId']) {
                $select = $this->getTable()->getParentNodeArray($data['chartId']);

                if (!empty($select)) {
                    $this->viewModel->setTerminal(true);
                    $this->viewModel->setTemplate('org-chart/node-admin/get-parent-node');
                    $this->viewModel->setVariables(array('select' => $select));
                    $html = $this->render($this->viewModel);
                    return new JsonModel(array(
                        'status' => 1,
                        'html' => $html,
                    ));
                }
            }
        }
        return new JsonModel(array(
            'status' => 0
        ));
    }

    /**
     * @return ChartNodeTable
     */
    private function getTable()
    {
        return getSM('chart_node_table');
    }
}
