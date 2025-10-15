<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SiteMap\Controller;

use Application\Model\Config;
use SiteMap\API\SiteMap;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;

class SitemapController extends BaseAbstractActionController
{
    public function indexAction()
    {
        ini_set('memory_limit', '256M');
        /* @var $sitemap SiteMap */
        $sitemap = getSM('sitemap_api');
        return $sitemap->get();
    }

    public function sitemapAction()
    {
        /* @var $sitemap SiteMap */
        $sitemapApi = getSM('sitemap_api');
        $siteMapArray = $sitemapApi->getTree();
        $html = Common::ItemList($siteMapArray);
        $this->viewModel->setTemplate('sitemap/sitemap/site-map');
        $this->viewModel->setVariables(array(
            'html' => $html,
        ));
        return $this->viewModel;
    }

    public function configAction()
    {
        /* @var $config Config */
        $config = getConfig('sitemap');
        $form = prepareConfigForm(new \SiteMap\Form\Config());
        $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if ($this->isSubmit()) {
                $form->setData($post);
                if ($form->isValid()) {
                    $post = $form->getData();
                    $config->setVarValue($post);
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Sitemap Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Sitemap configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('sitemap/sitemap/config');
        $this->viewModel->setVariables(array('form' => $form,));
        return $this->viewModel;
    }

    /**
     * @return SiteMap
     */
    private function getSitemapApi()
    {
        return getSM('sitemap_api');
    }
}
