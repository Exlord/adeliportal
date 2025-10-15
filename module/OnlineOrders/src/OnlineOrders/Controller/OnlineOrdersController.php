<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace OnlineOrders\Controller;

use Application\API\App;
use System\Controller\BaseAbstractActionController;
use Zend\Form\Fieldset;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;
use DataView\API\Grid;
use DataView\API\GridColumn;
use \Zend\Captcha\Image;

class OnlineOrdersController extends BaseAbstractActionController
{
    public function indexAction()
    {

        $dataGroups = array();
        $dataGroupsLevel = array();
        $dataItems = array();
        $dataLangs = array();

        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $admin_route = strpos($route, 'admin') > -1;

        if ($admin_route)
            $routeChangeCaptcha = url('admin/create-captcha');
        else
            $routeChangeCaptcha = url('app/create-captcha');


        $config = getConfig('onlineOrders')->varValue;
        if (isset($config['supportPercent']) && $config['supportPercent'])
            $supportPercenr = $config['supportPercent'];
        else
            $supportPercenr = 0;

        if (isset($config['langPercent']) && $config['langPercent'])
            $langPercenr = $config['langPercent'];
        else
            $langPercenr = 0;


        /* @var $tableGroup \OnlineOrders\Model\GroupsTable */
        /* @var $tableItem \OnlineOrders\Model\ItemsTable */
        /* @var $tableGroupItem \OnlineOrders\Model\GroupItemTable */
        /* @var $table \OnlineOrders\Model\LanguageTable */
        /* @var $tableCustomer \OnlineOrders\Model\CustomerTable */
        /* @var $tablePerDomains \OnlineOrders\Model\PerDomainsTable */
        /* @var $tableAccount \OnlineOrders\Model\accountNumberTable */
        $tableAccount = getSM()->get('accountNumber_table');
        $tablePerDomains = getSM()->get('perDomains_table');
        $tableLang = getSM()->get('language_online_order_table');
        $tableItem = getSM()->get('items_table');
        $tableGroup = getSM()->get('groups_table');
        $tableGroupItem = getSM()->get('groupItem_table');
        $tableCustomer = getSM()->get('order_customer_table');


        $request = $this->getRequest();


        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $formData['captcha'] = strtolower($formData['captcha']);
            if ($formData['CID'] == $formData['captcha']) {

                $txt = "itemCustomer" . $formData['idGroup'];
                $formData['itemCustomer'] = $formData[$txt];
                unset($formData[$txt]);

                switch ($formData['typePayment']) {
                    case 0 :
                        $formData['infoPayment'] = '';
                        $formData['end4CardNumber'] = '';
                        $formData['datePayment'] = '';
                        $formData['seryalPayment'] = '';
                        break;
                    case 1 :
                        $formData['infoPayment'] = $formData['infoPayment1'];
                        $formData['datePayment'] = $formData['datePayment1'];
                        $formData['end4CardNumber'] = $formData['end4CardNumber'];
                        $formData['seryalPayment'] = '';
                        break;
                    case 2 :
                        $formData['infoPayment'] = '';
                        $formData['end4CardNumber'] = '';
                        $formData['datePayment'] = '';
                        $formData['seryalPayment'] = '';
                        break;
                    case 3 :
                        $formData['infoPayment'] = $formData['infoPayment2'];
                        $formData['datePayment'] = $formData['datePayment2'];
                        $formData['seryalPayment'] = $formData['seryalPayment'];
                        $formData['end4CardNumber'] = '';
                        break;
                }


                if (isset($formData['submit-create'])) {

                    foreach ($formData['langCustomer'] as $key => $val) {
                        if ($val == 'on')
                            $formData['langCustomer'][$key] = '1';
                    }

                    $items = $formData['itemCustomer'];
                    $formData['itemCustomer'] = serialize($formData['itemCustomer']);
                    $formData['langCustomer'] = serialize($formData['langCustomer']);
                    unset($formData['captcha']);
                    unset($formData['CID']);
                    unset($formData['submit-create']);
                    unset($formData['infoPayment1']);
                    unset($formData['infoPayment2']);
                    unset($formData['datePayment1']);
                    unset($formData['datePayment2']);


                    $itemId = $tableCustomer->save($formData);

                    foreach ($items as $key => $value) {
                        if ($value == '1') {
                            $select = $tableItem->get($key);
                            $item[$key] = $select->itemName;
                        }
                    }


                    $this->viewModel->setTemplate('online-orders/online-orders/factorprew.phtml');
                    $this->viewModel->setVariables(
                        array(
                            'form' => $formData,
                            'item' => $item,
                        )
                    );

                    $htmlOutput = $this->getServiceLocator()
                        ->get('viewrenderer')
                        ->render($this->viewModel);


                    // send sms
                    if ($formData['mobilePer']) {
                        $config = getConfig('onlineOrders')->varValue;
                        if (isset($config['txtSms']) && $config['txtSms'])
                            $textSms = $config['txtSms'];
                        else
                            $textSms = "سفارش شما با موفقیت ثبت شد . کد پیگیری : __CODE__ . www.ipt24.ir";

                        $textSms = str_replace('__CODE__', $formData['refCode'], $textSms);
                        $resultSms = getSM('sms_api')->send_sms($formData['mobilePer'], $textSms);
                    }

                    // send email

                    $to = "info@azaript.com";
                    $from = "info@azaript.com";
                    $subject = 'فرم سفارش آنلاین شرکت آذر ایده پرداز تبریز';

                    send_mail(
                        $to,
                        $from,
                        $subject,
                        $htmlOutput,
                        'online-orders',
                        0
                    );
                    if ($formData['emailPer']) {
                        $to = $formData['emailPer'];
                        send_mail(
                            $to,
                            $from,
                            $subject,
                            $htmlOutput,
                            'online-orders',
                            0
                        );
                    }

                    $path = PUBLIC_FILE . "/onlineorders/pages/";
                    $date = date('d', time());
                    $name = $formData['refCode'];
                    $filetype = ".html";
                    file_put_contents(html_entity_decode($path . $name . $filetype), $htmlOutput);

                    $path = "app/online-orders/final-part-order";
                    return $this->redirect()->toRoute($path, array('id' => $itemId, 'typePayment' => $formData['typePayment'], 'refCode' => $formData['refCode'], 'sumResultPrice' => $formData['sumResultPrice']));


                }

            } else
                $this->flashMessenger()->addErrorMessage('Wrong Captcha');
        }


