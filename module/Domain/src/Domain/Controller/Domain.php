<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Domain\Controller;

use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use ServerManager\API\Hosting\Host;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;

class Domain extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('domain_table');
        $grid->route = 'admin/domain';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $domain = new Column('domain', 'Domain', array('attr' => array('class' => 'left-align')));

        $edit = new EditButton();
        $delete = new DeleteButton();

        $grid->addColumns(array($id, $domain, $edit, $delete));
        $grid->addNewButton();
        $grid->addDeleteSelectedButton();
        $this->viewModel->setTemplate('domain/domain/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function newAction($model = null)
    {
        $oldDomain = null;
        if (!$model) {
            $form = new \Domain\Form\Domain();
            $model = new \Domain\Model\Domain();
            $form->setAttribute('action', url('admin/domain/new'));
        } else {
            $form = new \Domain\Form\Domain($model->domain);
            $form->setAttribute('action', url('admin/domain/edit', array('id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
            $oldDomain = $model->domain;
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
                    $id = getSM()->get('domain_table')->save($model);

                    $sites = include ROOT . '/config/sites.php';

                    if ($oldDomain) {
                        unset($sites[$oldDomain]);
                        unset($sites['www.' . $oldDomain]);
                    }

                    $sites[$model->domain] = ACTIVE_SITE;
                    $sites['www.' . $model->domain] = ACTIVE_SITE;

                    $output = "<?php\n return " . var_export($sites, true) . ";";
                    file_put_contents(ROOT . '/config/sites.php', $output);

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    db_log_info("new domain url with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction();
                    }
                }
            }
        }

        $this->viewModel->setTemplate('domain/domain/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('domain_table')->get($id);
        return $this->newAction($model);
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getServiceLocator()->get('domain_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }
}
