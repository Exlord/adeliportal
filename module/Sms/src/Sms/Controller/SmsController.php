<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Sms\Controller;

use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;
use DataView\API\Grid;
use DataView\API\GridColumn;

class SmsController extends BaseAbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function sendSmsAction()
    {
        $form = new \Sms\Form\SendSms();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $number = '';
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                if (isset($data['onemobile']) && $data['onemobile'])
                    $number = $data['onemobile'];
                elseif (isset($data['groupmobile']) && $data['groupmobile'])
                    $number = $data['groupmobile'];
                if ($number && $data['textsms']) {
                    $smsApi = getSM('sms_api');
                    $result = $smsApi->send_sms($number, $data['textsms']);
                    if ($smsApi->hasError)
                        $this->flashMessenger()->addErrorMessage($result);
                    else
                        $this->flashMessenger()->addSuccessMessage($result);
//                    $this->flashMessenger()->addSuccessMessage('Your information has been sent successfully');
                } else
                    $this->flashMessenger()->addErrorMessage('Please read the text sms or phone number');
            } else
                $this->flashMessenger()->addErrorMessage('Please try again');
        }


        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function configAction()
    {
        /* @var $config Config */
        $config = $this->getServiceLocator()->get('config_table')->getByVarName('sms');
        $form = prepareConfigForm(new \Sms\Form\Config());
        $form->setData($config->varValue);


        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Sms Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Sms Text configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('sms/sms/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }
}
