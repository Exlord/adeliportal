<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\API\App;
use Application\Form\Cache;
use Application\Form\Config;
use Application\Form\Optimization;
use Application\Form\Widgets;
use Application\View\Helper\Widget;
use File\API\File;
use System\Controller\BaseAbstractActionController;
use \Application\Model;
use System\IO\Directory;
use Zend\EventManager\EventManager;
use Zend\View\Model\JsonModel;

class AdminController extends BaseAbstractActionController
{
    private function getWidgets()
    {
        $config = getSM('Config');
        if (isset($config['widgets']))
            return $config['widgets'];
        else
            return array();
    }

    public function dashBoardAction()
    {
        /* @var $config Model\Config */
        $config = getConfig('dashboard_widgets_config');

        $widget = getSM('application_event_manager')->loadingDashboard($this);

        $this->viewModel->setTemplate('application/admin/dash-board');
        $this->viewModel->setVariables(array(
            'widgets' => $config->varValue,
            'data' => $widget->data
        ));
        return $this->viewModel;
    }

    public function configAction()
    {
        ini_set('memory_limit', '128M');
        $imageFavIcon = '';
        $imageAdminLogo = '';
        /* @var $config Model\Config */
        $config = getConfig('system_config');
        if (isset($config->varValue['favIconUrl']) && $config->varValue['favIconUrl'])
            $imageFavIcon = $config->varValue['favIconUrl'];
        if (isset($config->varValue['adminLogoUrl']) && $config->varValue['adminLogoUrl'])
            $imageAdminLogo = $config->varValue['adminLogoUrl'];
        $form = new Config();
        $form->setAction(url('admin/configs/system'));
        $form = prepareConfigForm($form);
        if (!empty($config->varValue))
            $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = array_merge_recursive(
                $this->request->getPost()->toArray(),
                $this->request->getFiles()->toArray()
            );
            if (isset($post['buttons']['submit'])) {
                $form->setData($post);

                if ($form->isValid()) {

                    $post = $form->getData();

                    $oldImageFav = '';
                    if (!empty($post['favIconUrl']))
                        $oldImageFav = $post['favIconUrl'];
                    if (!empty($post['favIcon']['name'])) {
                        $favIconUrl = File::MoveUploadedFile($post['favIcon']['tmp_name'], PUBLIC_FILE . '/system', $post['favIcon']['name']);
                        $post['favIconUrl'] = $favIconUrl;
                        $imageFavIcon = $favIconUrl;
                        $form->get('favIconUrl')->setValue($favIconUrl);
                        // $model->fileType = $post['image']['type'];
                        if ($oldImageFav)
                            @unlink(PUBLIC_PATH . $oldImageFav);
                    } else
                        $favIconUrl = $oldImageFav;


                    $oldImageLogo = '';
                    if (!empty($post['adminLogoUrl']))
                        $oldImageLogo = $post['adminLogoUrl'];
                    if (!empty($post['adminLogo']['name'])) {
                        $adminLogoUrl = File::MoveUploadedFile($post['adminLogo']['tmp_name'], PUBLIC_FILE . '/system', $post['adminLogo']['name']);
                        $post['adminLogoUrl'] = $adminLogoUrl;
                        $imageAdminLogo = $adminLogoUrl;
                        $form->get('adminLogoUrl')->setValue($adminLogoUrl);
                        // $model->fileType = $post['image']['type'];
                        if ($oldImageLogo)
                            @unlink(PUBLIC_PATH . $oldImageLogo);
                    } else
                        $favIconUrl = $oldImageLogo;

                    $config->setVarValue($post);
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("System Configs changed");
                    $this->flashMessenger()->addInfoMessage('System configs saved successfully');
                } else
                    $this->formHasErrors();
            }
        }

