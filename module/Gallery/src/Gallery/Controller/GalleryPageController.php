<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Gallery\Controller;

use Application\API\App;
use DataView\Lib\Button;
use Gallery\Form\GalleryPageConfig;
use Mail\API\Mail;
use Theme\API\Themes;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use File\API\File;
use Localization\API\Date;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class GalleryPageController extends BaseAbstractActionController
{

    public function indexAction()
    {
        /* @var $config GalleryPageConfig */
        $config = getConfig('gallery_page_config')->varValue;
        $viewType = 1;
        $slideSpeed = 2;
        if (isset($config['viewType']))
            $viewType = $config['viewType'];
        if (isset($config['slideSpeed']))
            $slideSpeed = $config['slideSpeed'];
        $galleryItem = array();
        $gallery = null;

        switch ($viewType) {
            case 1 :
                $gallery = getSM('gallery_table')->getAll(array('status' => 1,'showType' => 1, 'type' => 'gallery'));
                $this->viewModel->setTemplate('gallery/gallery-page/view-gallery-simple');
                break;
            case 2 :
                $galleryItem = getSM('gallery_item_table')->getAllGalleryItems();
                $this->viewModel->setTemplate('gallery/gallery-page/view-gallery-slide');
                break;
        }

        switch ($slideSpeed) {
            case 1:
                $speed = 7000;
                break;
            case 2 :
                $speed = 4000;
                break;
            case 3:
                $speed = 2000;
                break;
        }
        $this->viewModel->setVariables(array(
            'gallery' => $gallery,
            'galleryItem' => $galleryItem,
            'speed' => $speed,
        ));
        return $this->viewModel;
    }

    public function photoGalleryAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if ($id) {
            $gallery = getSM('gallery_table')->get($id);
            $galleryItem = getSM('gallery_item_table')->getAll(array('groupId' => $id, 'status' => 1));
            $this->viewModel->setTemplate('gallery/gallery-page/photo-gallery');
            $this->viewModel->setVariables(array(
                'galleryItem' => $galleryItem,
                'id' => $id,
                'gallery' => $gallery,
                'hitsType' => 'web',
                'siteUrl'=>App::siteUrl(),
            ));
            return $this->viewModel;
        }
        return $this->indexAction();
    }

    public function configAction()
    {
        /* @var $config GalleryPageConfig */
        $config = getConfig('gallery_page_config');
        $form = prepareConfigForm(new \Gallery\Form\GalleryPageConfig());
        if ($config->varValue)
            $form->setData($config->varValue);
        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($post);
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Gallery Page Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Gallery Page configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('gallery/gallery-page/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

}
