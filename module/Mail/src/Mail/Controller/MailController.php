<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Mail\Controller;

use Application\Model\Config;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\Visualizer;
use Mail\API\Mail;
use Mail\Model\MailArchiveTable;
use Mail\Model\MailTable;
use System\Controller\BaseAbstractActionController;
use Zend\View\Helper\ViewModel;
use Zend\View\Model\JsonModel;

class MailController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('mail_queue_table');
        $grid->route = 'admin/mail';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $subject = new Column('subject', 'Subject');
        $entityType = new Column('entityType', 'Sender Module');
        $status = new Visualizer('status', 'Status',
            array(),
            array('0' => t('In waiting queue'), '1' => t('Processing'), '-1' => 'Error', '2' => 'Sent'));

        $del = new DeleteButton();

        $grid->addColumns(array($id, $subject, $entityType, $status, $del));
        $grid->addDeleteSelectedButton();
        $grid->addButton('Archived Emails', 'A list of archived emails', '/archive', false, array('archive_button', 'ajax_page_load'));
        $grid->getSelect()->where(array('domain' => ACTIVE_SITE));

        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        $this->viewModel->setTemplate('mail/mail/index');
        return $this->viewModel;
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

    public function archiveAction()
    {
        $grid = new DataGrid('mail_archive_table');
        $grid->route = 'admin/mail/archive';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $subject = new Column('subject', 'Subject');
        $entityType = new Column('entityType', 'Sender Module');
//
//        $position = new Select('position', 'Position', $theme['positions'],
//            array(), array('headerAttr' => array('width' => '50px')));
//
        $status = new Visualizer('status', 'Status',
            array('-1' => 'error', '2' => 'done'),
            array('0' => t('In waiting queue'), '1' => t('Processing'), '-1' => 'Error', '2' => 'Sent'));

        $sendTime = new Date('sendTime', 'Send Time');

        $del = new DeleteButton();

        $message = new Custom('message', 'Message', function (Custom $col) {
            $message = $col->dataRow->message;
//            $first = explode("\n\n", $message);
            return nl2br($message);
        }, array(
            'attr' => array('align' => 'left')
        ));

        $grid->addColumns(array($id, $subject, $entityType, $status, $sendTime, $del, $message));
        $grid->addDeleteSelectedButton();
        $grid->getSelect()->where(array('domain' => ACTIVE_SITE));

        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        $this->viewModel->setTemplate('mail/mail/index');
        return $this->viewModel;
    }

    public function archiveDeleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getArchiveTable()->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }

    public function configAction()
    {
        /* @var $config Config */
        $config = $this->getServiceLocator()->get('config_table')->getByVarName('mail_config');
        $form = new \Mail\Form\Config();
        $form->setAction(url('admin/configs/mail'));
        $form = prepareConfigForm($form);
        if (!empty($config->varValue))
            $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());

                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Mail Configs changed");
                    $this->flashMessenger()->addInfoMessage('Mail configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('mail/mail/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    /**
     * @return MailTable
     */
    private function getTable()
    {
        return getSM('mail_queue_table');
    }

    /**
     * @return MailArchiveTable
     */
    private function getArchiveTable()
    {
        return getSM('mail_archive_table');
    }

    public function quickSendMailAction()
    {
        $status = 0;
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form = new \Mail\Form\QuickSendMail('');
            $form->setData($data);
            if ($form->isValid()) {
                if ($data->quick_send_mail_email_to && $data->quick_send_mail_name && $data->quick_send_mail_email && $data->quick_send_mail_text) {
                    $data->quick_send_mail_text = t('Description')." : " . $data->quick_send_mail_text;
                    $data->quick_send_mail_text .= "<br/> ".t('Name')." : " . $data->quick_send_mail_name;
                    $data->quick_send_mail_text .= "<br/> ".t('Email')." : " . $data->quick_send_mail_email;
                    send_mail(
                        $data->quick_send_mail_email_to,
                        Mail::getFrom('mail_config'),
                        t('MAIL_MSG_FROM_USER'),
                        $data->quick_send_mail_text,
                        \Mail\Module::ENTITY_TYPE_QUICK_SEND_MAIL,
                        0
                    );
                    $status=1;
                }
            }
        }
        $this->viewModel->setTerminal(true);
        $this->viewModel->setTemplate('mail/mail/quick-send-mail-form');
        $this->viewModel->setVariables(array(
            'form'=>$form,
        ));
        $html = $this->render($this->viewModel);
        return new JsonModel(array(
            'status'=>$status,
            'html' => $html,
        ));
    }
}
