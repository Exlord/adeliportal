<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/6/13
 * Time: 3:18 PM
 */

/**
 * Checks if the provided variable is a valid closure
 * @param $t
 * @return bool
 */
function is_closure($t)
{
    return is_object($t) && ($t instanceof Closure);
}

/**
 * check if the variable has some value including 0 and '0'
 * check if the variable has some value including 0 and '0'
 * @param $t
 * @return bool
 */
function has_value($t)
{
    return (isset($t) && (!empty($t) || $t === 0 || $t === '0') && !is_null($t));
}

/**
 * @param null $name
 * @return array|object|\Zend\ServiceManager\ServiceLocatorInterface
 */
function getSM($name = null)
{
    /* @var $sm \Zend\ServiceManager\ServiceLocatorInterface */
    $sm = \Application\Module::$ServiceManager;
    if ($name) {
        if (!$sm->has($name))
            return null;
        return $sm->get($name);
    }
    return $sm;
}

/**
 * Get ViewHelper and ViewHelperManager
 * @param null $name
 * @return callable
 */
function getVHM($name = null)
{
    if ($name)
        return getSM('ViewHelperManager')->get($name);
    else
        return getSM('ViewHelperManager');
}

/**
 * Prepare a form . remove unwanted buttons ,rename required buttons ,change CSRF name to be unique for the form
 * @param \Zend\Form\Form $form
 * @param array $removeButtons
 * @param array $renameButtons kay=value
 * @return \System\Form\BaseForm
 */
function prepareForm(\Zend\Form\Form $form, array $removeButtons = array(), array $renameButtons = array())
{
    if (is_array($removeButtons) && count($removeButtons)) {
        foreach ($removeButtons as $btn) {
            $form->get('buttons')->remove($btn);
        }
    }
    if (is_array($renameButtons) && count($renameButtons)) {
        foreach ($renameButtons as $old => $new) {
            $btn = $form->get('buttons')->get($old);
            $btn->setLabel($new)->setAttribute('value', $new)->setOption('label', $new);
        }
    }
    return $form;
}

/**
 * Custom prepareForm for config forms removing submit-new and cancel buttons
 * @param \Zend\Form\Form $form
 * @return \System\Form\BaseForm
 */
function prepareConfigForm(\Zend\Form\Form $form)
{
    return prepareForm($form, array('submit-new', 'cancel'));
}

/**
 * @return \Zend\Cache\Storage\Adapter\Filesystem
 */
function getCache($memCache = false)
{
    if ($memCache) {
        if (class_exists('Memcached')) {
            try {
                $mem = getSM('memcache');
                /* @var $mem \Zend\Cache\Storage\Adapter\Memcached */
                return $mem;
            } catch (Exception $e) {
                db_log_exception($e);
            }
        }
    }
    return getSM()->get('cache');
}

/**
 * @param $key
 * @return bool
 * @deprecated Don't do this anymore just get the cache and check if it is null or not
 */
function cacheExist($key)
{
    return getCache()->hasItem($key);
}

/**
 * @param $key
 * @param $value
 * @return bool
 */
function setCacheItem($key, $value, $memCached = false)
{
    return getCache($memCached)->setItem($key, $value);
}

/**
 * @param $key
 * @return mixed
 */
function getCacheItem($key, $memCached = false)
{
    return getCache($memCached)->getItem($key);
}

function debug($var)
{
    if (is_array($var)) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    } else
        var_dump($var);
}

/**
 * Created a url using Url View Helper
 * @param $name
 * @param array $urlParams
 * @param array $routeOptions
 * @param bool $reuseMatchedParams
 * @return string
 */
function url($name, $urlParams = array(), $routeOptions = array(), $reuseMatchedParams = false)
{
    $url = getSM()->get('ViewHelperManager')->get('url');
    $url = $url($name, $urlParams, $routeOptions, $reuseMatchedParams);
    if ($name == 'app/front-page' && !\Application\API\App::hasIntro()) {
        $langTable = getSM('language_table');
        $lang = $langTable->getDefaultLang();
        if (strpos($url, $lang . '/front') !== false)
            $url = str_replace($lang . '/front', '', $url);
        elseif (strpos($url, '/front') !== false)
            $url = str_replace('/front', '', $url);
    }
    return $url;
}

