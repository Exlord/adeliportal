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
use Application\Form\Search;
use Application\Form\Test;
use ClientManager\API\License;
use Exception;
use finfo;
use Localization\API\Date;
use Payment\API\Saman;
use RSS\Model\ReaderTable;
use ServerManager\API\Hosting\Host;
use System\Controller\BaseAbstractActionController;
use System\DB\Installer;
use System\DB\Sql\Predicate\Not;
use System\DB\Sql\Select;
use System\IO\File;
use Theme\API\Themes;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\EventManager\EventManager;
use Zend\Filter\Digits;
use Zend\Form\Form;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Permissions\Acl\Acl;
use Zend\View\Model\JsonModel;

class IndexController extends BaseAbstractActionController
{
    public function testAction()
    {

    }

    public function indexAction()
    {
        $config = getConfig('system_config')->varValue;
        $uri = $this->getRequest()->getRequestUri();
        $resolver = $this->getEvent()
            ->getApplication()
            ->getServiceManager()
            ->get('Zend\View\Resolver\TemplatePathStack');

        $default_route = null;

        //region we have intro page and user has requested the front page
        $hasIntro = false;
        $intro = false;
        if ($uri == '/') {

            //main config has intro
            if (isset($config['intro']) && $config['intro'] == '1')
                $hasIntro = true;

            //this domain has intro
            if (isset($config['domains']) && is_array($config['domains'])) {
                if (isset($config['domains'][DOMAIN]) && is_array($config['domains'][DOMAIN])) {
                    if (isset($config['domains'][DOMAIN]['intro']) && $config['domains'][DOMAIN]['intro'] == '1') {
                        $hasIntro = true;
                        $intro = 'application/index/intro-' . str_replace('.', '_', DOMAIN);
                    } else
                        $hasIntro = false;
                }
            }
        }

        if ($hasIntro) {
            if ($intro && $resolver->resolve($intro)) {
                $this->viewModel->setTemplate($intro);
            } else {
                $this->viewModel->setTemplate('application/index/intro');
            }
            //intro is a stand alone page
            $this->viewModel->setTerminal(true);
            return $this->viewModel;
        }
        //endregion

        if (isset($config['default_route']) && !empty($config['default_route'])) {
            $default_route = trim($config['default_route']);
        }

        if (isset($config['domains']) && is_array($config['domains'])) {
            if (isset($config['domains'][DOMAIN]) && is_array($config['domains'][DOMAIN])) {
                if (isset($config['domains'][DOMAIN]['default_route']) && !empty($config['domains'][DOMAIN]['default_route'])) {
                    $default_route = $config['domains'][DOMAIN]['default_route'];
                }
            }
        }

//        if (isset($config['domain_route']) && !empty($config['domain_route'])) {
//            $domainRoute = $config['domain_route'];
//            if (is_array($domainRoute) && isset($domainRoute[DOMAIN]) && !empty($domainRoute[DOMAIN]))
//                $default_route = $domainRoute[DOMAIN];
//        }

        if ($default_route) {
            if (strpos($default_route, 'http') > -1)
                //we have a full url
                return $this->redirect()->toUrl($default_route);
            elseif ($default_route != '/') {

                /* @var $request \Zend\Http\PhpEnvironment\Request */
                $request = $this->getRequest();
                $uri = $request->getUri();
                try {
                    //if default route is actually a system route
                    $route = $this->getEvent()->getRouter()->assemble(array(), array('name' => 'app/' . $default_route));
                } catch (\Exception $e) {
                    //the default is is a relative system url
                    $route = '/' . SYSTEM_LANG . $default_route;
                }

                $uri->setPath($route);
                $request->setUri($uri);
                $route = $this->getEvent()->getRouter()->match($request);
                if ($route)
                    return $this->forward()->dispatch($route->getParam('controller'), array('action' => $route->getParam('action')));
            }
        }


        $isSubDomain = false;
        if (substr_count(DOMAIN, '.') > 1)
            $isSubDomain = true;
        $template = null;
        if ($isSubDomain) {
            $__template = 'application/index/index-' . str_replace('.', '_', DOMAIN);
            if ($resolver->resolve($__template) != false)
                $template = $__template;
            else {
                $__template = 'application/index/index-sub-domains';
                if ($resolver->resolve($__template) != false)
                    $template = $__template;
            }
        }

        if (!$template)
            $template = 'application/index/index';

        $this->viewModel->setTemplate($template);
        $this->viewModel->setTerminal(false);
        return $this->viewModel;
    }

