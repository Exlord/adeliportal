<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\ModulesTable;
use System\IO\Directory;
use ServerManager\Model;

class ModulesController extends \System\Controller\BaseAbstractActionController
{
    public function indexAction()
    {
        $installed_modules = $this->getTable()->getArray();
        $cacheKey = 'modules_list';
        if (!$modules = getCache()->getItem($cacheKey))
            return $this->rebuildAction();

        $stages = array('Disabled', 'Development', 'Active');

        $this->viewModel->setTemplate('application/modules/index');
        $this->viewModel->setVariables(array('modules' => $modules, 'stages' => $stages, 'installed_modules' => $installed_modules));
        return $this->viewModel;
    }

    public function rebuildAction()
    {
        $modules = array();

        $dirs = Directory::getDirs(ROOT . '/module', false, array('Sample', 'ServerManager', 'ClientManager', 'OnlineOrder', 'OnlineOrders',));
        foreach ($dirs as $ns) {
            $this->getModuleConfig($ns, $modules);
        }
        ksort($modules);
        getCache()->setItem('modules_list', $modules);
        return $this->indexAction();
    }

    private function getModuleConfig($ns, &$modules)
    {
        $file = parse_ini_file(ROOT . '/module/' . $ns . '/module.ini');
        $modules[$ns] = $file;
    }

    /**
     * @return ModulesTable
     */
    private function getTable()
    {
        return getSM('modules_table');
    }

//    private function getUpdates($updated_modules)
//    {
//        $updates = array();
//        foreach ($updated_modules as $key => $version) {
//            $mName = "\\$key\\Module";
//            $module = new $mName();
//            $new_updates = $module->getUpdates($version['installed_version']);
//            if (!count($new_updates))
//                $new_updates = $version['new_version'];
//            $updates[$key] = $new_updates;
//            $module = null;
//        }
//        return $updates;
//    }
//
//    public function updateAction()
//    {
//        $hidden_update = $this->params()->fromRoute('hidden-update', false);
//        $installed_modules = $this->getTable()->getArray();
//        $modules = getSM('ModuleManager')->getModules();
//        $loaded_modules = array();
//        foreach ($modules as $ns) {
//            $this->getModuleConfig($ns, $loaded_modules);
//        }
//        $updated_modules = array();
//
//        foreach ($loaded_modules as $key => $m) {
//            if ($m['version'] != $installed_modules[$key]->version)
//                $updated_modules[$key] = array(
//                    'installed_version' => $installed_modules[$key]->version,
//                    'new_version' => $m['version']
//                );
//        }
//        $updates = $this->getUpdates($updated_modules);
//        $processed = $updates;
//        $errors = array();
//        if ($this->request->isPost() || $hidden_update) {
//            foreach ($updates as $m => $update) {
//                if (is_array($update) && count($update)) {
//                    foreach ($update as $version => $details) {
//                        $mName = '_update_' . str_replace('.', '_', $version);
//                        try {
//                            getSM($details['table'])->$mName();
//                            $this->getTable()->update(array('version' => $version), array('name' => $m));
//                            unset($processed[$m][$version]);
//                        } catch (\Exception $ex) {
//                            $errors[$m][$version][] = $ex->getMessage();
//                            $errors[$m][$version][] = $ex->getPrevious()->getMessage();
//                        }
//                    }
//                    if (!count($processed[$m]))
//                        unset($processed[$m]);
//                } else {
//                    $this->getTable()->update(array('version' => $update), array('name' => $m));
//                    unset($processed[$m]);
//                }
//            }
//            getCache()->removeItem('installed_modules', 'modules_list');
//        }
//        if (!count($errors) && $hidden_update)
//            return false;
//
//        $updates = $processed;
//        $this->layout('layout/update');
//        $this->viewModel->setTemplate('application/modules/update');
//        $this->viewModel->setVariables(array('updates' => $updates, 'errors' => $errors));
//        return $this->viewModel;
//    }
}