        $this->viewModel->setTemplate('application/admin/config');
        $this->viewModel->setVariables(array('form' => $form, 'imageFavIcon' => $imageFavIcon, 'imageAdminLogo' => $imageAdminLogo));
        return $this->viewModel;

    }

    public function optimizationAction()
    {
        $config = getConfig('system_optimization_config');
        $form = new Optimization();

        $form = prepareConfigForm($form);
        if (!empty($config->varValue))
            $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());

                if ($form->isValid()) {
                    $postData = $form->getData();
                    $config->setVarValue($postData);
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("System optimization Configs changed");
                    $this->flashMessenger()->addInfoMessage('System Optimization configs saved successfully');

                    $combine_css = (boolean)(int)$postData['combine_css'];
                    if (!$combine_css) {
                        Directory::clear(PUBLIC_FILE . '/cache/css');
                        $this->flashMessenger()->addInfoMessage('Combined/minified and cached css files deleted.');
                    }

                    $combine_css = (boolean)(int)$postData['combine_js'];
                    if (!$combine_css) {
                        Directory::clear(PUBLIC_FILE . '/cache/js');
                        $this->flashMessenger()->addInfoMessage('Combined/minified and cached js files deleted.');
                    }

                    $dirs_to_delete = array('themes', 'css', 'js');
                    foreach ($dirs_to_delete as $d) {
                        Directory::clear(PUBLIC_PATH . '/' . $d, true);
                    }

                    return $this->redirect()->toRoute('admin/optimization');
                }
            }
        }

        $this->viewModel->setTemplate('application/admin/optimization');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function cacheAction()
    {
        $folders = Directory::getDirs(ROOT . '/public_html', false, array('clients', 'lib'));
        $files = Directory::getFiles(ROOT . '/public_html', false, false, array('503.php', 'cron.php', 'index.php', 'init.php', 'robots.txt', 'update.php', '.htaccess'));
        $form = new Cache($folders, $files);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['clear_cache']) && $post['clear_cache'] == '1')
                App::clearAllCache(ACTIVE_SITE);

            if (isset($post['block_url']) && $post['block_url'] == '1')
                getSM('block_url_table')->clear();

            if (isset($post['cache_url']) && $post['cache_url'] == '1')
                getSM('cache_url_table')->clear();

            $css = (isset($post['public_css']) && $post['public_css']);
            $js = (isset($post['public_js']) && $post['public_js']);
            $thumb = (isset($post['thumb']) && $post['thumb']);
            $captcha = (isset($post['captcha']) && $post['captcha']);

            App::clearAllPublicCache($css, $js, $thumb, $captcha);

            if (isset($post['public_folder']) && $post['public_folder']) {
                $publicFolder = $post['public_folder'];
                $dir = ROOT . '/public_html/';
                foreach ($publicFolder as $pf) {
                    if (in_array($pf, $folders))
                        Directory::clear($dir . $pf, true);
                    elseif (in_array($pf, $files))
                        unlink($dir . $pf);
                    else
                        $this->flashMessenger()->addErrorMessage('Invalid file/folder name in public folder');
                }
            }

            $this->flashMessenger()->addSuccessMessage(t('All selected caches have been deleted.'));
            return $this->redirect()->toRoute('admin/cache');
        }

        $this->viewModel->setTemplate('application/admin/cache');
        $this->viewModel->setVariables(array('form' => $form, 'cacheSize' => App::getUsedCacheSize()));
        return $this->viewModel;

    }

    public function widgetsAction()
    {
        $widgets = $this->getWidgets();

        /* @var $config Config */
        $config = $this->getServiceLocator()->get('config_table')->getByVarName('dashboard_widgets_config');
        $form = new Widgets($widgets);
        $form->setAction(url('admin/configs/widgets'));
        $form = prepareConfigForm($form);
        $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());

                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Dashboard widgets Config changed");
                    $this->flashMessenger()->addInfoMessage('Dashboard widgets selection changed successfully');
                }
            }
        }

        $this->viewModel->setTemplate('application/admin/widgets');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;

    }

    public function widgetLoaderAction()
    {
        /* @var $fqn Widget */
        $fqn = $this->params()->fromPost('fqn', false);
        if (!$fqn)
            return $this->unknownAjaxError();

        $content = '';
        $fqn = $this->vhm()->get($fqn);
        if (!$content = $fqn->getCachedView()) {
            $content = $fqn->render();
            if ($fqn->cacheKey)
                setCacheItem($fqn->cacheKey, $content);
        }
        return new JsonModel(array('content' => $content));
    }

    public function backupAction()
    {
        return $this->forward()->dispatch('Application\Controller\DbBackup', array('action' => 'index'));
    }

    public function configsAction()
    {
        return $this->adminMenuPage();
    }

    public function contentsAction()
    {
        return $this->adminMenuPage();
    }

    public function structureAction()
    {
        return $this->adminMenuPage();
    }

    public function ordersAction()
    {
        return $this->adminMenuPage();
    }

    public function deleteFavIconAction()
    {
        if ($this->request->isPost()) {
            $favIconUrl = $this->request->getPost('favIconUrl');
            if ($favIconUrl) {
                $config = getSM('config_table')->getByVarName('system_config');
                $config->varValue['favIconUrl'] = '';
                getSM('config_table')->save($config);
                @unlink(PUBLIC_PATH . $favIconUrl);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    public function deleteAdminLogoAction()
    {
        if ($this->request->isPost()) {
            $adminLogoUrl = $this->request->getPost('adminLogoUrl');
            if ($adminLogoUrl) {
                $config = getSM('config_table')->getByVarName('system_config');
                $config->varValue['adminLogoUrl'] = '';
                getSM('config_table')->save($config);
                @unlink(PUBLIC_PATH . $adminLogoUrl);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }
}