    public function printAction()
    {
        $this->viewModel->setTerminal(true);
        $data = $this->params()->fromRoute('data', false);
        if ($data) {
            $printData = base64_decode($data);
            if ($printData) {
                $printData = unserialize($printData);
                $page = $this->forward()->dispatch($printData['name'], $printData['params'])->setTerminal(true);
                $page = $this->render($page);
                if (isset($printData['template']))
                    $page = App::RenderTemplate($printData['template'], $page);
                $this->viewModel->setVariable('page', $page);
            }
        } else

            $this->viewModel->setTemplate('application/index/print');
        return $this->viewModel;
    }

    public function searchAction()
    {
        $data = array();
        $keyword = $this->params()->fromQuery('keyword', false);
        if (!$keyword)
            $keyword = $this->params()->fromRoute('keyword', false);
        if ($this->request->isPost())
            $keyword = $this->params()->fromPost('keyword', false);

        $form = new Search();
        if ($keyword) {
            $form->setData(array('keyword' => $keyword));
            if ($form->isValid()) {
                $formData = $form->getData();
                $keyword = $formData['keyword'];
                $data = getSM('search_api')->systemSearch($keyword);
            } else
                $keyword = false;
        }

        if (!$this->request->isXmlHttpRequest()) {
            $this->viewModel->setTemplate('application/index/search');
            $this->viewModel->setVariables(array(
                'keyword' => $keyword,
                'data' => $data,
                'form' => $form
            ));
            return $this->viewModel;
        } else {
//            $this->viewModel->setTemplate('application/index/search-result');
//            $json = array();
//            foreach ($data as $module => $items) {
//                foreach ($items as $item) {
//                    $json[] = array('label' => $item, 'category' => t($module));
//                }
//            }
//            return new JsonModel($json);
            $this->viewModel->setTerminal(true);
            $this->viewModel->setTemplate('application/index/search-result');
            $this->viewModel->setVariables(array(
                'data' => $data,
            ));
            return $this->viewModel;
        }
    }

    public function beforeUpdateAction()
    {
        App::clearAllCache(ACTIVE_SITE);
        getSM('block_url_table')->clear();
        getSM('cache_url_table')->clear();
        App::clearAllPublicCache(true, true, true, true);

        return new JsonModel(array('status' => 'done'));
    }

    public function updatesAction()
    {
//        $template = 'application/updates/' . SYSTEM_LANG;
//        $resolver = $this->getEvent()
//            ->getApplication()
//            ->getServiceManager()
//            ->get('Zend\View\Resolver\TemplatePathStack');
//
//        if (false === $resolver->resolve($template))
//            $template = 'application/updates/fa';

        $this->viewModel->setTemplate('application/updates');
        return $this->viewModel;
    }

