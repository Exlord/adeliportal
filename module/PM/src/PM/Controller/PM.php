<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PM\Controller;

use PM\Model\PMTable;
use System\Controller\BaseAbstractActionController;
use User\Model\UserTable;
use Zend\View\Model\JsonModel;

class PM extends BaseAbstractActionController
{
    public function newAction($replyTo = null)
    {
        $model = new \PM\Model\PM();
        $users = array();
        $to = $this->params()->fromRoute('to', false);
        $flagShowBBCode = $this->params()->fromQuery('flagShowBBCode', true);
        if ($replyTo) {
            $pm = $this->getTable()->get($replyTo)->current();
            $model->to = $pm->from;
            $model->msg = "[blockquote]" . $pm->msg . "[/blockquote]";
            $users[$pm->from] = $pm->username;
        } elseif ($to) {
            $user = (array)getSM('user_table')->getUser($to);
            if ($user) {
                $model->to = $to;
                $users[$to] = $user['username'];
            }
        }

        $form = prepareForm(new \PM\Form\PM(), array(), array(
            'submit' => 'Send',
            'submit-new' => 'Send and New'
        ));
        if(!$flagShowBBCode)
            $form = prepareForm(new \PM\Form\PM(), array('submit-new','cancel'));
        if ($replyTo)
            $form->setAction(url('admin/pm/reply', array('to' => $replyTo)));

        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

           // if ($this->isSubmit()) {
                $form->setData($post);
                if ($form->isValid()) {

                    if ($model->to) {

                        if ($model->to != current_user()->id) {

                            $model->from = current_user()->id;
                            $model->date = time();
                            $this->getTable()->save($model);

                            if(!$flagShowBBCode){
                                return new JsonModel(array(
                                    'status'=>1
                                ));

                            }

                            $this->flashMessenger()->addSuccessMessage('Private Message Sent successfully');

//                    $notify = getNotifyApi();
//                    if ($notify) {
//                        $user = $this->getUserTable()->getUser($model->to);
//                    }

                            if ($this->isSubmitAndNew()) {
                                $model = new \PM\Model\PM();
                                $form->bind($model);
                            } else
                                return $this->forward()->dispatch('PM\Controller\PMAdmin', array('action' => 'index'));
                        } else
                            $form->get('to')->setMessages(array('You are not allowed to send private messages to your self.'));
                    } else
                        $form->get('to')->setMessages(array('you need to select a recipient for your message'));
                } else
                    $this->formHasErrors();

           // } elseif ($this->isCancel()) {
          //      return $this->forward()->dispatch('PM\Controller\PMAdmin', array('action' => 'index'));
         //   }
        }

        $this->viewModel->setTemplate('pm/pm/new');
        $this->viewModel->setVariables(array(
            'form' => $form,
            'users' => json_encode($users),
            'flagShowBBCode'=>$flagShowBBCode,
        ));
        return $this->viewModel;
    }

    public function replyAction()
    {
        $replyTo = $this->params()->fromRoute('to', false);
        return $this->newAction($replyTo);
    }

    /**
     * @return PMTable
     */
    private function getTable()
    {
        return getSM('pm_table');
    }

    /**
     * @return UserTable
     */
    private function getUserTable()
    {
        return getSM('user_table');
    }
}