/**
 * @param $name
 * @return \Application\Model\Config
 */
function getConfig($name)
{
    return getSM()->get('config_table')->getByVarName($name);
}

function saveConfig(\Application\Model\Config $config)
{
    getSM('config_table')->save($config);
}

/**
 * @param string $name
 * @param array $value
 */
function setConfigValue($name, $value = array())
{
    $config = getConfig($name);
    $config->varValue = $value;
    getSM('config_table')->save($config);
}

/**
 * @param string $name
 * @param string $key
 * @param int|string|array $value
 */
function updateConfig($name, $key, $value)
{
    $config = getConfig($name);
    $config->varValue[$key] = $value;
    getSM('config_table')->save($config);
}


/**
 * Get the files name from a file path
 * @param string $path
 * @return mixed
 */
function getFileName($path)
{
    $original_name = end(explode('/', $path));
    $original_name = end(explode('\\', $original_name));
    $original_name = str_replace(' ', '_', $original_name);
    $original_name = str_replace('%20', '_', $original_name);
    return $original_name;
}

/**
 * Get files extention from a file name/path
 * @param string $path
 * @return mixed
 */
function getFileExt($path)
{
    $ext = substr($path, strrpos($path, '.') + 1);//this is a better solution in case of a multi extension file
    //end(explode('.', $path));
    return $ext;
}

function fileAccess($attr, $path, $data, $volume)
{
    return strpos(basename($path), '.') === 0 // if file/folder begins with '.' (dot)
        ? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
        : null; // else elFinder decide it itself
}

/**
 * Translates a message
 *
 * @param $message
 * @param null $textDomain
 * @param null $locale
 * @return string The Translated Message
 */
function t($message, $textDomain = null, $locale = null)
{
    if (!has_value($message))
        return $message;
    $t = getSM()->get('viewhelpermanager');
    $t = $t->get('translate');
    return $t($message, $textDomain, $locale);
}

/**
 * Translates a Plural message
 *
 * @param $singular
 * @param $plural
 * @param $number
 * @param null $textDomain
 * @param null $locale
 * @return mixed
 */
function tp($singular, $plural, $number, $textDomain = null, $locale = null)
{
    if ($number === 0 || $number === 1)
        return t($singular, $textDomain, $locale);
    else
        return sprintf(t($plural, $textDomain, $locale), $number);
}

/**
 * Translates the digits inside a string if needed
 * @param $str
 * @return string new $str with translated digits
 */
