<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PM\Controller;

use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\Visualizer;
use JBBCode\Parser;
use PM\API\BBCodeDefinitionSet;
use PM\Model\PMTable;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;
use Zend\View\Model\JsonModel;

class PMAdmin extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('pm_table');
        $grid->route = 'admin/pm';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        //region Status
        $status = new Visualizer('status', 'Status',
            array(
                0 => 'glyphicon glyphicon-eye-close text-danger fa-lg',
                1 => 'glyphicon glyphicon-eye-open text-muted',
            ),
            array(
                0 => t('PM_Unread'),
                1 => t('PM_Read')
            ),
            array(
                'headerAttr' => array('width' => '35px', 'align' => 'center'),
                'attr' => array('align' => 'center')
            ),
            true
        );
        //endregion

        //region From
        $from = new Custom('from', 'Sender',
            function (Custom $col) {
                return Common::Link($col->dataRow->username,
                    url('admin/users/view',
                        array('id' => $col->dataRow->from)),
                    array('class' => 'ajax_page_load')
                );
            },
            array(),
            true
        );
        //endregion

        //region Massage
        $parser = new Parser();
        $parser->addCodeDefinitionSet(new BBCodeDefinitionSet());
        $msg = new Custom('msg', 'Message', function (Custom $col) use ($parser) {
            $full_text = strip_tags(trim($col->dataRow->msg));

            $parser->parse($full_text);
            $full_text = $parser->getAsHTML();
            $text = strip_tags(\PM\API\PM::removeFirstQuote($full_text));

            $text = mb_substr($text, 0, 250);

            if ($col->dataRow->status == '0') {
                $text = "<strong>$text</strong>";
                $class[] = 'text-primary';
            } else
                $class[] = 'text-muted';

            $class[] = 'ajax_page_load';
            $text = Common::Link($text, url('admin/pm/view', array('id' => $col->dataRow->id)),
                array('class' => $class, 'data-tooltip' => $full_text)
            );
            return $text;
        });
        //endregion

        $date = new Date('date', 'Date');

        $reply = new Button('Reply', function (Button $col) {
            $col->contentAttr['class'] = array('btn', 'btn-default', 'btn-xs', 'ajax_page_load');
            $col->icon = 'fa fa-reply';
            $col->route = 'admin/pm/new';
            $col->routeParams = array('reply-to' => $col->dataRow->id);
        });

        $del = new DeleteButton();

        $grid->addColumns(array($id, $status, $from, $msg, $date, $reply, $del));
        $grid->addDeleteSelectedButton();
        $grid->addNewButton('New Private Message');

        $table = $grid->getTableGateway()->table;
        $grid->getSelect()
            ->join(array('u' => 'tbl_users'), $table . '.from=u.id', array('username'))
            ->where(array($table . '.to' => current_user()->id));

        $grid->defaultSort = $id;
        $grid->defaultSortDirection = 'DESC';

        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        $this->viewModel->setTemplate('pm/admin/index');
        return $this->viewModel;
    }

    public function viewAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->invalidRequest('admin/pm');

        $pm = $this->getTable()->get($id);
        if (!$pm)
            return $this->invalidRequest('admin/pm');

        $pm = $pm->current();

        //change status to read
        $this->getTable()->update(array('status' => 1), array('id' => $id));

        $parser = new Parser();
        $parser->addCodeDefinitionSet(new BBCodeDefinitionSet());
        $parser->parse($pm->msg);
        $pm->msg = $parser->getAsHTML();

        $this->viewModel->setVariables(array(
            'pm' => $pm
        ));
        $this->viewModel->setTemplate('pm/admin/view');
        return $this->viewModel;
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getTable()->remove($id);
                return new JsonModel(array('status' => 1, 'cmd' => "Notifications.update('pm');"));
            }
        }
        return $this->unknownAjaxError();
    }

//    public function configAction()
//    {
//        /* @var $config Config */
//        $config = getConfig('newsletter');
//        $form = prepareConfigForm(new \RSS\Form\Config($tagId));
//        $form->setData($config->varValue);
//
//
//        if ($this->request->isPost()) {
//            $post = $this->request->getPost();
//            if (isset($post['buttons']['submit'])) {
//                $form->setData($this->request->getPost());
//                if ($form->isValid()) {
//                    $config->setVarValue($form->getData());
//                    $this->getConfigTable()->save($config);
//                    db_log_info("Newsletter Configs changed");
//                    $this->flashMessenger()->addSuccessMessage('Newsletter Text configs saved successfully');
//                }
//            }
//        }
//
//        $this->viewModel->setVariables(array('form' => $form));
//        return $this->viewModel;
//    }

    /**
     * @return PMTable
     */
    private function getTable()
    {
        return getSM('pm_table');
    }
}
