<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/19/12
 * Time: 10:09 AM
 */
namespace Localization\View\Helper;

use System\View\Helper\BaseHelper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

class LanguageSwitcher extends BaseHelper
{
    public function __invoke($block, $simpleList = false)
    {
        if ($block) {
            $block->data['class'] .= ' language-switcher-block';
            $block->blockId = 'language-switcher-block-' . $block->id;

        }
        $sm = $this->getServiceManager();
        /* @var $app \Zend\Mvc\Application */
        $app = getSM('Application');
        $routeMatch = $app->getMvcEvent()->getRouteMatch();
//        $lang = null;
//        if ($routeMatch)
//            $lang = $routeMatch->getParam('lang', null);
//        if (!$lang)
//            $lang = getSM('language_table')->getDefaultLang();

        $cacheKey = 'language_switcher';
        if (!$languages = getCacheItem($cacheKey)) {
            $languages = $sm->get('language_table')->getAllActive()->toArray();
            foreach ($languages as &$lang) {
                $lang['title'] = t('Change Language to X', 'default', $lang['langSign']);
            }
            setCacheItem('language_switcher', $languages);
        }

        if (count($languages) > 1) {
            return $this->view->render(
                'localization/language/switcher',
                array('languages' => $languages, 'current_lang' => SYSTEM_LANG, 'simpleList' => $simpleList));
        } else
            return '';
    }
}
