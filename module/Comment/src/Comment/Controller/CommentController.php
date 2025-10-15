<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Comment\Controller;

use Application\API\App;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;
use DataView\API\Grid;
use DataView\API\GridColumn;

class CommentController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $html = '';
        $script = '';
        $showButtons = false;
        $startOffset = 0;
        $config = getSM('config_table')->getByVarName('comment')->varValue;
        $questStatus = 0;
        if (isset($config['questStatus']) && $config['questStatus'])
            $questStatus = (int)$config['questStatus'];
        /* @var $commentTable  \Comment\Model\commentTable */
        $commentTable = getSM()->get('comment_table');
        $data = $this->request->getPost()->toArray();
        if (isset($data['typeComment']) && $data['typeComment']) {
            if (isset($data['parentId'])) {
                $selectComment = $commentTable->getComments($config['count'], null, $data['entityType'], $data['parentId']);
                $showButtons = true;

            } elseif (isset($data['idParent'])) {
                $selectComment = $commentTable->getAll(array('id' => $data['idParent'], 'entityType' => $data['entityType']))->toArray();
                $showButtons = false;
                if (!$selectComment)
                    return new JsonModel(array(
                        'status' => 0,
                    ));
            } elseif (isset($data['id'])) {
                $selectComment = $commentTable->getComments($config['count'], $data['id'], $data['entityType'], null, $data['startOffset']);
                $startOffset = $data['startOffset'];
                $showButtons = true;
            }
        } else {
            if ($data['level'] == 1) {
                $idOneLevel = array();
                $idTwoLevel = array();
                $selectTwoLevel = array();
                $selectThreeLevel = array();
                if (isset($data['startOffset']) && $data['startOffset'])
                    $startOffset = $data['startOffset'];
                else
                    $startOffset = 0;
                if (isset($config['count']) && $config['count'])
                    $countComment = $config['count'];
                else
                    $countComment = 8;
                $selectOneLevel = getSM('comment_table')->getComments($countComment, 0, $data['entityType'], $data['entityId'], null, $startOffset);
                foreach ($selectOneLevel as $row)
                    $idOneLevel[] = $row['id'];
                if (!empty($idOneLevel)) {
                    $selectTwoLevel = getSM('comment_table')->getComments(0, $idOneLevel, $data['entityType'], $data['entityId']);
                    foreach ($selectTwoLevel as $row)
                        $idTwoLevel[] = $row['id'];
                    if (!empty($idTwoLevel))
                        $selectThreeLevel = getSM('comment_table')->getComments(-1, $idTwoLevel, $data['entityType'], $data['entityId']);
                }

                if (isset($config['editTime']) && $config['editTime'])
                    $editTime = $config['editTime'];
                else
                    $editTime = 86400;

                if (isset($config['deleteTime']) && $config['deleteTime'])
                    $deleteTime = $config['deleteTime'];
                else
                    $deleteTime = 86400;

                $viewHtml = new ViewModel();
                $viewHtml->setTerminal(true)->setTemplate('comment/comment/normal-comment-html');
                foreach ($selectOneLevel as $row) {
                    $viewHtml->setVariables(array(
                        'row' => $row,
                        'dataTwoLevel' => $selectTwoLevel,
                        'dataThreeLevel' => $selectThreeLevel,
                        'editTime' => (int)$editTime,
                        'deleteTime' => (int)$deleteTime,
                        'questStatus' => $questStatus,
                    ));
                    $html .= $this->render($viewHtml);
                }
                $script = 'Comment.startOffset = ' . $startOffset . ';';
                return new JsonModel(array(
                    'html' => $html,
                    'script' => $script,
                ));
            } else {
                $config = getSM('config_table')->getByVarName('comment')->varValue;
                $selectOneLevel = getSM('comment_table')->getComments(-1, $data['parentId'], $data['entityType'], $data['entityId']);
                $viewHtml = new ViewModel();
                $viewHtml->setTerminal(true);
                $viewHtml->setTemplate('comment/comment/normal-one-level-comment-html');
                $viewHtml->setVariables(array(
                    'dataOneLevel' => $selectOneLevel,
                    'editTime' => (int)$config['editTime'],
                    'deleteTime' => (int)$config['deleteTime'],
                    'className' => $data['className'],
                ));
                $html = $this->render($viewHtml);
                return new JsonModel(array(
                    'html' => $html,
                    'script' => '',
                ));
            }
        }
        //

        $viewHtml = new ViewModel();
        $viewScript = new ViewModel();
        $viewHtml->setTerminal(true)->setTemplate('comment/comment/comment-html');
        $viewScript->setTerminal(true)->setTemplate('comment/comment/comment-script');
        foreach ($selectComment as $row) {
            $viewHtml->setVariable('row', $row);
            $viewHtml->setVariable('showButtons', $showButtons);
            $html .= $this->render($viewHtml);

            $viewScript->setVariable('row', $row);
            $script .= $this->render($viewScript);
        }
        $script .= 'Comment.startOffset = ' . $startOffset . ';';
        return new JsonModel(array(
            'html' => $html,
            'script' => $script,
        ));

    }

    public function newAction()
    {

        $msg = '';
        $form = new \Comment\Form\CommentForm('new');
        $form->setAttribute('action', $this->url()->fromRoute('app/comment/new'));
        $model = new \Comment\Model\Comment();
        $form->bind($model);
        if ($this->request->isPost()) {
            $data = $this->request->getPost();

            $time_session = App::getSession('comment');
            if (!$time_session->offsetExists('timeForComment'))
                $time_session->timeForComment = array();
            $timeComment = $time_session->timeForComment;
            if (!isset($timeComment[$data['entityId']]) || $timeComment[$data['entityId']] < time() - 60) {
                $form->setData($data);
                if ($form->isValid()) {
                    $config = getConfig('comment')->varValue;
                    $questStatus = 0;
                    if (isset($config['questStatus']) && $config['questStatus'])
                        $questStatus = (int)$config['questStatus'];
                    if ($config['status'] == 1) {
                        if ($data['status'] == '1' || $data['status'] == '2')
                            $model->status = 1;
                        elseif ($data['status'] == '0' || $data['status'] == '4')
                            $model->status = 0;
                    } elseif ($config['status'] == 0)
                        $model->status = 0;

                    $id = getSM('comment_table')->save($model);
                    $time_session->timeForComment[$model->entityId] = time();

                    if ($model->status == 1) {
                        $selectOneLevel = getSM('comment_table')->getComments(-1, $model->parentId, $model->entityType, $model->entityId, $id);
                        $viewHtml = new ViewModel();
                        $viewHtml->setTerminal(true)->setTemplate('comment/comment/normal-one-level-comment-html');
                        $viewHtml->setVariables(array(
                            'dataOneLevel' => $selectOneLevel,
                            'editTime' => (int)$config['editTime'],
                            'deleteTime' => (int)$config['deleteTime'],
                            'questStatus'=>$questStatus,
                        ));
                        $html = $this->render($viewHtml);
                        return new JsonModel(array(
                            'html' => $html,
                            'script' => '',
                            'id' => $id,
                            'status' => 1,
                            'commentStatus' => 1
                        ));
                    } else {
                        return new JsonModel(array(
                            'id' => $id,
                            'status' => 1,
                            'commentStatus' => 0
                        ));
                    }
                }

            } else
                $msg = t('Please wait a minute.');

        }

        $this->viewModel->setVariables(array(
            'form' => $form,
            'msg' => $msg,
        ));
        return $this->viewModel;
    }

    public function deleteAction()
    {
        $data = $this->request->getPost();
        if (isset($data['id']) && $data['id']) {
            /* @var $commentTable  \Comment\Model\commentTable */
            $commentTable = getSM()->get('comment_table');
            $commentTable->delete(array('id' => $data['id']));
            return new JsonModel(array(
                'status' => 1
            ));
        }
        return new JsonModel(array(
            'status' => 0
        ));
    }

    public function editAction()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost()->toArray();
            if ($data['id']) {
                $select = getSM('comment_table')->get($data['id']);
                if ((isAllowed(\Comment\Module::APP_COMMENT_EDIT) && current_user()->id == $select->userId) || isAllowed(\Comment\Module::APP_COMMENT_EDIT_ALL)) {
                    getSM('comment_table')->update(array('comment' => $data['comment']), array('id' => $data['id']));
                    return new JsonModel(array(
                        'status' => 1,
                        'text' => $data['comment'],
                    ));
                } else {
                    db_log_error("User with code : " . current_user()->id . " wanted to edit comment with code : " . $data['id']);
                    return new JsonModel(array(
                        'status' => 0,
                    ));
                }
            }
        }
    }
}
