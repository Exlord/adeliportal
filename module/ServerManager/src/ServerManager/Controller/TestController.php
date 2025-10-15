<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ServerManager\Controller;

use Application\API\App;
use ServerManager\API\Hosting\Host;
use System\Controller\BaseAbstractActionController;

class TestController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $api = Host::GetApi();
        $result = $api->createDataBase('test', 'test', 'test');
//        $result = $api->deleteDataBase('test');
//        $result = $api->listDataBases();
        $db = App::getDbAdapter($result->result['dbName'], $result->result['dbUser'], 'test');
        $q = file_get_contents(ROOT.'/module/Application/install.sql');
        $db->query($q)->execute();

        $error = $api->error;
        $this->viewModel->setVariables(array('result' => $result, 'error' => $error));
        $this->viewModel->setTemplate('server-manager/test/index');
        return $this->viewModel;
    }
}
