<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace Notify\API;

use Application\API\App;
use Mail\Model\Mail;
use Sms\Model\Sms;
use System\API\BaseAPI;
use System\IO\Directory;
use Zend\Form\Element;

class Notify extends BaseAPI
{
//    const EVENT_LOAD_CONFIG = 'load_config';

    const TYPE_SMS = 'sms';
    const TYPE_EMAIL = 'email';
    const TYPE_INTERNAL = 'internal';

    private $types = array(
        self::TYPE_SMS,
        self::TYPE_EMAIL,
        self::TYPE_INTERNAL
    );

    private $_baseConfigs = null;
    private $_globalConfigs = null;
    private $_userRoleConfig = null;
    private $_userConfigs = null;
    private $_systemRecipient = null;
    private $_updatedNotifications = false;

    /**
     * @var Sms
     */
    private $sms;
    /**
     * @var Mail
     */
    private $email;
    /**
     * @var \Notify\Model\Notify
     */
    private $internal;

    /**
     * User Id to be notified
     * @var int
     */
    public $uid = 0;

    /**
     * Highest User Role
     * @var int
     */
    public $userRole = 0;

    public $sentTypes = array();

    /**
     * @return Mail
     */
    public function getEmail()
    {
        if (!$this->email)
            $this->email = new Mail();
        return $this->email;
    }

    /**
     * @return Sms
     */
    public function getSms()
    {
        if (!$this->sms)
            $this->sms = new Sms();
        return $this->sms;
    }

    /**
     * @return \Notify\Model\Notify
     */
    public function getInternal()
    {
        if (!$this->internal)
            $this->internal = new \Notify\Model\Notify();
        return $this->internal;
    }

    public function loadBaseConfig()
    {
        if (!is_null($this->_baseConfigs))
            return $this->_baseConfigs;

        $cacheKey = "notify_modules_config";
        if ($this->_baseConfigs = getCacheItem($cacheKey)) {
            return $this->_baseConfigs;
        }

        $list = array();
        $modules = Directory::getDirs(ROOT . '/module', true);
        foreach ($modules as $moduleName) {
            $notifyConfigFile = $moduleName . '/config/notify.config.php';
            if (file_exists($notifyConfigFile))
                $list = array_merge_recursive($list, include $notifyConfigFile);
        }
        setCacheItem($cacheKey, $list);
        $this->_baseConfigs = $list;
        return $list;
    }