        $groupList = $tableGroup->getAll(null, 'groupPosition DESC');

        foreach ($groupList as $rowGroup) {
            $id = $rowGroup->id;
            $dataGroups[$rowGroup->id] = array(
                'id' => $rowGroup->id,
                'groupName' => $rowGroup->groupName,
                'groupDesc' => $rowGroup->groupDesc,
                'groupPosition' => $rowGroup->groupPosition,
                'groupParentId' => $rowGroup->groupParentId,
                'groupPermit' => $rowGroup->groupPermit,
                'imageIcon' => $rowGroup->imageIcon,
                'groupLevel' => $rowGroup->groupLevel,
                'groupPrice' => $rowGroup->groupPrice,
                'groupShowLang' => $rowGroup->groupShowLang,
                'groupShowSupport' => $rowGroup->groupShowSupport,
            );

            // in ghesmat be surate kolli nist va faghat baraye in sefaresh online ast chon faghat ta 2 zir shakhe gharar ast anjam shavad vali baghie bakhshha kolli hastand
            if ($rowGroup->groupLevel == 0)
                $dataGroupsLevel[$rowGroup->id] = $rowGroup->id;
            //-----------------------------------------------


            $select = $tableItem->getItem($id);
            foreach ($select as $row) {
                $dataItems[$id][$row->id] = array(
                    'id' => $row->id,
                    'itemName' => $row->itemName,
                    'itemDesc' => $row->itemDesc,
                    'itemPrice' => $row->itemPrice,
                    'itemType' => $row->itemType,
                    'itemActive' => $row->itemActive,
                    'itemPosition' => $row->itemPosition,
                    'groupId' => $row->groupId,
                    'itemDescMore' => $row->itemDescMore,
                );
            }
        }


        $lang = $tableLang->getAll();
        foreach ($lang as $rowLang) {
            $dataLangs[$rowLang->id] = array(
                'id' => $rowLang->id,
                'langName' => $rowLang->langName,
                'langCode' => $rowLang->langCode,
            );
        }

        $captcha = new \OnlineOrders\API\Captcha();
        $captcha = $captcha->createCaptcha();


        $factorNumber = 0;
        // $selectDomain = $tablePerDomains->getAll(array('domainStatus' => 1, 'domainSell' => 0));
        $selectAccount = $tableAccount->getAll(array('status' => 1));
        $selectPish = $tableCustomer->getAll(null, 'ID DESC', 1, null);
        if ($selectPish) {
            foreach ($selectPish as $row) {
                $factorNumber = $row->ID;
            }
        }


        $factorNumber += 1001;
        $refCode = 'ipt-' . $factorNumber;
        /*$i = 1;
        while ($i) {
            $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $refCode = "";
            $length = 5;

            for ($p = 0; $p < $length; $p++) {
                $refCode .= $characters[mt_rand(0, strlen($characters) - 1)];
            }

            if (!$tableCustomer->getAll(array('refCode' => $refCode))->count())
                $i = 0;
        }*/

        $this->viewModel->setTerminal(true);
        //$this->layout('layout/layoutonlineorder.phtml');

        $this->viewModel->setTemplate('online-orders/online-orders/index');


        $this->viewModel->setVariables(array(
            'dataGroups' => $dataGroups,
            'dataItems' => $dataItems,
            'dataLangs' => $dataLangs,
            'dataGroupsLevel' => $dataGroupsLevel,
            'langPercent' => $langPercenr,
            'supportPercent' => $supportPercenr,
            'selectAccount' => $selectAccount,
            'refCode' => $refCode,
            'factorNumber' => $factorNumber,
            'captcha' => $captcha,
            'routeChangeCaptcha' => $routeChangeCaptcha,
            // 'formCustomer' => $formCustomer,
            // 'selectDomain' => $selectDomain,
            'admin_route' => $admin_route,
        ));