    public function helpAction()
    {
        $helpApi = getSM('help_api');
        $helps = $helpApi->loadHelp();
        $page = $this->params()->fromRoute('page', 'admin');
        if (!isset($helpApi->flatPages[$page]) && $page != 'admin') {
            $page = 'admin';
        }

        $terminal = $this->params()->fromQuery('terminal', false);

        if ($this->request->isXmlHttpRequest() && $terminal) {
            $this->viewModel->setTerminal(true);
            $this->viewModel->setTemplate($helpApi->flatPages[$page]['page']);
            return $this->viewModel;
        }

        $this->viewModel->setVariables(array(
            'pages' => $helps,
            'selected' => $page
        ));
        $this->viewModel->setTemplate('application/index/help');
        return $this->viewModel;
    }

//    public function refreshCaptchaAction()
//    {
//        /** @var  $captcha \Zend\Captcha\AdapterInterface */
//        $form = prepareConfigForm(new \OnlineOrder\Form\Customer('new', 0));
//        $captcha = $form->get('captcha')->captcha;
//        $data = array();
//        $data['id'] = $captcha->generate();
//        $data['src'] = $captcha->getImgUrl() .
//            $captcha->getId() .
//            $captcha->getSuffix();
//
//        return new JsonModel(array('data' => $data));
//    }
}

/*make new license
$license = License::makeNewLicense(13, strtotime('+ 1 year'));
$data = License::encrypt($license);
$license->data = $data;
getSM('license_table')->save($license);
echo($data);
die();*/


///* @var $notify \Notify\API\Notify */
//$notify = getNotifyApi();
//if ($notify) {
//    $email = $notify->getEmail();
//    $email->setFrom('adeli@azaript.com');
//    $email->setTo('adeli.farhad@gmail.com');
//    $email->setSubject('notify test');
//
//
//    $sms = $notify->getSms();
//    $sms->to = '09355801034';
//    $sms->msg = 'notify test';
//
//    $internal = $notify->getInternal();
//    $internal->uId = 1;
//
//    $notify->uid = 1;
//
//    $notify->notify('User', 'status_changed');
//}

//$adapter = App::getDbAdapter();
//$q = "SHOW TABLES";
//$tables = $adapter->query($q)->execute();
//foreach($tables as $tbl){
//    $tbl = current($tbl);
//    $q = "SHOW INDEX FROM {$tbl} where Key_name <> 'PRIMARY'";
//    $indexs = $adapter->query($q)->execute();
//    if($indexs){
//        foreach($indexs as $in){
//            debug($in);
//        }
//    }
//}
//die();

//$bcrypt = new Bcrypt();
//echo $bcrypt->create('9141002902');
//die();


//$data = '{"17":{"data":{"id":"0", "stateId":"0", "cityId":"0", "ownerMobile":"123", "estateType":"35", "estateArea":"80", "totalPrice":"50000", "ownerEmail":"dsaf", "addressShort":"", "description":"", "regType":"", "addressFull":"", "ownerName":"dasrg"}, "fields":{"build_year":"1390", "can_get_vam":"0", "heating_system":"", "ensheabat":"1", "has_elevator":"0", "room_floor_type":"", "cabinet":"0", "kolangi":"0", "gozar_chand_metry":"", "exchange":"0", "floor_count":"1", "witch_floor":"", "melk_position":"", "sanad_dong":"", "sanad_type":""}},"18":{"data":{"id":"0", "stateId":"0", "cityId":"0", "ownerMobile":"123", "estateType":"35", "estateArea":"65", "totalPrice":"50000000", "ownerEmail":"dsaf", "addressShort":"", "description":"", "regType":"", "addressFull":"", "ownerName":"dasrg"}, "fields":{"build_year":"1390", "can_get_vam":"0", "heating_system":"", "ensheabat":"1", "has_elevator":"0", "room_floor_type":"", "cabinet":"0", "kolangi":"0", "gozar_chand_metry":"", "exchange":"0", "floor_count":"1", "witch_floor":"", "melk_position":"", "sanad_dong":"", "sanad_type":""}}}';
//
//
//$request = new Request();
//$request->setUri('http://iptcms/fa/real-estate/upload-app-data');
//$request->setMethod('POST');
//$request->getPost()->set('username', 'developer');
//$request->getPost()->set('password', '123456');
//$request->getPost()->set('data', $data);
//
//
//$client = new Client();
//$client->setEncType('application/x-www-form-urlencoded');
//
///* @var $response \Zend\Http\Response */
//$response = $client->dispatch($request);
//echo($response->getBody());
//die();