function localizedDigits($str)
{
    if (Locale::getDefault() == 'fa_IR')
        return str_replace(
            array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'),
            array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'), $str);
    else
        return $str;
}

/**
 * @param $date
 * @param int $dateType
 * @param $timeType
 * @param null $locale
 * @param null $pattern
 * @return mixed
 *
 * $dateType :
 *   -1 => '',
 *   0 => 'l,d F Y',
 *   1 => 'd F Y',
 *   2 => 'd M Y',
 *   3 => 'd/m/Y',
 *   4 => 'Y/m/d',
 *   20 => 'Y'
 *
 * $timeType :
 *   -1 => '',
 *   0 => 'h:i:sa',
 *   1 => 'H:i:s',
 *   2 => 'h:ia',
 *   3 => 'H:i'
 *
 */
function dateFormat($date,
                    $dateType = 0,
                    $timeType = -1,
                    $locale = null,
                    $pattern = null)
{
    /* @var $dateFormat callable */
    $dateFormat = getSM('ViewHelperManager')->get('dateFormat');
    return $dateFormat($date, $dateType, $timeType, $locale, $pattern);
}

/**
 * @param $number
 * @param null $currencyCode IRR,IRT,USD ...
 * @param null $showDecimals
 * @param null $locale
 * @param null $pattern
 * @return mixed
 */
function currencyFormat($number,
                        $currencyCode = null,
                        $showDecimals = null,
                        $locale = null,
                        $pattern = null)
{
    /* @var $currencyFormat callable */
    $currencyFormat = getSM('ViewHelperManager')->get('currency_format');
    return $currencyFormat($number, $currencyCode, $showDecimals, $locale, $pattern);
}

define('LOGGER_EMERG', 0);
define('LOGGER_ALERT', 1);
define('LOGGER_CRIT', 2);
define('LOGGER_ERR', 3);
define('LOGGER_WARNING', 4);
define('LOGGER_NOTICE', 5);
define('LOGGER_INFO', 6);
define ('LOGGER_DEBUG', 7);

function ___exception_trace(Exception $ex, $newline = '')
{
    $msg = $newline . $ex->getMessage();
    $msg .= "\n\n" . $ex->getTraceAsString();
    if ($ex->getPrevious()) {
        $msg .= ___exception_trace($ex->getPrevious(), $msg, "\n\n");
    }
    return $msg;
}

function db_log($type, $msg, $entityType = null)
{
    getSM()->get('logger')->log($type, $msg, $entityType);
}

function db_log_exception(Exception $ex)
{
    getSM()->get('logger')->log(LOGGER_DEBUG, ___exception_trace($ex));
}

function db_log_info($msg, $entityType = null)
{
    db_log(LOGGER_INFO, $msg, $entityType);
}

function db_log_error($msg, $entityType = null)
{
    db_log(LOGGER_ERR, $msg, $entityType);
}

function db_log_notice($msg, $entityType = null)
{
    db_log(LOGGER_NOTICE, $msg, $entityType);
}

function db_log_warning($msg, $entityType = null)
{
    db_log(LOGGER_WARNING, $msg, $entityType);
}

/**
 * @param string|array $to
 *  A comma separated string of email address           exp: 'a1.server.com,a2.server.com'
 *  An array of email address                           exp: array('a1.server.com','a2.server.com')
 *  An array of email address with the keys as alias's  exp: array('a1.server.com'=>'A1','a2.server.com'=>'A2')
 * @param string|array $from
 *  string : the email address                      exp: a1.server.com
 *  array : email address and the alias as the key  exp: array('a1.server.com'=>'A1')
 * @param string $subject
 * @param string $body
 * @param string $entityType
 * @param int $queued if =0 this email will be send immediately
 */
function send_mail($to, $from, $subject, $body, $entityType = 'System', $queued = 1)
{
    if (getSM()->has('mail_api')) {
        /* @var $api \Mail\API\Mail */
        $api = getSM('mail_api');
        $api->addToQueue($to, $from, $subject, $body, $entityType, $queued);
    }
}

/**
 * @return \Thumbnail\API\Thumbnail
 */
function getThumbnail()
{
    return getSM('thumbnail_api');
}

/**
 * @return \User\Model\User
 */
function current_user()
{
    return \User\API\User::getCurrentUser();
}

/**
 * @param $dataRow
 * @return string
 */
function getUserDisplayName($dataRow)
{
    $dataRow = (array)$dataRow;
    $name = $dataRow['displayName'];
    if (empty($name)) {
        if (!empty($dataRow['firstName']) || !empty($dataRow['lastName'])) {
            $name = $dataRow['firstName'] . ' ' . $dataRow['lastName'];
        }
        if (empty($name)) {
            $name = $dataRow['username'];
        }
    }
    return $name;
}

function isAllowed($resource, $roles = null)
{
    $acl = \User\Permissions\Acl\AclManager::load();
    return $acl->isAllowed($roles, $resource);
}

function isCurrentUser($id)
{
    return current_user()->id == $id;
}

function localize(&$model, $entityType, $lang = false)
{
    if (!$lang) {
        /* @var $routeMath \Zend\Mvc\Router\Http\RouteMatch */
        $routeMath = getSM('Application')->getMvcEvent()->getRouteMatch();
        if ($routeMath)
            $lang = $routeMath->getParam('lang', false);
    }

    if (!$lang)
        return false;

    $defaultLang = getSM('language_table')->getDefault();
    if ($lang == $defaultLang)
        return false;

    $config = getSM('Config');
    if (!isset($config['localizable'][$entityType]))
        return false;

    $config = $config['localizable'][$entityType];

    $singleModel = false;
    if (!is_array($model)) {
        $singleModel = true;
        if (is_object($model))
            $model = array($model->{$config['pk']} => $model);
        else
            $model = array($model[$config['pk']] => $model);

//        $translated = getSM('translate_table')->getAll($entityType, $entityId, $lang);
//        foreach ($translated as $translatedContent) {
//            if (is_object($model))
//                $model->{$translatedContent->fieldName} = $translatedContent->content;
//            else
//                $model[$translatedContent->fieldName] = $translatedContent->content;
//        }
    }
    $entityId = array();
    foreach ($model as $item) {
        if (is_object($item))
            $entityId[] = $item->{$config['pk']};
        else
            $entityId[] = $item[$config['pk']];
    }

    $translated = getSM('translate_table')->getAll($entityType, $entityId, $lang);
    foreach ($translated as $translatedContent) {
        if (isset($model[$translatedContent->entityId])) {
            if (is_object($model[$translatedContent->entityId]))
                $model[$translatedContent->entityId]->{$translatedContent->fieldName} = $translatedContent->content;
            else
                $model[$translatedContent->entityId][$translatedContent->fieldName] = $translatedContent->content;
        }
    }

    if ($singleModel)
        $model = array_shift($model);
    return true;
}

/**
 * @return \Notify\API\Notify
 */
function getNotifyApi()
{
    if (getSM()->has('notify_api'))
        return getSM('notify_api');
    return null;
}

function getCurrency()
{
    $config = getConfig('localization_config')->varValue;
    if (isset($config['currency']))
        return $config['currency'];
    else
        return t('currency_code');
}


function psort($input_arr, $function="asort")
{
    $converted = $result = array();

    $alphabet = array(
        '$A$' => "۰",	'$B$' => "۱",	'$C$' => "۲",
        '$D$' => "۳",	'$E$' => "۴",	'$F$' => "۵",
        '$G$' => "۶",	'$H$' => "۷",	'$I$' => "۸",
        '$J$' => "۹",	'$K$' => "آ",	'$L$' => "ا",
        '$M$' => "أ",	'$N$' => "إ",	'$O$' => "ؤ",
        '$P$' => "ئ",	'$Q$' => "ء",	'$R$' => "ب",
        '$S$' => "پ",	'$T$' => "ت",	'$U$' => "ث",
        '$V$' => "ج",	'$W$' => "چ",	'$X$' => "ح",
        '$Y$' => "خ",	'$Z$' => "د",	'$a$' => "ذ",
        '$b$' => "ر",	'$c$' => "ز",	'$d$' => "ژ",
        '$e$' => "س",	'$f$' => "ش",	'$g$' => "ص",
        '$h$' => "ض",	'$i$' => "ط",	'$j$' => "ظ",
        '$k$' => "ع",	'$l$' => "غ",	'$m$' => "ف",
        '$n$' => "ق",	'$o$' => "ك",	'$p$' => "گ",
        '$q$' => "ل",	'$r$' => "م",	'$s$' => "ن",
        '$t$' => "و",	'$u$' => "ه",	'$v$' => "ی",
        '$w$' => "ي",	'$x$' => "ۀ",	'$y$' => "ة"
    );

    foreach($input_arr as $input_key => $input_str)
    {
        if(is_string($input_str))
            foreach($alphabet as $e_letter => $f_letter)
                $input_str = str_replace($f_letter , $e_letter, $input_str);

        if(is_array($input_str))
            $input_str = psort($input_str, $function);

        $converted[$input_key] = $input_str;
    }

    $ret = $function($converted);	// Run function
    $converted = is_array($ret) ? $ret : $converted;	// Check for function output. Some functions affect input itself and retuen bool...

    foreach($converted as $converted_key => $converted_str)
    {
        if(is_string($converted_str))
            foreach($alphabet as $e_letter => $f_letter)
                $converted_str = str_replace($e_letter , $f_letter, $converted_str);

        if(is_array($converted_str))
            $converted_str = psort($converted_str, $function);

        $result[$converted_key] = $converted_str;
    }

    return $result;
}