        return $this->viewModel;
    }

    public function searchDomainAction()
    {
        $domain = $this->params()->fromRoute('domain');
        $html = '';
        set_time_limit(0);
        ob_start();

        $extensions = array(
            '.com' => array('whois.crsnic.net', 'No match for'),
            '.info' => array('whois.afilias.net', 'NOT FOUND'),
            '.net' => array('whois.crsnic.net', 'No match for'),
            '.co.uk' => array('whois.nic.uk', 'No match'),
            '.nl' => array('whois.domain-registry.nl', 'not a registered domain'),
            '.ca' => array('whois.cira.ca', 'AVAIL'),
            '.name' => array('whois.nic.name', 'No match'),
            '.ws' => array('whois.website.ws', 'No Match'),
            '.be' => array('whois.ripe.net', 'No entries'),
            '.org' => array('whois.pir.org', 'NOT FOUND'),
            '.biz' => array('whois.biz', 'Not found'),
            '.tv' => array('whois.nic.tv', 'No match for'),
        );


        if ($domain) {
            $domain = str_replace(array('www.', 'http://'), NULL, $domain);

            if (strlen($domain) > 0) {
                $i = 0;
                foreach ($extensions as $extension => $who) {
                    $buffer = NULL;

                    $sock = fsockopen($who[0], 43) or die('Error Connecting To Server:');
                    fputs($sock, $domain . $extension . "\r\n");

                    while (!feof($sock)) {
                        $buffer .= fgets($sock, 128);
                    }

                    fclose($sock);

                    if (preg_match('/' . $who[1] . '/i', $buffer)) {
                        $html[$i] = $domain . $extension . " available";
                    } else {
                        $html[$i] = $domain . $extension . " Not available";
                    }
                    $i++;

                    ob_flush();
                    flush();
                    sleep(0.3);

                }

                $view = new ViewModel();
                $view->setVariables(array(
                    'html' => $html
                ));
                $view->setTerminal(true);
                $view->setTemplate('online-orders/online-orders/search-domain');
                return $view;


            } else {
                return 'Please enter the domain name';

            }
        }


    }

    public function groupSelectAction()
    {

        /* @var $table \OnlineOrders\Model\GroupsTable */
        /* @var $tableItem \OnlineOrders\Model\ItemsTable */
        /* @var $tableGroupItem \OnlineOrders\Model\GroupItemTable */
        $tableGroupItem = getSM()->get('groupItem_table');
        $tableItem = getSM()->get('items_table');
        $table = getSM()->get('groups_table');
        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $admin_route = strpos($route, 'admin') > -1;


        $formData = $this->request->getPost()->toArray();
        $file = $this->request->getFiles()->toArray();


        $request = $this->getRequest();


        if ($request->isPost()) {


            if ($formData['csrf_groups_form']) {

                if (isset($formData['submit-create'])) {
                    unset($formData['submit-create']);
                    unset($formData['csrf_groups_form']);
                    $pathImage = '';
                    if (isset($file['user-file'])) {
                        if ($file['user-file']['name']) {
                            if ($file["user-file"]["size"] < 1000000) {
                                if ($file["user-file"]["error"] > 0)
                                    $this->flashMessenger()->addInfoMessage('No picture to upload');
                                else {
                                    $url = PUBLIC_FILE . "/onlineorders/" . $file["user-file"]["name"];
                                    if (file_exists($url))
                                        $this->flashMessenger()->addInfoMessage('This file is now available');
                                    else {
                                        $pathImage = "/clients/" . ACTIVE_SITE . "/files/onlineorders/" . $file["user-file"]["name"];
                                        move_uploaded_file($file["user-file"]["tmp_name"], $url);
                                        $this->flashMessenger()->addSuccessMessage('Your file was uploaded successfully');
                                    }
                                }
                            } else
                                $this->flashMessenger()->addSuccessMessage('File size is too large');
                        }
                    }


                    $levelQuery = $table->get($formData['groupParentId']);
                    if ($levelQuery)
                        $groupLevel = $levelQuery->groupLevel;
                    else
                        $groupLevel = -1;
                    $formData['groupLevel'] = $groupLevel + 1;

                    $formData['imageIcon'] = $pathImage;
                    $table->save($formData);
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                }
                if (isset($formData['submit-edit'])) {
                    unset($formData['submit-edit']);
                    unset($formData['csrf_groups_form']);
                    $id = $formData['id'];
                    if (isset($file['user-file'])) {
                        if ($file['user-file']['name']) {
                            if ($file["user-file"]["size"] < 1000000) {
                                if ($file["user-file"]["error"] > 0)
                                    $this->flashMessenger()->addInfoMessage('No picture to upload');
                                else {
                                    $url = PUBLIC_FILE . "/onlineorders/" . $file["user-file"]["name"];
                                    if (file_exists($url))
                                        $this->flashMessenger()->addInfoMessage('This file is now available');
                                    else {
                                        $select = $table->get($id);
                                        @unlink(PUBLIC_PATH . $select->imageIcon);
                                        $pathImage = "/clients/" . ACTIVE_SITE . "/files/onlineorders/" . $file["user-file"]["name"];
                                        move_uploaded_file($file["user-file"]["tmp_name"], $url);
                                        $this->flashMessenger()->addSuccessMessage('Your file was uploaded successfully');
                                        $formData['imageIcon'] = $pathImage;
                                    }
                                }
                            } else
                                $this->flashMessenger()->addSuccessMessage('File size is too large');
                        }
                    }

                    unset($formData['id']);
                    $levelQuery = $table->get($formData['groupParentId']);
                    if ($levelQuery)
                        $groupLevel = $levelQuery->groupLevel;
                    else
                        $groupLevel = -1;
                    $formData['groupLevel'] = $groupLevel + 1;
                    $table->update($formData, array('id' => $id));
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Updated');
                }
                if (isset($formData['submit-delete'])) {
                    $id = $formData['id'];
                    $selectGroupItem = $tableGroupItem->getAll(array('groupId' => $id));
                    foreach ($selectGroupItem as $row) {
                        $idItem = $row->itemId;
                        $tableItem->delete(array('id' => $idItem));
                        $tableGroupItem->delete(array('groupId' => $id));
                    }
                    $select = $table->get($id);
                    @unlink(PUBLIC_PATH . $select->imageIcon);

                    $table->delete(array('id' => $id));

                    $selectGroupsChild = $table->getAll(array('groupParentId' => $id));
                    if ($selectGroupsChild) {
                        foreach ($selectGroupsChild as $rowChild) {
                            $idgGroup = $rowChild->id;
                            if ($rowChild->groupParentId != 0) {
                                $selectGroupItem = $tableGroupItem->getAll(array('groupId' => $idgGroup));
                                foreach ($selectGroupItem as $row) {
                                    $idItem = $row->itemId;
                                    $tableItem->delete(array('id' => $idItem));
                                    $tableGroupItem->delete(array('groupId' => $idgGroup));
                                }
                                $select = $table->get($idgGroup);
                                @unlink(PUBLIC_PATH . $select->imageIcon);
                                $table->delete(array('id' => $idgGroup));
                            }
                        }
                    }
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully deleted');
                }
            }
        }

        $select = $table->getAll();
        foreach ($select as $val) {
            $groupArray[$val->id] = array(
                'id' => $val->id,
                'groupName' => $val->groupName,
                'groupParentId' => $val->groupParentId,
                'groupLevel' => $val->groupLevel
            );
        }
        $treeFunc = new \OnlineOrders\API\TreeFunction();

        $html = $treeFunc->baseTreeFunc(0, $groupArray);
        $formNew = new \OnlineOrders\Form\Groups($html);
        $formEdit = new \OnlineOrders\Form\Groups($html);

        $formNew->remove('submit-delete');
        $formNew->remove('submit-edit');
        // $formEdit = prepareFormElement($formEdit, array('submit-create'));


        $select = $table->getAll(null, "id DESC", null, $this->params()->fromQuery('page', 1));

        $this->viewModel->setTemplate('online-orders/online-orders/group-select');
        return $this->viewModel->setVariables(array(
            'select' => $select,
            'formNew' => $formNew,
            'formEdit' => $formEdit,
            'route' => $route,
            'groupArray' => $groupArray,
            'admin_route' => $admin_route
        ));


        // baraye zamani ke ajax bud
        // $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        /* @var $table \OnlineOrders\Model\GroupsTable */
        /*$table = getSM()->get('groups_table');
        $select = $table->getAll();
        foreach ($select as $val) {
            $groupArray[$val->id] = array(
                'id' => $val->id,
                'groupName' => $val->groupName,
                'groupParentId' => $val->groupParentId,
                'groupLevel' => $val->groupLevel
            );
        }
        $treeFunc = new \OnlineOrders\API\TreeFunction();
        $html = $treeFunc->baseTreeFunc(0, $groupArray);
        $formNew = new \OnlineOrders\Form\Groups($html);
        $select = $table->getAll();
        return new ViewModel(array(
            'select' => $select,
            'formNew' => $formNew,
            'route' => $route,
            'groupArray' => $groupArray
        ));*/
    }

    public function groupOperationsAction()
    {
        // baraye zamani ke ba ajax bud
        /* @var $table \OnlineOrders\Model\GroupsTable */
        $table = getSM()->get('groups_table');
        $type = $this->params()->fromRoute('type');
        $request = $this->getRequest();

        switch ($type) {
            case 1 :
                if ($request->isPost()) {
                    $formData = $this->request->getPost()->toArray();
                    $select = $table->get($formData['id']);
                    $selectGroup = $table->getAll();
                    foreach ($selectGroup as $val) {
                        $groupArray[$val->id] = array(
                            'id' => $val->id,
                            'groupName' => $val->groupName,
                            'groupParentId' => $val->groupParentId,
                            'groupLevel' => $val->groupLevel
                        );
                    }
                    $treeFunc = new \OnlineOrders\API\TreeFunction();
                    $html = $treeFunc->baseTreeFunc(0, $groupArray);
                    $formEdit = new \OnlineOrders\Form\Groups($html);
                    $formEdit->bind($select);
                    $view = new ViewModel();
                    $view->setVariables(array(
                        'form' => $formEdit
                    ));
                    $view->setTemplate('online-orders/online-orders/group-operations');
                    return $view;
                }
                break;
            case 2 :
                if ($request->isPost()) {
                    $formData = $this->request->getPost()->toArray();
                    $levelQuery = $table->get($formData['groupParentId']);
                    if ($levelQuery)
                        $groupLevel = $levelQuery->groupLevel;
                    else
                        $groupLevel = 0;
                    $formData['groupLevel'] = $groupLevel + 1;
                    $id = $table->save($formData);
                    return new JsonModel(array(
                        'id' => $id
                    ));

                }
                break;
            case 3 :
                if ($request->isPost()) {
                    $formData = $this->request->getPost()->toArray();
                    $levelQuery = $table->get($formData['groupParentId']);
                    if ($levelQuery)
                        $groupLevel = $levelQuery->groupLevel;
                    else
                        $groupLevel = 0;
                    $formData['groupLevel'] = $groupLevel + 1;


                    $id = $formData['id'];
                    unset($formData['id']);
                    $table->update($formData, array('id' => $id));
                    $select = $table->get($id);
                    $selectGroup = $table->getAll();
                    foreach ($selectGroup as $val) {
                        $groupArray[$val->id] = array(
                            'id' => $val->id,
                            'groupName' => $val->groupName,
                            'groupParentId' => $val->groupParentId,
                            'groupLevel' => $val->groupLevel
                        );
                    }
                    $treeFunc = new \OnlineOrders\API\TreeFunction();
                    $html = $treeFunc->baseTreeFunc(0, $groupArray);
                    $formEdit = new \OnlineOrders\Form\Groups($html);

                    $formEdit->bind($select);
                    $view = new ViewModel();
                    $view->setVariables(array(
                        'form' => $formEdit
                    ));
                    $view->setTemplate('online-orders/online-orders/group-operations');
                    return $view;
                }
                break;
            case 4 :
                if ($request->isPost()) {
                    $formData = $this->request->getPost()->toArray();
                    $id = $formData['id'];
                    $table->remove(array('id' => $id));
                }
                break;
            case 5 :
                $formData = $this->request->getPost()->toArray();
                $id = $formData['id'];
                $select = $table->get($id);
                @unlink(PUBLIC_PATH . $select->imageIcon);
                $table->update(array('imageIcon' => ''), array('id' => $id));
                break;
        }
    }

    public function languageSelectAction()
    {
        /* @var $table \OnlineOrders\Model\LanguageTable */
        $table = getSM()->get('language_online_order_table');

        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $admin_route = strpos($route, 'admin') > -1;
        $formValid = new \OnlineOrders\Form\Language();

        $formData = $this->request->getPost()->toArray();

        $request = $this->getRequest();

        if ($request->isPost()) {

            $formValid->setData($formData);
            if ($formValid->isValid()) {
                if (isset($formData['submit-create'])) {
                    unset($formData['submit-create']);
                    unset($formData['csrf_Language_form']);
                    $table->save($formData);
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                }
                if (isset($formData['submit-edit'])) {
                    unset($formData['submit-edit']);
                    unset($formData['csrf_Language_form']);
                    $id = $formData['id'];
                    unset($formData['id']);
                    $table->update($formData, array('id' => $id));
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Updated');
                }
                if (isset($formData['submit-delete'])) {
                    $id = $formData['id'];
                    $table->delete(array('id' => $id));
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully deleted');
                }
            }
        }

        $formEdit = new \OnlineOrders\Form\Language();
        $formNew = new \OnlineOrders\Form\Language();
        $formNew->remove('submit-delete');
        $formNew->remove('submit-edit');
        $formEdit->remove('submit-create');

        $select = $table->getAll(null, "id DESC", null, $this->params()->fromQuery('page', 1));

        $this->viewModel->setTemplate('online-orders/online-orders/language-select');
        return $this->viewModel->setVariables(array(
            'select' => $select,
            'formNew' => $formNew,
            'formEdit' => $formEdit,
            'route' => $route,
            'admin_route' => $admin_route
        ));


    }

    public function itemSelectAction()
    {
        $viewPage = 0;
        $select = '';
        $formNew = '';
        $formEdit = '';

        /* @var $tableGroup \OnlineOrders\Model\GroupsTable */
        /* @var $tableItem \OnlineOrders\Model\ItemsTable */
        /* @var $tableGroupItem \OnlineOrders\Model\GroupItemTable */
        $tableItem = getSM()->get('items_table');
        $tableGroup = getSM()->get('groups_table');
        $tableGroupItem = getSM()->get('groupItem_table');

        $selectGroup = $tableGroup->getAll();
        foreach ($selectGroup as $val) {
            $groupArray[$val->id] = array(
                'id' => $val->id,
                'groupName' => $val->groupName,
                'groupParentId' => $val->groupParentId,
                'groupLevel' => $val->groupLevel
            );
        }
        $treeFunc = new \OnlineOrders\API\TreeFunction();
        $html = $treeFunc->baseTreeFunc(0, $groupArray);
        $formGroupList = new \OnlineOrders\Form\GroupList($html);

        $request = $this->getRequest();

        if ($request->isPost()) {

            $viewPage = 1;
            $formData = $this->request->getPost()->toArray();
            if (isset($formData['groupList']))
                $id = $formData['groupList'];
            else
                $id = $formData['groupId'];


            $dataListGroup = array(
                'groupList' => $id,
            );
            $formGroupList->setData($dataListGroup);


            $formNew = new \OnlineOrders\Form\Items($id);
            $formEdit = new \OnlineOrders\Form\Items($id);
            $formValid = new \OnlineOrders\Form\Items($id);

            $formNew->remove('submit-delete');
            $formNew->remove('submit-edit');
            $formEdit->remove('submit-create');
            $formValid->remove('submit-create');
            $formValid->setData($formData);
            if (isset($formData['csrf_items_form'])) {
                if ($formData['csrf_items_form']) {
                    if (isset($formData['submit-create'])) {
                        unset($formData['submit-create']);
                        unset($formData['csrf_items_form']);
                        unset($formData['groupId']);
                        if (!$formData['itemPrice'])
                            $formData['itemActive'] = 1;
                        $formData['itemDescMore'] = str_replace('\"', '', $formData['itemDescMore']);
                        $itemId = $tableItem->save($formData);
                        $dataItem['groupId'] = $id;
                        $dataItem['itemId'] = $itemId;

                        $tableGroupItem->save($dataItem);
                        $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    }
                    if (isset($formData['submit-edit'])) {
                        unset($formData['submit-edit']);
                        unset($formData['groupId']);
                        unset($formData['csrf_items_form']);
                        $itemId = $formData['id'];
                        unset($formData['id']);
                        if (!isset($formData['itemPrice']))
                            $formData['itemActive'] = 1;
                        $formData['itemDescMore'] = str_replace('\"', '', $formData['itemDescMore']);
                        $tableItem->update($formData, array('id' => $itemId));
                        $this->flashMessenger()->addSuccessMessage('Your information was successfully Updated');
                    }
                    if (isset($formData['submit-delete'])) {
                        $itemId = $formData['id'];
                        $tableItem->delete(array('id' => $itemId));
                        $tableGroupItem->delete(array('itemId' => $itemId));
                        $this->flashMessenger()->addSuccessMessage('Your information was successfully deleted');
                    }
                }
            }
            $select = $tableItem->getItem($id);
        }


        $this->viewModel->setTemplate('online-orders/online-orders/item-select');
        return $this->viewModel->setVariables(array(
            'formGroup' => $formGroupList,
            'select' => $select,
            'formNew' => $formNew,
            'formEdit' => $formEdit,
            'viewPage' => $viewPage,
        ));
    }

    public function orderListAction()
    {
        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $admin_route = strpos($route, 'admin') > -1;
        /* @var $tableGroup \OnlineOrders\Model\GroupsTable */
        /* @var $tableItem \OnlineOrders\Model\ItemsTable */
        /* @var $tableGroupItem \OnlineOrders\Model\GroupItemTable */
        /* @var $tableCustomer \OnlineOrders\Model\CustomerTable */
        $tableItem = getSM()->get('items_table');
        $tableGroup = getSM()->get('groups_table');
        $tableGroupItem = getSM()->get('groupItem_table');
        $tableCustomer = getSM()->get('order_customer_table');
        $select = '';
        $dataGroups = array();
        $dataCustomer = array();


        $selectCustomer = $tableCustomer->getAll(null, "ID DESC", null, $this->params()->fromQuery('page', 1));

        foreach ($selectCustomer as $row) {
            $idGroup = $row->idGroup;
            $dataCustomer[$row->ID] = array(
                'ID' => $row->ID,
                'idGroup' => $idGroup,
                'namePer' => $row->namePer,
                'nameCompanyPer' => $row->nameCompanyPer,
                'emailPer' => $row->emailPer,
                'mobilePer' => $row->mobilePer,
            );

            $selectGroups = $tableGroup->get($idGroup);
            if ($selectGroups)
                $dataGroups[$idGroup] = $selectGroups->groupName;
        }

        $this->viewModel->setTemplate('online-orders/online-orders/order-list');
        return $this->viewModel->setVariables(array(
            'selectCustomer' => $selectCustomer,
            'dataCustomer' => $dataCustomer,
            'dataGroups' => $dataGroups,
            'route' => $route,
            'admin_route' => $admin_route

        ));


    }

    public function viewOrdersAction()
    {
        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();

        /* @var $tableGroup \OnlineOrders\Model\GroupsTable */
        /* @var $tableItem \OnlineOrders\Model\ItemsTable */
        /* @var $tableGroupItem \OnlineOrders\Model\GroupItemTable */
        /* @var $table \OnlineOrders\Model\LanguageTable */
        /* @var $tableCustomer \OnlineOrders\Model\CustomerTable */
        $tableLang = getSM()->get('language_online_order_table');
        $tableItem = getSM()->get('items_table');
        $tableGroup = getSM()->get('groups_table');
        $tableGroupItem = getSM()->get('groupItem_table');
        $tableCustomer = getSM()->get('order_customer_table');

        $id = $this->params()->fromRoute('id');
        $data = $tableCustomer->getAll(array('ID' => $id))->toArray();
        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                $formData[$key] = $value;
            }
        }


        $form = new \OnlineOrders\Form\Customer($formData['idGroup'], $formData['resultPrice'], $formData['sumResultPrice']);
        $form->setAttribute('action', url('admin/online-orders/order-list/view-orders', array('id' => $id)));
        $formData['itemCustomer'] = unserialize($formData['itemCustomer']);
        $formData['langCustomer'] = unserialize($formData['langCustomer']);

        $select = $tableItem->getItem($formData['idGroup']);
        $lang = $tableLang->getAll();


        $customItem = new Fieldset();
        $customItem->setName('itemCustomer');
        $customItem->setLabel('itemCustomer');
        foreach ($select as $row) {
            $idGroup = $row->groupId;
            if ($row->itemType == 1) {
                $customItem->add(array(
                    'name' => $row->id,
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => $row->itemName . '<span>' . $row->itemPrice . '</span>'
                    ),
                    'attributes' => array(
                        'class' => 'el-tx',
                    )
                ));
            } else {

                $customItem->add(array(
                    'name' => $row->id,
                    'type' => 'Zend\Form\Element\Checkbox',
                    'options' => array(
                        'label' => $row->itemName . '<span>' . $row->itemPrice . '</span>'
                    ),
                    'attributes' => array(
                        'class' => 'el-ch',
                    )
                ));
            }

        }
        $formCustomer = new \OnlineOrders\Form\Customer($idGroup, 0, 0);
        $formCustomer->add($customItem);


        $customLang = new Fieldset();
        $customLang->setName('langCustomer');
        $customLang->setLabel('langCustomer');
        foreach ($lang as $row) {
            $customLang->add(array(
                'name' => $row->id,
                'type' => 'Zend\Form\Element\Checkbox',
                'options' => array(
                    'label' => $row->langName
                ),
                'attributes' => array(
                    'class' => 'el-lang',
                )
            ));
        }
        $formCustomer->add($customLang);


        $formCustomer->setData($formData);
        $routeAdd = url($route . '/confirmation-orders', array('id' => $id, 'domainName' => $formData['domainName'], 'domainType' => $formData['domainType']));
        $routePrint = url($route . '/print-contract', array('id' => $id));

        $this->viewModel->setTemplate('online-orders/online-orders/view-orders');
        return $this->viewModel->setVariables(array(
            'form' => $formCustomer,
            'routeAdd' => $routeAdd,
            'routePrint' => $routePrint,
        ));
    }

    public function confirmationOrdersAction()
    {
        /* @var $tableCustomer \OnlineOrders\Model\CustomerTable */
        /* @var $tablePerDomains \OnlineOrders\Model\PerDomainsTable */
        $tablePerDomains = getSM()->get('perDomains_table');
        $tableCustomer = getSM()->get('order_customer_table');
        $params = $this->params()->fromPost();
        $id = $this->params()->fromRoute('id');

        switch ($params['domainType']) {
            case 0 :
                $tableCustomer->update(array('confirmation' => 1), array('ID' => $id));
                break;
            case 1 :
                $tablePerDomains->update(array('domainSell' => 1), array('domainName' => $params['domainName']));
                $tableCustomer->update(array('confirmation' => 1), array('ID' => $id));
                break;
        }
        $view = new ViewModel();
        $view->setTerminal(true);
       // $view->setTemplate('online-orders/online-orders/per-domains');
        return $view;
    }

    public function perDomainsAction()
    {
        /* @var $tablePerDomains \OnlineOrders\Model\PerDomainsTable */
        $tablePerDomains = getSM()->get('perDomains_table');
        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $admin_route = strpos($route, 'admin') > -1;
        $formEdit = new \OnlineOrders\Form\PerDomains();
        $formNew = new \OnlineOrders\Form\PerDomains();


        $formNew->remove('submit-delete');
        $formNew->remove('submit-edit');
        $formEdit->remove('submit-create');


        $formData = $this->request->getPost()->toArray();

        $request = $this->getRequest();

        if ($request->isPost()) {

            $formEdit->setData($formData);
            if ($formEdit->isValid()) {
                if (isset($formData['submit-create'])) {
                    unset($formData['submit-create']);
                    unset($formData['csrf_domains_form']);
                    $tablePerDomains->save($formData);
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                }
                if (isset($formData['submit-edit'])) {
                    unset($formData['submit-edit']);
                    unset($formData['csrf_domains_form']);
                    $id = $formData['ID'];
                    unset($formData['ID']);
                    $tablePerDomains->update($formData, array('id' => $id));
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Updated');
                }
                if (isset($formData['submit-delete'])) {
                    $id = $formData['ID'];
                    $tablePerDomains->delete(array('id' => $id));
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully deleted');
                }
            }
        }


        $select = $tablePerDomains->getAll(null, "id DESC", null, $this->params()->fromQuery('page', 1));

        $this->viewModel->setTemplate('online-orders/online-orders/per-domains');
        return $this->viewModel->setVariables(array(
            'select' => $select,
            'formNew' => $formNew,
            'formEdit' => $formEdit,
            'route' => $route,
            'admin_route' => $admin_route
        ));
    }

    public function configAction()
    {
        /* $config = getConfig('onlineOrders')->varValue;
         var_dump($config);*/
        /* @var $config Config */
        $config = getSM('config_table')->getByVarName('onlineOrders');
        $form = prepareConfigForm(new \OnlineOrders\Form\Config());
        if (is_array($config->varValue))
            $form->setData($config->varValue);


        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Online Orders Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Online Orders configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('online-orders/online-orders/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function accountNumberAction()
    {
        /* @var $table \OnlineOrders\Model\accountNumberTable */
        $table = getSM()->get('accountNumber_table');
        $formEdit = new \OnlineOrders\Form\AccountNumber();
        $formNew = new \OnlineOrders\Form\AccountNumber();
        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $admin_route = strpos($route, 'admin') > -1;

        $formNew->remove('submit-delete');
        $formNew->remove('submit-edit');
        $formEdit->remove('submit-create');


        $formData = $this->request->getPost()->toArray();

        $request = $this->getRequest();

        if ($request->isPost()) {

            $formEdit->setData($formData);
            if ($formEdit->isValid()) {
                if (isset($formData['submit-create'])) {
                    unset($formData['submit-create']);
                    unset($formData['csrf_account_number_form']);
                    $table->save($formData);
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                }
                if (isset($formData['submit-edit'])) {
                    unset($formData['submit-edit']);
                    unset($formData['csrf_account_number_form']);
                    $id = $formData['ID'];
                    unset($formData['ID']);
                    $table->update($formData, array('ID' => $id));
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Updated');
                }
                if (isset($formData['submit-delete'])) {
                    $id = $formData['ID'];
                    $table->delete(array('ID' => $id));
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully deleted');
                }
            }
        }


        $select = $table->getAll(null, "id DESC", null, $this->params()->fromQuery('page', 1));

        $this->viewModel->setTemplate('online-orders/online-orders/account-number');
        return $this->viewModel->setVariables(array(
            'select' => $select,
            'formNew' => $formNew,
            'formEdit' => $formEdit,
            'route' => $route,
            'admin_route' => $admin_route
        ));
    }

    public function finalPartOrderAction()
    {

        $params = $this->params()->fromRoute();
        $view = new ViewModel();
        $view->setTemplate('online-orders/online-orders/final-part-order');
        $view->setVariables(array(
            'params' => $params
        ));
        $view->setTerminal(true);
        return $view;
    }

    public function orderTrackingAction()
    {

        /* @var $tableCustomer \OnlineOrders\Model\CustomerTable */
        $tableCustomer = getSM()->get('order_customer_table');
        $confirm = -1;
        $refCode = $this->params()->fromPost('refCode');

        $select = $tableCustomer->getAll(array('refCode' => $refCode));
        foreach ($select as $row) {
            $confirm = $row->confirmation;
        }
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('online-orders/online-orders/order-tracking');
        $view->setVariables(array(
            'confirm' => $confirm
        ));
        return $view;

    }

    public function factorprewAction()
    {
        /* @var $tableItem \OnlineOrders\Model\ItemsTable */
        /* @var $tableCustomer \OnlineOrders\Model\CustomerTable */
        $tableItem = getSM()->get('items_table');
        $tableCustomer = getSM()->get('order_customer_table');
        $id = $this->params()->fromPost('id');

        $data = $tableCustomer->getAll(array('ID' => $id));

        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                $formData[$key] = $value;
            }
        }
        $items = unserialize($formData['itemCustomer']);
        foreach ($items as $key => $value) {
            if ($value == '1') {
                $select = $tableItem->get($key);
                $item[$key] = $select->itemName;
            }
        }

        unset($formData['itemCustomer']);
        unset($formData['langCustomer']);
        $this->viewModel->setTerminal(true);
        $this->viewModel->setTemplate('online-orders/online-orders/factorprew');
        return $this->viewModel->setVariables(
            array(
                'form' => $formData,
                'item' => $item,
            )
        );
    }

    public function printContractAction()
    {
        $id = $this->params()->fromRoute('id');
        /* @var $tableCustomer \OnlineOrders\Model\CustomerTable */
        $tableCustomer = getSM()->get('order_customer_table');

        $select = $tableCustomer->get($id);
        $this->viewModel->setTerminal(true);
        $this->viewModel->setTemplate('online-orders/online-orders/print-contract');
        $this->viewModel->setVariables(array(
            'select' => $select,
            'id' => $id
        ));
        return $this->viewModel;
    }

    public function createCaptchaCodeAction()
    {
        $captcha = new \OnlineOrders\API\Captcha();
        $captcha = $captcha->createCaptcha();
        $this->viewModel->setTerminal(true);
        $this->viewModel->setTemplate('online-orders/online-orders/create-captcha-code');
        return $this->viewModel->setVariables(array(
            'captcha' => $captcha
        ));
    }

    public function outPutAction()
    {
        if ($this->request->getPost()) {
            $param = $this->params()->fromPost();
            if (isset($param['html'])) {
                $path = PUBLIC_FILE . "/onlineorders/pages/";
                $name = 'item_' . rand(1, 10000);
                $filetype = ".html";
                file_put_contents(html_entity_decode($path . $name . $filetype), $param['html']);
                return new JsonModel(array(
                    'status' => 1,
                    'url' => App::siteUrl().'/clients/ipt24/files/onlineorders/pages/'.$name.$filetype,
                ));
            }
        }
        return new JsonModel(array(
            'status' => 0,
        ));
    }
}


