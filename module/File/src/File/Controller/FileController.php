<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace File\Controller;

use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;
use DataView\API\Grid;
use DataView\API\GridColumn;


class FileController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $this->viewModel->setTemplate('file/file/index');
        return $this->viewModel;
    }

    public function connectorAction()
    {
        $type = $this->params()->fromRoute('type', false);
        $path = PUBLIC_FILE . '/';

        $URL = PUBLIC_FILE_MANAGER_PATH . '/';

        $this->viewModel->setTerminal(true);
        $ds = DIRECTORY_SEPARATOR;
        include_once dirname(__DIR__) . $ds . 'Finder' . $ds . 'elFinderConnector.class.php';
        include_once dirname(__DIR__) . $ds . 'Finder' . $ds . 'elFinder.class.php';
        include_once dirname(__DIR__) . $ds . 'Finder' . $ds . 'elFinderVolumeDriver.class.php';
        include_once dirname(__DIR__) . $ds . 'Finder' . $ds . 'elFinderVolumeLocalFileSystem.class.php';
        $opts = array(
            // 'debug' => true,
            'roots' => array(
                array(
                    'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                    'path' => $path, // path to files (REQUIRED)
                    'URL' => $URL, // URL to files (REQUIRED)
                    'accessControl' => 'fileAccess' // disable and hide dot starting files (OPTIONAL)
                )
            )
        );

// run elFinder
        $connector = new \elFinderConnector(new \elFinder($opts));
        $connector->run();
    }

    public function publicAction()
    {
        $this->viewModel->setTemplate('file/file/public');
        return $this->viewModel;
    }

    public function publicManagerAction()
    {
        $locale = $this->params()->fromRoute('lang', 'fa');
        $this->viewModel->setTerminal(true);

        $this->viewModel->setTemplate('file/file/manager');
        $this->viewModel->setVariables(array(
            'locale' => $locale,
            'type' => 'public'
        ));
        return $this->viewModel;
    }

    public function deleteFileAction()
    {
        $status = FALSE;
        $file = $this->params()->fromRoute('file', false);
        if ($file) {
            $file = base64_decode($file);

            if (file_exists($file)) {
                if (unlink($file)) {
                    $fileName = str_replace(PUBLIC_PATH, '', $file);
                    $file_table = getSM()->get('file_table');
                    if (!($file_table->removeByFileName($fileName) instanceof \Exception))
                        $status = TRUE;
                }
            }
        }
        return new JsonModel(array('status' => $status));
    }
}
