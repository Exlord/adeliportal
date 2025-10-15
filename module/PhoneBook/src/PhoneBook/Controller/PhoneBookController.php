<?php

namespace PhoneBook\Controller;

use Application\API\Export;
use Application\API\Printer;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class PhoneBookController extends BaseAbstractActionController
{

    public function indexAction()
    {
        $newsTemplate=null;
        if (getSM()->has('news_letter_template')) {
            $newsTemplate = getSM()->get('news_letter_template')->getNewsTemplate();
            $flagNewsLetter = true;
        } else
            $flagNewsLetter = false;
        $grid = new DataGrid('phoneBook_table');
        $grid->getSelect()->order('id DESC');
        $grid->itemCountPerPage = 30;
        $grid->route = 'admin/phone-book';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $nameAndFamily = new Column('nameAndFamily', 'Name');
        $email = new Column('email', 'Email');
        $mobile = new Column('mobile', 'Mobile');
        $phone = new Column('phone', 'Phone');
        $fax = new Column('fax', 'Fax');
        $comment = new Column('comment', 'Comment');
        $date = new Custom('date', 'Date', function (Column $col) {
            $dateFormat = getSM()->get('viewhelpermanager')->get('DateFormat');
            return $dateFormat($col->dataRow->date);
        });

        $edit = new EditButton();
        $delete = new DeleteButton();
        $grid->addColumns(array($id, $nameAndFamily, $email, $mobile, $phone, $fax, $comment, $date, $edit, $delete));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();
        $grid->addButton('Word Export', 'Word Export', '/word-export', false, 'phone-book-word-export',null,'',array(),array(),array(),'glyphicon glyphicon-export text-info');
        $grid->addButton('Word export for all', 'Word export for all', '/word-export', false, 'phone-book-word-export-all',null,'',array(),array(),array(),'glyphicon glyphicon-export text-primary');
        $grid->addButton('Print', 'Print', '/print', false, 'phone-book-print',null,'',array(),array(),array(),' glyphicon glyphicon-print text-info');
        $grid->addButton('Print all', 'Print all', '/print', false, 'phone-book-print-all',null,'',array(),array(),array(),' glyphicon glyphicon-print text-primary');
        if ($flagNewsLetter)
            $grid->addButton('Send Email', 'Send Email', '/send-phone-book-email', false, 'send-phone-book-email',null,'',array(),array(),array(),'glyphicon glyphicon-envelope text-info');
        $grid->addButton('Send Sms', 'Send Sms', '/send-phone-book-sms', false, 'send-phone-book-sms',null,'',array(),array(),array(),'glyphicon glyphicon-send text-info');

        $this->viewModel->setTemplate('phone-book/phone-book/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
            'newsTemplate' => $newsTemplate
        ));
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $form = new \PhoneBook\Form\PhoneBook();

        if (!$model) {
            $model = new \PhoneBook\Model\PhoneBook();
            $form->setAttribute('action', url('admin/phone-book/new'));
        } else {
            $form->setAttribute('action', url('admin/phone-book/edit', array('id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
        }


        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction();
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $form->setData($post);

                if ($form->isValid()) {
                    $id = getSM()->get('phoneBook_table')->save($model);
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant page with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction();
                    }
                }
            }
        }

        $this->viewModel->setTemplate('phone-book/phone-book/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('phoneBook_table')->get($id);
        return $this->newAction($model);
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getServiceLocator()->get('phoneBook_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function sendPhoneBookSmsAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $sms = new \Sms\API\SMS();
            $sms->to = $data->number;
            $sms->msg = $data->message;
            $sms->send();
            return new JsonModel(array(
                'status' => 1
            ));
        }
    }

    public function sendPhoneBookEmailAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            return $this->redirect()->toRoute('admin/news-letter/send', array(), array('templateId' => $data['templateId'], 'email' => $data['email']));
        }
    }

    public function wordExportAction()
    {
        $data = $this->params()->fromQuery();
        if ($data['type'] == 'all')
            $select = getSM('phoneBook_table')->getAll();
        elseif ($data['type'] == 'single') {
            $allId = explode(",", $data['allId']);
            $select = getSM('phoneBook_table')->getAll(array('id' => $allId));
        }
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('phone-book/phone-book/view');
        $view->setVariables(array(
            'select' => $select,
        ));
        $htmlOutput = $this->render($view);
        return Export::exportToWord($htmlOutput, 'export-word-phone-book');
    }

    public function preparePrintAction()
    {
        $params = $this->params()->fromQuery();
        if (isset($params['allId']) && $params['allId'])
            $allId = $params['allId'];
        else
            $allId = null;

        $printData = array(
            'type' => $params['type'],
            'allId' => $allId,
        );
        $htmlOutput = $this->render($this->printAction($printData));

        $mailTemplateId = null;
        // TODO GET TEMPLATE FROM CONFIG PHONE BOOK OR SYSTEM
        return Printer::getViewModel($htmlOutput, $mailTemplateId);

    }

    public function printAction($data = null)
    {
        if (!$data)
            $data = $this->params()->fromRoute();
        if ($data['type'] == 'all')
            $select = getSM('phoneBook_table')->getAll();
        elseif ($data['type'] == 'single') {
            $allId = explode(",", $data['allId']);
            $select = getSM('phoneBook_table')->getAll(array('id' => $allId));
        }
        $this->viewModel->setTerminal(true);
        $this->viewModel->setTemplate('phone-book/phone-book/view');
        $this->viewModel->setVariables(array(
            'select' => $select,
        ));
        return $this->viewModel;
    }

}
