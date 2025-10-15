<?php
namespace Contact\Controller;

use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use File\API\File;
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use Theme\API\Themes;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class ContactUserClientController extends BaseAbstractActionController
{
    public function contactsAction()
    {
        $selectContactUser = '';
        $sendId = ''; //be user default send shavad
        $zoom = 4; //zoom default
        $center = '32.64369215935833,53.445556685328484'; //center map default
        $showContactUrl = true; //show link for contact section
        $dataArray = array();
        $defType = $this->params()->fromQuery('defType',null);

        $route = $this->params()->fromRoute();
        if (isset($route['contactId']) && $route['contactId']) {
            $selectContactUser = getSM('contact_user_table')->getContacts($route['contactId']);
            $sendId = $route['contactId'];
            $showContactUrl = false;
        } elseif (isset($route['catId']) && $route['catId'])
            $selectContactUser = getSM('contact_user_table')->getUserContactByParentId($route['catId']);
        else // all contact user
            $selectContactUser = getSM('contact_user_table')->getContacts();

        if ($selectContactUser) {
            foreach ($selectContactUser as $row) {
                $dataArray[$row->id] = (array)$row;
            }
            $config = getSM('config_table')->getByVarName('contact')->varValue;

            if (isset($config['defaultUser']) && $config['defaultUser'] && empty($sendId))
                $sendId = $config['defaultUser'];

            $typeArray = null;
            $typeArray = getSM('contact_type_table')->getArray($sendId, 1);
            if (isset($config['zoom']) && $config['zoom'])
                $zoom = $config['zoom'];

            if (isset($config['center']) && $config['center'])
                $center = $config['center'];
            $form = new \Contact\Form\Contact($sendId, $typeArray);
            $model = new \Contact\Model\Contact();
            if($defType)
               $model->typeContact=$defType;
            $form->bind($model);
            //save & send mail contact
            if ($this->request->isPost()) {
                $data = $this->request->getPost();
                if ($data && isset($data['sendIds'])) {
                    $form->setData($data);
                    if ($form->isValid()) {
                        $model->date = time();
                        $model->sendId = $data['sendIds'];
                        $id = getSM('contact_table')->save($model);
                        //send notify
                        if ($model->sendId) {
                            $selectUser = getSM('contact_user_table')->get($model->sendId);
                            $config = getConfig('contact')->varValue;
                            $typeName = '';
                            $emailsToSend = array();
                            if ($model->typeContact)
                                $typeName = getSM('contact_type_table')->get((int)$model->typeContact)->title;
                            if (isset($config['sendEmail']) && $config['sendEmail'])
                                $emailsToSend = explode(',', $config['sendEmail']);
                            if ($selectUser->email)
                                $emailsToSend[] = $selectUser->email;
                            $notify = getNotifyApi();
                            if ($notify) {
                                /* @var $dateFormat callable */
                                $dateFormat = getSM('ViewHelperManager')->get('dateFormat');
                                $email = $notify->getEmail();
                                $email->to = $emailsToSend;
                                $email->from = Mail::getFrom();
                                $email->subject = t('CONTACT_SUBJECT_MAIL');
                                $email->entityType = \Contact\Module::CONTACT_USER_ENTITY_TYPE;
                                $email->queued = 0;
                                $params = array(
                                    '__NAME__' => $model->name,
                                    '__EMAIL__' => $model->email,
                                    '__MOBILE__' => $model->mobile,
                                    '__DESCRIPTION__' => $model->description,
                                    '__TYPE__' => $typeName,
                                    '__DATE__' => $dateFormat($model->date, 0, 1),
                                    '__GETTER__' => $model->sendId,
                                );
                                $notify->notify('Contact', 'Contact_notify', $params);
                                //end
                            }
                        }
                        $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                        $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new contact with id:$id is created");
                    } else {
                        $this->formHasErrors();
                    }

                }
            }
            //end

            $this->viewModel->setTemplate('contact/contact-user-client/contacts');
            $this->viewModel->setVariables(array(
                'form' => $form,
                'selectContactUser' => $dataArray,
                'zoom' => $zoom,
                'center' => $center,
                'showContactUrl' => $showContactUrl,
                'sendId' => $sendId,
            ));
            return $this->viewModel;
        } else {
            $this->flashMessenger()->addErrorMessage('Invalid Request !');
            return $this->redirect()->toRoute('app/front-page');
        }

    }

}
