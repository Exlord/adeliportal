<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 1:10 PM
 */
namespace Localization;

use Application\API\App;
use Components\Form\NewBlock;
use System\Module\AbstractModule;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Form\Fieldset;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\StaticEventManager;
use User\Permissions\Acl\Acl;
use Zend\Console\Request as ConsoleRequest;
use Zend\Validator\AbstractValidator;

class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;
    protected $version = '1.0';

    const ADMIN_LANGUAGE = 'route:admin/languages';
    const ADMIN_LANGUAGE_UPDATE = 'route:admin/languages/update';
    const ADMIN_LANGUAGE_TRANSLATE = 'route:admin/languages/translate';

    public function onBootstrap(MvcEvent $e)
    {
        parent::onBootstrap($e);

        /* @var $translator \Zend\Mvc\I18n\Translator */
        $translator = $e->getApplication()->getServiceManager()->get('translator');

        AbstractValidator::setDefaultTranslator($translator);

        $em = StaticEventManager::getInstance();
        $em->attach('Zend\Mvc\Application', MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -900);
        $em->attach('Zend\Mvc\Application', MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), 0);
        $em->attach('Zend\Mvc\Application', MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'));
    }

    public function onDispatch(MvcEvent $e)
    {
        /* @var $translator \Zend\I18n\Translator\Translator */
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $translator->addTranslationFilePattern('phpArray', ROOT . '/language/%s', 'miscellaneous.lang');
        $translator->addTranslationFilePattern('phpArray', ROOT . '/module/Theme/public' . TEMPLATE_PATH . '/language', '%s.lang', TEMPLATE_NAME);
    }

    public function onDispatchError(MvcEvent $e)
    {
        $locale = null;
        if (!$e->getRequest() instanceof ConsoleRequest) {
            $uri = explode('/', $e->getRequest()->getRequestUri());
            if (isset($uri[1]) && strlen($uri[1]) === 2)
                $locale = $uri[1];
        }
        if (!$locale) {
            $locale = getSM('language_table')->getDefaultLang();
        } else {
            $langTable = getSM('language_table');
            $activeLangs = $langTable->getArray(true);
            if (!array_key_exists($locale, $activeLangs))
                $locale = getSM('language_table')->getDefaultLang();
        }
        $this->initLang($e, $locale);
    }

    public function onRoute(MvcEvent $e)
    {
        $langTable = getSM('language_table');
        $activeLangs = $langTable->getArray(true);
        $lang = $langTable->getDefaultLang();
        $routeLang = $e->getRouteMatch()->getParam('lang', $lang);
        if (!array_key_exists($routeLang, $activeLangs))
            $routeLang = $lang;

        $e->getRouter()->setDefaultParam('lang', $routeLang);

        $this->initLang($e, $routeLang);
    }

    /**
     * @param MvcEvent $e
     * @param $locale
     * @return Translator
     */
    private function initLang(MvcEvent $e, $locale)
    {
        $sm = $e->getApplication()->getServiceManager();
        /* @var $translator Translator */
        $translator = $sm->get('translator');
        $translator->setLocale($locale);

        /* @var $translator \Zend\I18n\Translator\Translator */
        $translator->addTranslationFilePattern('phpArray', ROOT . '/language', '%s/miscellaneous.lang');
        if (!IS_DEVELOPMENT_SERVER)
            $translator->setCache(getCache(true));

        \Locale::setDefault($translator->translate('lang_locale'));
        defined('SYSTEM_LANG') or define('SYSTEM_LANG', $locale);
        return $translator;
    }
}