//$form = new Test();
//
//if ($this->getRequest()->isPost()) {
//    $form->setData($this->request->getPost());
//    if ($form->isValid()) {
//        var_dump('is valid');
//    }
//}
//
//$this->viewModel->setVariables(array(
//    'form' => $form
//));
//return $this->viewModel;

//        $request = new Request();
//        $request->setUri('http://melkban.com/fa/real-estate/upload-app-data');
//        $request->setMethod('POST');
//        $request->getPost()->set('data', '"{"30":{"data":{"id":"112", "stateId":"1", "cityId":"140", "ownerMobile":"09132471086", "estateType":"45", "totalPrice":"2000000", "ownerEmail":"info@melkban.org", "addressShort":"10", "description":"", "regType":"77", "addressFull":"", "ownerName":"ملک بانها"}, "fields":{"zirbana":"10", "masahate_zamin":"100", "service_behdashti":"0,0,0", "t_noorgiri":"0,0,1,0", "s_m_dar_tabaghe":"1", "s_m_kolle_tabaghat":"2", "s_m_vahed_dar_tabaghe":"2", "s_m_kolle_vahedha":"", "s_tasisat":"0,0,0,0,0,0,0,0", "refahi":"0,0,0,0", "gozar":"", "sharghan":"", "gharban":"", "jonuban":"", "shomalan":"", "c_otagh_khab":"2", "vaziat_sakhteman":"2", "kitchen":"3", "cabinet_safhe":"0", "cabinet_badane":"1", "cabinet_rukesh":"0", "t_shekle_sakht":"2", "t_kaf":"2", "t_divar":"3", "t_eskelet":"4", "s_nama":"2", "sanad":"3", "mojaveze_sakht":"0", "mashinro":"1", "mosharafbe":"0", "divarkeshi":"2", "hesar":"2", "system_abyari":"3", "emtiaz_ab":"3", "c_parking":"0"}},"31":{"data":{"id":"113", "stateId":"1", "cityId":"140", "ownerMobile":"09142471086", "estateType":"45", "totalPrice":"500000000", "ownerEmail":"info@melkban.org", "addressShort":"aaaaaaaa", "description":"", "regType":"77", "addressFull":"bbbbbbbbbbbb", "ownerName":"ملک بان"}, "fields":{"zirbana":"600", "masahate_zamin":"", "service_behdashti":"0,0,0", "t_noorgiri":"0,0,0,0", "s_m_dar_tabaghe":"2", "s_m_kolle_tabaghat":"2", "s_m_vahed_dar_tabaghe":"3", "s_m_kolle_vahedha":"2", "s_tasisat":"0,0,0,0,0,0,0,0", "refahi":"0,0,0,0", "gozar":"", "sharghan":"", "gharban":"", "jonuban":"", "shomalan":"", "c_otagh_khab":"2", "vaziat_sakhteman":"1", "kitchen":"4", "cabinet_safhe":"1", "cabinet_badane":"1", "cabinet_rukesh":"1", "t_shekle_sakht":"1", "t_kaf":"1", "t_divar":"1", "t_eskelet":"1", "s_nama":"1", "sanad":"1", "mojaveze_sakht":"1", "mashinro":"1", "mosharafbe":"1", "divarkeshi":"1", "hesar":"1", "system_abyari":"1", "emtiaz_ab":"1", "c_parking":"0"}}}"');
//        $request->getPost()->set('username', 'serverAdmin');
//        $request->getPost()->set('password', '123456');
//
//        $client = new Client();
//        $client->setEncType('application/x-www-form-urlencoded');
//
//        $response = false;
//        try {
//            /* @var $response \Zend\Http\Response */
//            $response = $client->dispatch($request);
//
//        } catch (Exception $e) {
//            throw $e;
//        }
//        echo($response->getBody());
//
//        if ($response && $response->isSuccess()) {
//            $updateInfo = $response->getBody();
//            var_dump($updateInfo);
//        }
//        die;