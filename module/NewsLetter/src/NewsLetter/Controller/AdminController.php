<?php
namespace NewsLetter\Controller;

use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use Mail\API\Mail;
use NewsLetter\Form\Config;
use NewsLetter\Form\ConfigMore;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;

class AdminController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('news_letter_template');
        $grid->route = 'admin/news-letter/template';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $desc = new Column('desc', 'Description');
        $sendEmail = new Button('Send Email', function (Button $col) {
            $col->route = 'admin/news-letter/send';
            $col->routeOptions['query'] = array('templateId' => $col->dataRow->id, 'email' => '');
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('grid_button', 'mail_button', 'ajax_page_load'))
        ));
        $edit = new EditButton();
        $delete = new DeleteButton();
        $grid->addColumns(array($id, $desc, $sendEmail, $edit, $delete));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();
        $grid->addButton('Send News Letter', 'Send News Letter', false, 'admin/news-letter/send', 'send');

        $this->viewModel->setTemplate('news-letter/admin/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function sendNewsLetterAction()
    {
        $queryParams = $this->params()->fromQuery();
        $id = current_user()->id;
        // $phoneBookData = getSM()->get('phoneBook_table')->getEmails();
        $role = getSM()->get('role_table')->getRoleName();
        $configMail = Mail::getFrom('mail_config');
        $defaultMail = array_values($configMail);
        $form = new \NewsLetter\Form\NewsLetter($role, $defaultMail[0]);
        $form->setAttribute('action', url('admin/news-letter/send'));
        if (isset($queryParams['templateId']) && isset($queryParams['email'])) {
            if ($queryParams['templateId']) {
                $selectTemp = getSM()->get('news_letter_template')->get($queryParams['templateId']);
                if ($selectTemp) {
                    $form->get('body')->setValue($selectTemp->body);
                    $form->get('saveTemplate')->setAttribute('class', 'hidden');
                    $form->get('saveTemplate')->setOptions(array('label' => ''));
                }
            }
            if ($queryParams['email'])
                $form->get('to')->setValue($queryParams['email']);
        }
        if ($this->request->isPost()) {
            $dataRole = array();
            $post = $this->request->getPost()->toArray();
            $form->setData($post);
            if ($form->isValid()) {
                if (isset($post['roles']) && $post['roles'] != -1) {
                    $dataRole = getSM()->get('user_table')->getEmailsByRoleId($post['roles']);
                } elseif (isset($post['to']) && $post['to']) {
                    $dataRole = $post['to'];
                }
                if (isset($post['saveTemplate']) && $post['saveTemplate']) {
                    getSM()->get('news_letter_template')->save(array(
                        'body' => $post['body'],
                        'desc' => $post['subject'],
                    ));
                }
                send_mail(
                    $post['to'],
                    $configMail,
                    $post['subject'],
                    $post['body'],
                    \NewsLetter\Module::ENTITY_TYPE,
                    1);
                $this->flashMessenger()->addSuccessMessage('Your information has been sent successfully');
                return $this->indexAction();
            }
        }
        $this->viewModel->setTemplate('news-letter/admin/send-news-letter');
        $this->viewModel->setVariables(array(
            'form' => $form,
        ));
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $form = new \NewsLetter\Form\NewsLetterTemplate();

        if (!$model) {
            $model = new \NewsLetter\Model\NewsLetterTemplate();
            $form->setAttribute('action', url('admin/news-letter/template/new'));
        } else {
            $form->setAttribute('action', url('admin/news-letter/template/new', array('id' => $model->id)));
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

                    $id = getSM()->get('news_letter_template')->save($model);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new constant news Letter template with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction();
                    }
                }
            }
        }

        $this->viewModel->setTemplate('news-letter/admin/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('news_letter_template')->get($id);
        return $this->newAction($model);
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getServiceLocator()->get('news_letter_template')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function configAction()
    {
        $data = array();
        $dataObject = new \stdClass();
        $dataObject->data = $data;
        getSM('news_letter_api')->getInfo($dataObject);

        $this->viewModel->setTemplate('news-letter/admin/config');
        $this->viewModel->setVariables(array(
            'data' => $dataObject->data,
        ));
        return $this->viewModel;
    }

    public function configMoreAction()
    {
        $data = $this->params()->fromRoute();
        if (isset($data['api']) && isset($data['namespace']) && $data['api'] && $data['namespace']) {
            /* @var $config ConfigMore */
            $config = getConfig('newsLetter_config_more');
            if (is_array($config->varValue))
                $oldData = $config->varValue;
            $form = prepareConfigForm(new \NewsLetter\Form\ConfigMore($data['api']));
            $form->setAction(url('admin/news-letter/config/more', array('api' => $data['api'], 'namespace' => $data['namespace'])));
            if ($config->varValue)
                $form->setData($config->varValue);
            if ($this->request->isPost()) {
                $post = $this->request->getPost()->toArray();
                if (isset($post['buttons']['submit'])) {
                    $form->setData($this->request->getPost());
                    if ($form->isValid()) {
                        if (isset($oldData[$data['api']]))
                            unset($oldData[$data['api']]);
                        $oldData[$data['api']] = $post['select'];
                        $config->setVarValue($oldData);
                        $this->getServiceLocator()->get('config_table')->save($config);
                        db_log_info("News Letter Configs More changed");
                        $this->flashMessenger()->addSuccessMessage('News Letter Configs More saved successfully');
                    }
                }
            }

            $this->viewModel->setTemplate('news-letter/admin/config-more');
            $this->viewModel->setVariables(array('form' => $form));
            return $this->viewModel;
        } else
            return $this->configAction();
    }

    public function emailsListAction()
    {
        $grid = new DataGrid('news_letter_sign_up_table');
        $grid->route = 'admin/news-letter/emails-list';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $email = new Column('email', 'Email');
        $status = new Select('status', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );
        $delete = new DeleteButton();
        $grid->addColumns(array($id, $email, $status, $delete));
        $grid->addDeleteSelectedButton();
        $this->viewModel->setTemplate('news-letter/admin/emails-list');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function emailsUpdateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $field) {
                if ($field == 'status') {
                    getSM('news_letter_sign_up_table')->update(array($field => $value), array('id' => $id));
                    return new JsonModel(array('status' => 1));
                }
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    public function emailsDeleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                getSM('news_letter_sign_up_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function globalConfigAction()
    {
        /* @var $config Config */
        $config = getConfig('newsLetter_config');
        $form = prepareConfigForm(new \NewsLetter\Form\Config());
        $form->setAction(url('admin/news-letter/config/global'));
        if ($config->varValue)
            $form->setData($config->varValue);
        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($post);
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("News Letter Global Configs More changed");
                    $this->flashMessenger()->addSuccessMessage('News Letter Global Configs More saved successfully');
                }
            }
        }
        $this->viewModel->setTemplate('news-letter/admin/global-config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }
}
