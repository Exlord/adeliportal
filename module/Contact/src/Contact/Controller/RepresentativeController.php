<?php
namespace Contact\Controller;

use Contact\Form\RepresentativeConfig;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use File\API\File;
use System\Controller\BaseAbstractActionController;
use Theme\API\Themes;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class RepresentativeController extends BaseAbstractActionController
{

    public function representativeAction()
    {
        $zoom = 4; //zoom default
        $center = '32.64369215935833,53.445556685328484'; //center map default

        $selectContactUser = getSM('contact_user_table')->getContacts(null, null, 1);

        if ($selectContactUser->count()) {
            foreach ($selectContactUser as $row) {
                $dataArray[$row->id] = (array)$row;
            }
            $entityType = \Contact\Module::CONTACT_USER_ENTITY_TYPE;
            localize($dataArray, $entityType);
            $config = getSM('config_table')->getByVarName('contact_representative')->varValue;

            if (isset($config['zoom']) && $config['zoom'])
                $zoom = $config['zoom'];

            if (isset($config['center']) && $config['center'])
                $center = $config['center'];

            $this->viewModel->setVariables(array(
                'selectContactUser' => $dataArray,
                'zoom' => $zoom,
                'center' => $center,
            ));
        } else {

            $this->viewModel->setVariables(array(
                'empty' => true,
            ));

        }

        $this->viewModel->setTemplate('contact/representative/representative');
        return $this->viewModel;
    }

    public function configAction()
    {
        /* @var $config RepresentativeConfig */
        $config = getConfig('contact_representative');
        $form = prepareConfigForm(new \Contact\Form\RepresentativeConfig());
        if ($config->varValue)
            $form->setData($config->varValue);
        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($post);
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Contact representative Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Contact representative configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('contact/representative/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

}
