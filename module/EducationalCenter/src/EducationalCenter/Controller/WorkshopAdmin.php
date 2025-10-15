<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace EducationalCenter\Controller;

use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Visualizer;
use EducationalCenter\Model\Workshop;
use EducationalCenter\Model\WorkshopTable;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;
use Zend\View\Model\JsonModel;

class WorkshopAdmin extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('ec_workshop_table');
        $grid->route = 'admin/educational-center/workshop';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $title = new Column('title', 'Title');
        $code = new Column('code', 'Workshop Code');

        $status = new Custom('status', 'Status',
            function (Custom $col) {
                $s = $col->dataRow->status;
                $title = '';
                $icon = '';

                if ($s == '0') {
                    $title = t('Disabled');
                    $icon = 'glyphicon glyphicon-remove-sign text-danger';
                } elseif ($s == '1') {
                    $title = t('Enabled');
                    $icon = 'glyphicon glyphicon-ok text-success';
                }

                $content = "<span title='{$title}' class='{$icon} grid-icon'></span>";


                $icon = '';
                $url = '#';
                $class = 'btn btn-default btn-xs';
                $title = '';
                $route = false;

                $routeParams = array('id' => $col->dataRow->id);
                if ($s == '0') {
                    $title = t('Click here to enable this workshop');
                    $icon = 'glyphicon glyphicon-ok text-primary';
                    $route = 'admin/educational-center/workshop/change-status';
                    $routeParams['status'] = 1;
                    $class .= ' ajax_page_load';
                } else {
                    $title = t('Click here to disable this workshop');
                    $icon = 'glyphicon glyphicon-remove-sign text-danger';
                    $route = 'admin/educational-center/workshop/change-status';
                    $routeParams['status'] = 0;
                    $class .= ' ajax_page_load';
                }
                $url = url($route, $routeParams);
                $content .= ' ' . Common::Link(
                        "<span class='$icon'></span>", $url,
                        array('class' => $class, 'title' => $title)
                    );


                return $content;
            },
            array(
                'contentAttr' => array('align' => 'center'), 'attr' => array('align' => 'center'),
                'headerAttr' => array('width' => '70px', 'align' => 'center'),
            ));

        $classes = new Button('Classes',
            function (Button $col) {
                $col->route = 'admin/educational-center/workshop/class';
                $col->routeParams['workshop'] = $col->dataRow->id;
                $col->icon = "glyphicon glyphicon-th-list text-primary";
                $col->contentAttr['class'] = array('ajax_page_load', 'btn', 'btn-default', 'btn-xs');
            },
            array(
                'contentAttr' => array('align' => 'center'), 'attr' => array('align' => 'center'),
                'headerAttr' => array('width' => '70px', 'align' => 'center'),
            )
        );

        $del = new DeleteButton();
        $edit = new EditButton();

        $grid->addColumns(array($id, $title, $code, $classes, $status, $edit, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('New Workshop');

        $grid->getSelect()
            ->join(array('ci' => 'tbl_category_item'), $grid->getTableGateway()->table . '.catId=ci.id', array('itemName'), 'LEFT');
        $grid->defaultSort = $id;
        $grid->defaultSortDirection = 'DESC';

        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        $this->viewModel->setTemplate('educational-center/workshop-admin/index');
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        $educatorUserRoles = array();
        $config = getConfig('educational-center');
        if (isset($config->varValue['educatorUserRole'])) {
            $educatorUserRoles = $config->varValue['educatorUserRole'];
        }

        if (!count($educatorUserRoles)) {
            $this->flashMessenger()->addErrorMessage('Educational center configs has not been initialized yet !');
            return $this->forward()->dispatch('EducationalCenter\Controller\EducationalCenter', array('action' => 'config'));
        }


        $form = new \EducationalCenter\Form\Workshop();
        if (!$model) {
            $model = new Workshop();
            $form->setAction(url('admin/educational-center/workshop/new'));
            $form = prepareForm($form, array('submit-close'));
        } else {
            $form->setAction(url('admin/educational-center/workshop/edit', array('id' => $model->id)));
            $form = prepareForm($form, array('submit-new'));
        }
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            if ($this->isSubmit()) {
                $form->setData($post);
                if ($form->isValid()) {
                    $this->getTable()->save($model);
                    $this->flashMessenger()->addSuccessMessage('New Workshop created successfully');
                    if ($this->isSubmitAndClose())
                        return $this->indexAction(); //TODO if not edit redirect to workshop class new
                    elseif ($this->isSubmitAndNew()) {
                        $model = new Workshop();
                        $form->bind($model);
                    } elseif (!$model)
                        return $this->indexAction();
                    //TODO if not edit redirect to workshop class new
                } else
                    $this->formHasErrors();

            } elseif ($this->isCancel()) {
                return $this->indexAction();
            }
        }

        $this->viewModel->setTemplate('educational-center/workshop-admin/new');
        $this->viewModel->setVariables(array('form' => $form, 'educatorUserRoles' => $educatorUserRoles, 'model' => $model));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $model = $this->getTable()->get($id);
            if ($model)
                return $this->newAction($model->current());
        }
        return $this->invalidRequest('admin/educational-center/workshop');
    }

    public function changeStatusAction()
    {
        $status = (int)$this->params()->fromRoute('status', false);
        $id = $this->params()->fromRoute('id', 0);

        if ($id && $status !== false) {
            $workshop = $this->getTable()->getItem($id);
            $allowChange = false;
            if ($workshop->status == '0' && $status == 1) {
                $allowChange = true;
                //TODO notify workshop is enabled
                //TODO what to do with active classes
            }
            if ($workshop->status == '1' && $status == 0) {
                $allowChange = true;
                //TODO notify workshop disabled
                //TODO what to do with active classes
            }
            if ($allowChange) {
                $this->getTable()->update(array('status' => $status), array('id' => $id));
                $this->flashMessenger()->addSuccessMessage(t('Workshop status changed successfully'));
                return $this->indexAction();
            }
        }

        $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
        return $this->indexAction();
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

    /**
     * @return WorkshopTable
     */
    private function getTable()
    {
        return getSM('ec_workshop_table');
    }
}