    private function getUserRole()
    {
        if ($this->userRole)
            return $this->userRole;

        if (!$this->uid)
            return array(0);

        $this->userRole = getSM('user_role_table')->getRolesArray($this->uid);
        return $this->userRole;
    }

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $this->sms = null;
        $this->email = null;
        $this->internal = null;
    }

    /**
     * @param string $module Module Namespace
     * @param string $notifyKey
     * @param array $params on associated array of key=>values. array('[__VAR__]'=>'[value]')
     * @return bool
     */
    public function notify($moduleNameSpace, $notifyKey, array $params = array())
    {
        $this->sentTypes = array();

        //the config from merged notify.config files
        $baseConfig = $this->loadBaseConfig();

        //this module dose not have notify activated
        if (!isset($baseConfig[$moduleNameSpace]))
            return $this->returnNotify(false);

        //the config stored in database
        $userConfig = $this->_getGlobalConfig();

        //no notify config has been activated for this system
        if (!count($userConfig))
            return $this->returnNotify(false);

        //no notify config has been activated for this module
//        $modules = $userConfig['modules'];
//        if (!isset($modules[$moduleNameSpace]))
//            return $this->returnNotify(false);

        //no notify config has been activated for this event
//        $module = $modules[$moduleNameSpace];
//        if (!isset($module[$notifyKey]))
//            return $this->returnNotify(false);

        if (!$params || !is_array($params))
            $params = array();

        $anyNotify = false;
        $usedTemplates = array();

        foreach ($userConfig as $roleId => $configs) {

            //notification has been activated for this module
            if (isset($configs['modules'][$moduleNameSpace])) {

                //notification has been activated for this event
                if (isset($configs['modules'][$moduleNameSpace][$notifyKey])) {

                    //type == sms|email|internal
                    foreach ($configs['modules'][$moduleNameSpace][$notifyKey] as $type => $config) {

                        //a notification should ne done for this event with this type
                        if (isset($config['send']) && $config['send'] == '1') {

                            //type setting has been set user system/developer (getSms()|getEmail()|getInternal())
                            if (!is_null($this->{$type})) {

                                //type == sms|email|internal
                                switch ($type) {
                                    case self::TYPE_SMS :
                                        //system has sms module and its has been loaded
                                        if (getSM()->has('sms_api')) {
                                            if ($this->sms) {
                                                if (isset($this->sms->to)) {

                                                    $msg = false;

                                                    //a message has been passed to this notify object directly from the caller code
                                                    //getNotifyApi()->getSms()->msg = 'notify test';
                                                    if (isset($this->sms->msg)) {
                                                        $msg = $this->sms->msg;

                                                        $msg = App::RenderTemplateString($msg, $params);

                                                        //a message with the exact same content has already been delivered (with another userRole)
                                                        if (isset($usedTemplates[$type]))
                                                            if (in_array($msg, $usedTemplates[$type]))
                                                                continue;

                                                        $usedTemplates[$type][] = $msg;
                                                    }

                                                    if (!$msg) {
                                                        //a template has been selected in the config form(global|userRole)
                                                        if (isset($config['template']) && !empty($config['template'])) {
                                                            $template = $config['template'];

                                                            $msg = App::RenderTemplate($template, $params);

                                                            //a message with the exact same content has already been delivered (with another userRole)
                                                            if (isset($usedTemplates[$type]))
                                                                if (in_array($template, $usedTemplates[$type]))
                                                                    continue;

                                                            $usedTemplates[$type][] = $template;
                                                        }
                                                    }

                                                    if (!$msg) {
                                                        //use the default template message provided in module config
                                                        $template = @$baseConfig[$moduleNameSpace][$notifyKey]['notify_with'][$type];
                                                        if ($template) {

                                                            $msg = App::RenderTemplateString(t($template), $params);

                                                            //a message with the exact same content has already been delivered (with another userRole)
                                                            if (isset($usedTemplates[$type]))
                                                                if (in_array($template, $usedTemplates[$type]))
                                                                    continue;

                                                            $usedTemplates[$type][] = $template;
                                                        }
                                                    }

                                                    if (has_value($msg)) {
                                                        getSM('sms_api')->send_sms($this->sms->to, $msg);
                                                        $anyNotify = true;
                                                        $this->sentTypes[] = 'sms';
                                                    }
                                                }
                                            }
                                        }
                                        break;
                                    case self::TYPE_EMAIL :
                                        if ($this->email) {
                                            if (getSM()->has('mail_api')) {
                                                if (isset($this->email->to) && isset($this->email->from) && isset($this->email->subject)) {
                                                    $msg = false;

                                                    //a message has been passed to this notify object directly from the caller code
                                                    //getNotifyApi()->getEmail()->body = 'notify test';
                                                    if (isset($this->email->body)) {
                                                        $msg = $this->email->body;

                                                        $msg = App::RenderTemplateString($msg, $params);

                                                        //a message with the exact same content has already been delivered (with another userRole)
                                                        if (isset($usedTemplates[$type]))
                                                            if (in_array($msg, $usedTemplates[$type]))
                                                                continue;

                                                        $usedTemplates[$type][] = $msg;
                                                    }

                                                    if (!$msg) {

                                                        //a template has been selected in the config form(global|userRole)
                                                        if (isset($config['template']) && !empty($config['template'])) {
                                                            $template = $config['template'];

                                                            $msg = App::RenderTemplate($template, $params);

                                                            //a message with the exact same content has already been delivered (with another userRole)
                                                            if (isset($usedTemplates[$type]))
                                                                if (in_array($template, $usedTemplates[$type]))
                                                                    continue;

                                                            $usedTemplates[$type][] = $template;
                                                        }
                                                    }

                                                    if (!$msg) {

                                                        //use the default template message provided in module config
                                                        $template = @$baseConfig[$moduleNameSpace][$notifyKey]['notify_with'][$type];
                                                        if ($template) {

                                                            $msg = $this->render($template, $params);

                                                            //a message with the exact same content has already been delivered (with another userRole)
                                                            if (isset($usedTemplates[$type]))
                                                                if (in_array($template, $usedTemplates[$type]))
                                                                    continue;

                                                            $usedTemplates[$type][] = $template;
                                                        }

                                                    }

                                                    if (has_value($msg)) {
                                                        send_mail(
                                                            $this->email->to,
                                                            $this->email->from,
                                                            $this->email->subject,
                                                            $msg,
                                                            $this->email->entityType,
                                                            $this->email->queued
                                                        );
                                                        $anyNotify = true;
                                                        $this->sentTypes[] = 'email';
                                                    }
                                                }
                                            }
                                        }
                                        break;
                                    case self::TYPE_INTERNAL :
                                        if ($this->internal) {
                                            if (getSM()->has('notify_table')) {
                                                if ($this->internal->uId) {
                                                    $msg = false;

                                                    //a message has been passed to this notify object directly from the caller code
                                                    //getNotifyApi()->getInternal()->msg = 'notify test';
                                                    if (isset($this->internal->msg)) {
                                                        $msg = App::RenderTemplateString($this->internal->msg, $params);

                                                        //a message with the exact same content has already been delivered (with another userRole)
                                                        if (isset($usedTemplates[$type]))
                                                            if (in_array($msg, $usedTemplates[$type]))
                                                                continue;

                                                        $usedTemplates[$type][] = $msg;
                                                    }

                                                    if (!$msg) {

                                                        //a template has been selected in the config form(global|userRole)
                                                        if (isset($config['template']) && !empty($config['template'])) {
                                                            $template = $config['template'];

                                                            $msg = App::RenderTemplate($template, $params);

                                                            //a message with the exact same content has already been delivered (with another userRole)
                                                            if (isset($usedTemplates[$type]))
                                                                if (in_array($template, $usedTemplates[$type]))
                                                                    continue;

                                                            $usedTemplates[$type][] = $template;
                                                        }
                                                    }

                                                    if (!$msg) {

                                                        //use the default template message provided in module config
                                                        $template = @$baseConfig[$moduleNameSpace][$notifyKey]['notify_with'][$type];
                                                        if ($template) {

                                                            $msg = App::RenderTemplateString(t($template), $params);

                                                            //a message with the exact same content has already been delivered (with another userRole)
                                                            if (isset($usedTemplates[$type]))
                                                                if (in_array($template, $usedTemplates[$type]))
                                                                    continue;

                                                            $usedTemplates[$type][] = $template;
                                                        }

                                                    }

                                                    if (has_value($msg)) {
                                                        $this->internal->msg = $msg;
                                                        $this->internal->date = time();
                                                        getSM('notify_table')->save($this->internal);
                                                        $anyNotify = true;
                                                        if ($this->_updatedNotifications === false) {
                                                            $this->_updatedNotifications = true;
                                                            getSM('ViewHelperManager')->get('InlineScript')->appendScript('Notifications.update("notify");');
                                                        }
                                                        $this->sentTypes[] = 'internal';
                                                    }
                                                }
                                            }
                                        }
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!$anyNotify)
            return $this->returnNotify(false);

        return $this->returnNotify(true);
    }

    private function _getGlobalConfig()
    {
        if (!is_null($this->_globalConfigs))
            return $this->_globalConfigs;

        $cacheKey = "notify_global_config";
        if ($this->_globalConfigs = getCacheItem($cacheKey)) {
            return $this->_globalConfigs;
        }

        $_globalConfigs = getConfig('notify')->varValue;
        if (!is_array($_globalConfigs)) {
            $this->$_globalConfigs = array();
            return $this->_globalConfigs;
        }

        $this->_globalConfigs = array(0 => $_globalConfigs);
        $userRoleConfigs = $this->_getUserRoleConfig();
        if (!isset($userRoleConfigs['user_roles'])) {
            setCacheItem($cacheKey, $this->_globalConfigs);
            return $this->_globalConfigs;
        }

        $roles = $this->getUserRole();
        if ($roles && count($roles)) {

            $temp = array();
            foreach ($roles as $rid) {
                if (isset($userRoleConfigs['user_roles'][$rid])) {
                    $temp[$rid] = $userRoleConfigs['user_roles'][$rid];
                }
            }
            $userRoleConfigs = $temp;
            $temp = null;

            if (count($userRoleConfigs)) {
                foreach ($userRoleConfigs as $rId => $configs) {
                    if (isset($configs['modules'])) {

                        foreach ($configs['modules'] as $moduleName => $events) {

                            //this module dose not exist in global config
                            if (!isset($_globalConfigs['modules'][$moduleName])) {
                                unset($configs['modules'][$moduleName]);
                                continue;
                            }

                            foreach ($events as $event => $notifyTypes) {

                                //this event dose not exist in global configs
                                if (!isset($_globalConfigs['modules'][$moduleName][$event])) {
                                    unset($configs['modules'][$moduleName][$event]);
                                    continue;
                                }

                                foreach ($notifyTypes as $type => $typeSetting) {

                                    $send = $typeSetting['send'];
                                    if ($send == '2') {
                                        $send = '0';
                                        if (isset($_globalConfigs['modules'][$moduleName][$event][$type]['send']))
                                            $send = $_globalConfigs['modules'][$moduleName][$event][$type]['send'];
                                        $userRoleConfigs[$rId]['modules'][$moduleName][$event][$type]['send'] = $send;
                                    }


                                    if ($send == '1') {
                                        if (isset($typeSetting['template'])) {
                                            $template = $typeSetting['template'];
                                            if (empty($template)) {
                                                $template = $_globalConfigs['modules'][$moduleName][$event][$type]['template'];
                                                $userRoleConfigs['modules'][$moduleName][$event][$type]['template'] = $template;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $this->_globalConfigs = $userRoleConfigs;
            }
        }

        setCacheItem($cacheKey, $this->_globalConfigs);
        return $this->_globalConfigs;
    }

    private function _getUserRoleConfig()
    {
        if (is_null($this->_userRoleConfig))
            $this->_userRoleConfig = getConfig('notify-advance')->varValue;

        return $this->_userRoleConfig;
    }

    /**
     * Reset Notify Object and return
     * @param $return
     * @return mixed
     */
    private function returnNotify($return)
    {
        $this->init();
        return $return;
    }

    public function isAllowed($moduleNameSpace, $event, $type)
    {
        //the config from merged notify.config files
        $baseConfig = $this->loadBaseConfig();

        //this module dose not have notify activated
        if (!isset($baseConfig[$moduleNameSpace]))
            return $this->returnNotify(false);

        //the config stored in database
        $userConfig = $this->_getGlobalConfig();

        //no notify config has been activated for this system
        if (!count($userConfig))
            return $this->returnNotify(false);

        foreach ($userConfig as $roleId => $configs) {

            //notification has been activated for this module
            if (isset($configs['modules'][$moduleNameSpace])) {

                //notification has been activated for this event
                if (isset($configs['modules'][$moduleNameSpace][$event])) {

                    foreach ($configs['modules'][$moduleNameSpace][$event] as $notifyTypes) {

                        if (isset($notifyTypes[$type])) {
                            $config = $notifyTypes[$type];
                            //a notification should ne done for this event with this type
                            if (isset($config['send']) && $config['send'] == '1') {
                                return true;
                            }
                        }

                    }
                }
            }
        }

        return false;
    }

    /**
     * @return SystemRecipient
     */
    public function getSystemRecipient()
    {
        if (is_null($this->_systemRecipient)) {

            if (!$this->_systemRecipient = getCache(true)->getItem('system_notification_recipient')) {

                $this->_systemRecipient = new SystemRecipient();
                $config = getConfig('notify')->varValue;
                $user = 2; //1 is serverAdmin and 2 is Admin by default
                if (isset($config['systemNotificationRecipient']))
                    $user = $config['systemNotificationRecipient'];

                $fields = array(
                    'table' => array('id', 'email', 'username', 'displayName'),
                    'profile' => array('mobile', 'firstName', 'lastName')
                );
                $user = getSM('user_table')->getUser($user, $fields);
                if (!$user) {
                    $user = getSM('user_table')->getUser(2, $fields);
                }

                $this->_systemRecipient->id = $user['id'];
                if (isset($user['email']) && has_value($user['email']))
                    $this->_systemRecipient->email = $user['email'];
                if (isset($user['mobile']) && has_value($user['mobile']))
                    $this->_systemRecipient->mobile = $user['mobile'];
                $this->_systemRecipient->name = getUserDisplayName($user);

                getCache(true)->setItem('system_notification_recipient', $this->_systemRecipient);
            }
        }

        return $this->_systemRecipient;
    }
}