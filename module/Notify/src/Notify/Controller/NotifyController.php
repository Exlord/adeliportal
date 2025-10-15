<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Notify\Controller;

use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\Visualizer;
use Notify\Form\AdvanceConfig;
use Notify\Form\Config;
use Notify\Model\NotifyTable;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;
use Zend\View\Model\JsonModel;

class NotifyController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $uid = current_user()->id;
        $grid = new DataGrid('notify_table');

        $grid->route = 'admin/notify';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $msg = new Column('msg', 'Message');
        $date = new Date('date', 'Date',
            array(
                'headerAttr' => array('width' => '210px', 'align' => 'center'),
                'attr' => array('align' => 'center'),
            ), 0, 1
        );

        $status = new Custom('status', 'Status',
            function (Custom $col) {
                $s = $col->dataRow->status;
                if ($s == '0') {
                    $title = t('NOTIFY_UNREAD');
                    $content = "<span class='glyphicon glyphicon-eye-close text-danger grid-icon' title='{$title}'></span>";
                    $content .= Common::Link(
                        "<span class='glyphicon glyphicon-eye-open text-success'></span>",
                        url('admin/notify/read', array('id' => $col->dataRow->id)),
                        array('class' => array('ajax_page_load', 'btn', 'btn-default', 'btn-xs'), 'title' => t('Click here to change status to read'))
                    );
                } else {
                    $title = t('NOTIFY_READ');
                    $content = "<span class='glyphicon glyphicon-eye-open text-muted grid-icon' title='{$title}'></span>";
                }

                return $content;
            },
            array(
                'headerAttr' => array('width' => '70px', 'align' => 'center'),
            )
        );

        $delete = new DeleteButton();

        $grid->addColumns(array($id, $status, $msg, $date, $delete));
        $grid->addDeleteSelectedButton();

        $grid->defaultSort = $date;
        $grid->defaultSortDirection = $grid::SORT_DESC;
        $grid->getSelect()->where(array('uId' => $uid));

        $this->viewModel->setTemplate('notify/notify/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function readAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $this->getTable()->update(array('status' => 1), array('id' => $id));
            if ($this->request->isPost())
                return new JsonModel(array('status' => 1));
        }

        if ($this->request->isPost())
            return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
        else
            return $this->indexAction();
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getTable()->remove($id);
                return new JsonModel(array('status' => 1, 'cmd' => "Notifications.update('notify');"));
            }
        }
        return $this->unknownAjaxError();
    }

    public function configAction()
    {
        /* @var $config Config */
        $config = getConfig('notify');
        $form = prepareConfigForm(new Config());
        $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($post->toArray());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getConfigTable()->save($config);
                    db_log_info("Notifications Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Notifications configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('notify/notify/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function advanceConfigAction()
    {
        /* @var $config Config */
        $config = getConfig('notify-advance');
        $form = prepareConfigForm(new AdvanceConfig());
        $form->setData($config->varValue);


        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($post->toArray());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getConfigTable()->save($config);
                    db_log_info("Notifications Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Notifications configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('notify/notify/advance-config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function updateAction()
    {
        /* @var $notifications callable */
        $notifications = $this->vhm()->get('notifications');
        return new JsonModel(array('data' => $notifications(false)));
    }

    /**
     * @return NotifyTable
     */
    private function getTable()
    {
        return getSM('notify_table');
    }
}
