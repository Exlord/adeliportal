<?php

namespace OnlineOrder\Controller;

use Application\API\App;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use Localization\API\Date;
use Mail\API\Mail;
use ServerManager\API\Hosting\Host;
use System\Controller\BaseAbstractActionController;
use System\DB\Installer;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\Adapter;
use Zend\Ldap\Node\RootDse;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class OnlineOrderController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $config = getSM('config_table')->getByVarName('online-order')->varValue;
        $form = prepareConfigForm(new \OnlineOrder\Form\Customer('new', 0));
        $form->setAttribute('action', url('app/online-order/new'));
        $api = new \OnlineOrder\API\OnlineOrder();
        $groups = $api->getOnlineOrderGroups();
        $items = $api->getOnlineOrderItems();
        $count = $api->getOnlineOrderCountDomain();

        //$this->layout('layout/layout-online-order.phtml');

        $this->viewModel->setTemplate('online-order/online-order/index');

        $this->viewModel->setVariables(array(
            'groups' => $groups,
            'items' => $items,
            'count' => $count,
            'form' => $form,
            'config'=>$config,
        ));
        return $this->viewModel;
    }

    public function ordersAction()
    {
        $statusArrayFilter = array('1' => t('Active'), '0' => t('Inactive'));
        $api = new \OnlineOrder\API\OnlineOrder();
        $groupArray = $api->getOnlineOrderGroups();
        $grid = new DataGrid('customer_table');
        $grid->route = 'admin/online-order/orders';
        if (!isAllowed(\OnlineOrder\Module::ADMIN_ONLINE_ORDER_ORDERS_ALL))
            $grid->getSelect()->where(array('userId' => current_user()->id));
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '40px')));
        $grid->setIdCell($id);
        $name = new Column('name', 'Name');
        $company = new Column('company', 'Company Name');
        $email = new Column('email', 'Email');
        $mobile = new Column('mobile', 'Mobile');
        $subDomain = new Column('subDomain', 'Sub Domain Name');

        $domains = new Custom('domains', 'Payment Status', function (Column $col) {
            if ($col->dataRow->payerId) {
                $class = 'done icon-error-done';
                $status = t('Done') . " . Code : " . $col->dataRow->payerId;
            } else {
                $class = 'error icon-error-done';
                $status = t('Not');
            }

            $html = '<div class=tooltip-publish>
                    <div>
                    <label>' . t('Payment Status') . ' : ' . $status . '</label>
                    </div>
            </div>';
            return '<div data-tooltip="' . $html . '" class="' . $class . '" ></div>';
        }, array('attr' => array('align' => 'center')));

        $publish = new Custom('Status', 'Published', function (Column $col) {
            $dateFormat = getSM()->get('viewhelpermanager')->get('DateFormat');
            $class = 'publish-up';
            $status = t('Published');
            $adminMsg = '';
            if ($col->dataRow->publishDown && $col->dataRow->publishUp) {
                if ($col->dataRow->publishDown < time()) {
                    $status = t('Unpublished');
                    $class = 'publish-down';
                }
                if ($col->dataRow->publishUp > time()) {
                    $class = 'future-publish';
                    $status = t('Future Publish');
                }
                if ($col->dataRow->status == 0) {
                    $class = 'error icon-error-done';
                    $adminMsg = t('Have been disabled by an administrator');
                }
                $html = '<div class=tooltip-publish>
                    <div>
                    <label>' . t('Status') . ' : ' . $status . '</label>
                    </div>
                    <div>
                    <label>' . $adminMsg . '</label>
                    </div>
                    <div>
                    <label>' . t('Publish Up') . ' : ' . $dateFormat($col->dataRow->publishUp, 4) . '</label>
                    <br/>
                    <label>' . t('Publish Down') . ' : ' . $dateFormat($col->dataRow->publishDown, 4) . '</label>
                    </div>
            </div>';
            } else {
                $status = t('Not Approved');
                $class = 'error icon-error-done';
                $html = '<div class=tooltip-publish>
                    <div>
                    <label>' . t('Status') . ' : ' . $status . '</label>
                    </div>
                    </div>';
            }

            return '<div data-tooltip="' . $html . '" class="' . $class . '" ></div>';
        }, array('attr' => array('align' => 'center')));

        $confirm = new Select('confirmation', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        /*$status = new Select('status', 'Status',
            $statusArrayFilter,
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );*/

        $extension = new Button('Extension', function (Button $col) {
            $col->route = 'admin/online-order/orders/extension';
            $col->routeParams['id'] = $col->dataRow->id;
            $col->icon = 'glyphicon glyphicon-share-alt';
        }, array(
            'headerAttr' => array(),
            'contentAttr' => array(
                'class' => array( 'btn', 'btn-default','btn-extension'),
                'title' => 'Extension'
            ),
        ));

        /*$extension = new Button('Extension', array(), array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('grid_button', 'search_button', 'btn-extension'))
        ));*/

        $groupFilter = new Column('groupId', 'Groups');
        $groupFilter->selectFilterData = $groupArray;

        $statusFilter = new Column('status', 'Status');
        $statusFilter->selectFilterData = $statusArrayFilter;

        $edit = new EditButton();
        $delete = new DeleteButton();
        $grid->addColumns(array($id, $name, $company, $email, $mobile, $subDomain, $domains, $publish, $confirm, $extension, $edit, $delete));
        $grid->setSelectFilters(array($groupFilter, $statusFilter));
        $grid->addDeleteSelectedButton();

        $this->viewModel->setTemplate('online-order/online-order/orders');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function configAction()
    {
        /* @var $config Config */
        $config = $this->getServiceLocator()->get('config_table')->getByVarName('online-order');
        $mailTemplate = getSM('template_table')->getArray();
        $form = prepareConfigForm(new \OnlineOrder\Form\Config($mailTemplate));
        $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Online Order Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Online Order configs saved successfully');
                }
            }
        }
        $this->viewModel->setTemplate('online-order/online-order/config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $api = new \OnlineOrder\API\OnlineOrder();
        $count = $api->getOnlineOrderCountDomain();
        $allowedAmount = $api->getOnlineOrderAmount();
        $pageType = ''; //values : 'new' or 'edit'
        if ($model) {
            $pageType = 'edit';
            $form = prepareConfigForm(new \OnlineOrder\Form\Customer($pageType, $count[$model->groupId]));
            $form->setAttribute('action', url('admin/online-order/orders/edit', array('id' => $model->id)));
            if ($model->domains)
                $model->domains = unserialize($model->domains);
            $domainModel = $model->domains;
            $groupIdModel = $model->groupId;
            $amountModel = $model->amount;
        } else {
            $pageType = 'new';
            $form = prepareConfigForm(new \OnlineOrder\Form\Customer($pageType, 0));
            $form->setAttribute('action', url('admin/online-order/orders/new'));
            $model = new \OnlineOrder\Model\Customer();
        }
        $form->bind($model);

        if ($this->request->isPost()) {

            $data = $this->request->getPost();
            $form->setData($data);

            if ($form->isValid()) {

                if ($pageType == 'edit') {
                    if (!empty($model->publishUp))
                        $model->publishUp = Date::jalali_to_gregorian($model->publishUp);

                    if (empty($model->publishUp))
                        $model->publishUp = time();

                    if (!empty($model->publishDown))
                        $model->publishDown = Date::jalali_to_gregorian($model->publishDown);

                    if (empty($model->publishDown))
                        $model->publishDown = 0;

                    $redirect = url('admin/online-order/orders');


                    //begin : taghiirat subdomain
                    $newDomains = array();
                    foreach ($data->domains['domain'] as $key => $val) {
                        if ($domainModel['domain'][$key]['domainName'] != $val['domainName']) {
                            $newDomains[] = $val['domainName'];
                            $newDomains[] = 'www.' . $val['domainName'];
                            $beforeDomains[] = $domainModel['domain'][$key]['domainName'];
                            $beforeDomains[] = 'www.' . $domainModel['domain'][$key]['domainName'];
                        }
                    }
                    if (!empty($newDomains)) {
                        $permitToCreate = getSM('client_table')->getSearchDomains($newDomains);
                        if ($permitToCreate) {

                            getSM('site_table')->updateDomains($beforeDomains, $newDomains);
                            getSM('client_table')->updateDomains($beforeDomains, $newDomains);
                            $this->editSiteFilePhp();
                            foreach ($data->domains['domain'] as $key => $val) {
                                if ($domainModel['domain'][$key]['domainName'] != $val['domainName']) {
                                    $data->domains['domain'][$key]['domainName'] = $val['domainName'];
                                }
                            }
                            $this->flashMessenger()->addSuccessMessage('Your domain changes is success');
                        } else
                            $this->flashMessenger()->addErrorMessage('Your domain Name is duplicate');
                    }


                    if ($data->groupId != $groupIdModel) {
                        if ($data->subDomain)
                            $clientDomain = $data->subDomain;
                        elseif (isset($data->domains['domain']) && !empty($data->domains['domain'])) {
                            foreach ($data->domains['domain'] as $key => $val) {
                                if (isset($data->domains['domain'][$key]['domainName']) && $data->domains['domain'][$key]['domainName']) {
                                    $clientDomain = $data->domains['domain'][$key]['domainName'];
                                    break;
                                }
                            }
                        }
                        $selectClient = getSM('client_table')->getAll(array('clientDomain' => $clientDomain))->current();
                        $config = getSM('ApplicationConfig');
                        $config = $config['db'];
                        $config['database'] = $selectClient->dbName;
                        $config['username'] = $selectClient->dbUser;
                        $config['password'] = $selectClient->dbPass;
                        $adapter = new \Zend\Db\Adapter\Adapter($config);
                        $this->createGlobalRealEstateConfig($adapter, $data->groupId, 'update');
                        $this->flashMessenger()->addSuccessMessage('Group change success');

                        //mohasebe hazine
                        $amountGroup = $allowedAmount[$groupIdModel];
                        $additionalAmount = $amountModel - $amountGroup;
                        $model->amount = $additionalAmount + $allowedAmount[$data->groupId];
                        //
                    }
                    //end


                } elseif ($pageType == 'new') {
                    $model->refCode = $api->getRefCode();
                    $model->date = time();

                    $countDomain = 0;
                    if (isset($data['domains']['domain']) && !empty($data['domains']['domain'])) {
                        foreach ($data['domains']['domain'] as $row)
                            $countDomain++;
                        $allowedCount = $count[$model->groupId];

                        if ($countDomain > $allowedCount) {
                            $additionalCount = $countDomain - $allowedCount;
                            $configRow = $this->getServiceLocator()->get('config_table')->getByVarName('online-order');
                            if (isset($configRow->varValue['domainPrice']))
                                $additionalAmount = $configRow->varValue['domainPrice'];
                            if (empty($additionalAmount))
                                $additionalAmount = 0;

                            $model->amount = ($additionalCount * $additionalAmount) + $allowedAmount[$model->groupId];
                        } else
                            $model->amount = $allowedAmount[$model->groupId];
                    }
                }

                $model->domains = serialize($data['domains']);

                $id = getSM()->get('customer_table')->save($model);
                if ($id)
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                if ($pageType == 'new') {

                    //register guest
                    $dataUser = array();
                    if (current_user()->id == 0) {
                        $userCount = getSM()->get('user_table')->getAll(array('username' => $model->email))->count();
                        if ($userCount < 1) {
                            $passUserMain = rand(100, 1000000);
                            $user = new \User\Model\User();
                            $user->username = $model->email;
                            $user->password = $passUserMain;
                            $user->email = $model->email;
                            $user->displayName = $model->name;
                            $userId = getSM('user_table')->save($user);
                            $dataUser = array(
                                'username' => $model->email,
                                'password' => $passUserMain,
                            );
                        }
                    }
                    //end
                    return $this->viewFactorAction($model, $dataUser);

                }
                return $this->redirect()->toUrl($redirect);
            } else {
                $this->flashMessenger()->addErrorMessage('Submission Error: Please enter information carefully');
                return $this->redirect()->toRoute('app/online-order');
            }

        }

        $this->viewModel->setTemplate('online-order/online-order/new');
        $this->viewModel->setVariables(array(
            'form' => $form
        ));
        return $this->viewModel;

    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('customer_table')->get($id);
        $dateFormat = getSM()->get('viewhelpermanager')->get('DateFormat');
        $model->publishUp = $dateFormat($model->publishUp, 3);
        $model->publishDown = $dateFormat($model->publishDown, 3);
        $this->viewModel->setTemplate('online-order/online-order/new');
        return $this->newAction($model);
    }

    public function updateAction()
    {
        /*if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $field && has_value($value)) {
                if ($field == 'confirmation' && $value == 1) {
                    return $this->confirmationAndCreateSiteAction($id);

                }
                if ($field == 'status') {
                    $this->getServiceLocator()->get('customer_table')->update(array($field => $value), array('id' => $id));
                    return new JsonModel(array('status' => 1));
                }
            }
        }*/
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getServiceLocator()->get('customer_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function checkDomainAction()
    {
        if ($this->request->isPost()) {
            $name[] = $this->request->getPost('domainName');
            $count = getSM('client_table')->getSearchDomains($name);
            if ($count)
                return new JsonModel(array('status' => 1));
            else
                return new JsonModel(array('status' => 0));
        }
    }

    public function confirmationAndCreateSiteAction($id)
    {
        set_time_limit(0);
        $api = new \OnlineOrder\API\OnlineOrder();
        $select = $selectSite = getSM('customer_table')->get($id);
        $permitToCreate = false; // baraye ejaze sakhte site

        $siteUrl = str_replace('http://www.', '', App::siteUrl());
        if ($select->subDomain) {
            $subDomain = $select->subDomain . '.' . $siteUrl;
            $domains = array($subDomain);
        } elseif ($select->domains) {
            $select->domains = unserialize($select->domains);

            foreach ($select->domains['domain'] as $val)
                if (!empty($val['domainName']))
                    $domains[] = $val['domainName'];

        }

        $permitToCreate = getSM('client_table')->getSearchDomains($domains);
        if ($permitToCreate) {

            try {

                //sakhte database
                $db_name = $id;
                $db_user = $id;
                $db_pass = rand(10000000000, 100000000000);
                $apiHost = Host::GetApi();
                $result = $apiHost->createDataBase($db_name, $db_user, $db_pass);
                //end

                if ($result->details != 'You have already reached your assigned limit') {

                    $dataSite = array();
                    foreach ($domains as $val) {
                        $domainAlias = str_replace('.', '_', $val);
                        $dataSite[$val] = $domainAlias;
                        $dataSite['www.' . $val] = $domainAlias;
                    }

                    // $idSite1 = getSM('site_table')->multipleSave($dataSite);
                    // TODO : table site niazi nist chon avval bar asase domain ha dar table clients search mishavad

                    if (count($dataSite)) {


                        //virayeshe file site.php
                        $this->editSiteFilePhp($dataSite);
                        //end


                        //create domain pointer

                        foreach ($domains as $val)
                            $apiHost->createDomainPointer($val, $siteUrl);
                        //end


                        if ($result->error)
                            throw new \Exception($result->text . ' . ' . ($result->details));


                        //sakhte pass user login
                        $passLoginAdmin = uniqid();
                        $passLoginUser = rand(100, 1000000);
                        $showPassUser = $passLoginUser;
                        $bcrypt = new Bcrypt();
                        $securePassUser = $bcrypt->create($passLoginUser);
                        $securePassAdmin = $bcrypt->create($passLoginAdmin);
                        //end

                        $db_name = $result->result['dbName'];
                        $db_user = $result->result['dbUser'];
                        $adapter = App::getDbAdapter($result->result['dbName'], $result->result['dbUser'], $db_pass);

                        /* @var $mm \Zend\ModuleManager\ModuleManager */
                        $mm = getSM()->get('ModuleManager');
                        $modules = $mm->getModules();
                        if ($index = array_search('User', $modules))
                            unset($modules[$index]);

                        if ($index = array_search('Components', $modules))
                            unset($modules[$index]);

                        if ($index = array_search('Theme', $modules))
                            unset($modules[$index]);

                        $defaultThemes = getSM('theme_table')->getDefaults();
                        $modules['User'] = array('admin-password' => $securePassAdmin, 'user-password' => $securePassUser);
                        $modules['Components'] = array('groupId' => $select->groupId);
                        $modules['Theme'] = array('themes' => $defaultThemes);

                        $installer = new Installer($adapter, $modules);
                        $installer->install();

                        //sakhte safhe config domain
                        $this->viewModel->setTemplate('online-order/online-order/create-site-config.phtml');
                        $htmlOutput = $this->render($this->viewModel);

                        $htmlOutput = str_replace('{database}', $db_name, $htmlOutput);
                        $htmlOutput = str_replace('{username}', $db_user, $htmlOutput);
                        $htmlOutput = str_replace('{password}', $db_pass, $htmlOutput);

                        $path = ROOT . "/config/clients/";
                        $name = $domainAlias . '.config';
                        $filetype = ".php";
                        file_put_contents(html_entity_decode($path . $name . $filetype), '<?php' . $htmlOutput);
                        // end

                        //sakhte globalConfig dar database
                        $this->createGlobalRealEstateConfig($adapter, $select->groupId);
                        //end


                        //sakhte pooshe chache
                        $dir = ROOT . '/data/' . $domainAlias . '/cache';
                        if (!is_dir($dir))
                            mkdir($dir, 0755, true);
                        //end


                        //sakhte pooshe template
                        $dir = ROOT . '/public_html/clients/' . $domainAlias;
                        if (!is_dir($dir))
                            mkdir($dir, 0755);
                        //end


                        //unzip kardane pushe theme
                        /*  $zip = new ZipArchive;
                          $res = $zip->open(PUBLIC_PATH.'/theme/theme.zip');
                          if ($res === TRUE) {
                              $zip->extractTo(PUBLIC_PATH.'/clients/'.$domainAlias.'/');
                              $zip->close();
                              echo 'Yes';
                          } else {
                              echo 'No';
                          }*/
                        //


                        //zakhire user va pass va moshakhasate digare admin dar database adminserver

                        foreach ($domains as $val) {
                            $dataClient[] = array(
                                'clientName' => $select->name,
                                'clientEmail' => $select->email,
                                'clientDomain' => $val,
                                'dbName' => $db_name,
                                'dbUser' => $db_user,
                                'dbPass' => $db_pass,
                                'diskSpace' => '',
                                'bandwidth' => '',
                                'locked' => 0,
                                'modules' => '',
                                'username' => 'serverAdmin',
                                'password' => $passLoginAdmin,
                                'subDomainUser' => 'admin',
                                'subDomainPass' => $showPassUser,
                            );
                        }

                        $idClient = getSM('client_table')->multipleSave($dataClient);
                        //end

                        $publishDown = strtotime('+1 year', time()); // 1 year = 31556952 seconds
                        getSM('customer_table')->update(array(
                            'confirmation' => 1,
                            'publishUp' => time(),
                            'publishDown' => $publishDown
                        ), array('id' => $id));


                        //region send mail
                        $groups = $api->getOnlineOrderGroups();
                        $items = $api->getOnlineOrderItems();
                        $amount = $api->getOnlineOrderAmount();
                        $form = getSM('customer_table')->get($id);
                        $view = new ViewModel();
                        $view->setTemplate('online-order/online-order/send-factor');
                        $view->setVariables(array(
                            'groups' => $groups,
                            'items' => $items,
                            'form' => $form,
                            'showPassUser' => $showPassUser,
                            'amount' => $amount,
                            'domains' => $domains
                        ));
                        $html = $this->render($view);
                        $config = getSM('config_table')->getByVarName('online-order');
                        if (isset($config->varValue['createSite'])) {
                            $mailTemplateId = $config->varValue['createSite'];
                            $html = App::RenderTemplate($mailTemplateId, array(
                                '__CONTENT__' => $html,
                                '__TIME__' => time(),
                            ));
                        }
                        send_mail(
                            $form->email,
                            Mail::getFrom('mail_config'),
                            t('Successfully built your site'),
                            $html,
                            \OnlineOrder\Module::ENTITY_TYPE,
                            0
                        );
                        //endregion
                        return new JsonModel(array('status' => 1));

                    } else
                        return new JsonModel(array('status' => 0, 'msg' => t('Error : Noting Save')));
                } else
                    return new JsonModel(array('status' => 0, 'msg' => t($result->details)));
                //end

            } catch (\Exception $e) {
                return new JsonModel(array('status' => 0, 'msg' => t($e->getMessage())));
            }

        } else
            return new JsonModel(array('status' => 0, 'msg' => t('duplicate domain name')));

    }

    public function viewFactorAction($model, $dataUser)
    {
        $type = false; // bedanim button pardakht bashad ya nabashad
        $url = '';
        $api = new \OnlineOrder\API\OnlineOrder();
        $groups = $api->getOnlineOrderGroups();
        $items = $api->getOnlineOrderItems();
        if ($model->amount > 0) {
            $paymentParams = array(
                'amount' => $model->amount,
                'email' => $model->email,
                'comment' => 'Pay For Online Order Package',
                'validate' => array(
                    'rote' => 'app/online-order/online-order-validate',
                    'params' => array(
                        'id' => $model->id,
                    ),
                )
            );
            $paymentParams = serialize($paymentParams);
            $paymentParams = base64_encode($paymentParams);
            $url = url('app/payment', array(), array('query' => array('routeParams' => $paymentParams)));
            $type = true;
        }

        $printView = new ViewModel();
        $printView->setTemplate('online-order/online-order/print-factor');
        $printView->setVariables(array(
            'form' => $model,
            'dataUser' => $dataUser,
            'groups' => $groups,
            'items' => $items,
        ));
        $html = $this->render($printView);
        $config = getSM('config_table')->getByVarName('online-order');
        if (isset($config->varValue['RegOrder']) && !empty($config->varValue['RegOrder'])) {
            $mailTemplateId = $config->varValue['RegOrder'];
            $html = App::RenderTemplate($mailTemplateId, array(
                '__CONTENT__' => $html,
                '__TIME__' => time(),
            ));
        }
        send_mail(
            $model->email,
            Mail::getFrom('mail_config'),
            t('Buy a website'),
            $html,
            \OnlineOrder\Module::ENTITY_TYPE,
            0
        );
        $this->viewModel->setTemplate('online-order/online-order/view-factor');
        return $this->viewModel->setVariables(array(
            'html' => $html,
            'type' => $type,
            'url' => $url,
        ));
    }

    public function editSiteFilePhp($dataSite)
    {
        $htmlSite = "<?php \n return array( \n";
        //$selectSite = getSM()->get('site_table')->getAll();
        $sites = include ROOT . '/config/sites.php';
        $selectSite = array_merge($sites, $dataSite);
        foreach ($selectSite as $key => $value) {
            $htmlSite .= "'" . $key . "' => '" . $value . "', \n ";
        }
        $htmlSite .= ');';
        $path = ROOT . "/config/";
        $name = 'sites';
        $filetype = ".php";
        file_put_contents(html_entity_decode($path . $name . $filetype), $htmlSite);
    }

    public function createGlobalRealEstateConfig($adapter, $groupId, $type = 'new')
    {
        $api = new \OnlineOrder\API\OnlineOrder();
        $globalRealEstateConfig = $api->getGlobalRealEstateConfig();
        $changeBanner = $api->getCahngeBanner();
        if ($type == 'new')
            $q = "INSERT INTO `tbl_config` (`varName`, `varValue`) VALUES
                ('global_site_configs','" . serialize(array(
                        'real-estate' => $globalRealEstateConfig[$groupId],
                        'banner' => array(
                            'changeBanner' => $changeBanner[$groupId]
                        )
                    )
                ) . "');";
        elseif ($type == 'update') {
            $q = "SELECT * FROM `tbl_config` WHERE `varName`='global_site_configs';";
            $select = $adapter->query($q, Adapter::QUERY_MODE_EXECUTE);
            foreach ($select as $row) {
                $configId = $row->id;
                $varValue = unserialize($row->varValue);
            }
            $varValue['real-estate'] = $globalRealEstateConfig[$groupId];
            $q = "UPDATE `tbl_config` SET `varValue`='" . serialize($varValue) . "' WHERE `id`='" . $configId . "' ;";
        }
        $adapter->query($q, Adapter::QUERY_MODE_EXECUTE);
    }

    public function subDomainsAction()
    {
        $grid = new DataGrid('client_table');
        $grid->route = 'admin/online-order/sub-domains';
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $clientName = new Column('clientName', 'Name');
        $clientEmail = new Column('clientEmail', 'Email');
        $clientDomain = new Column('clientDomain', 'Domain');
        $dbName = new Column('dbName', 'Database Name');
        $dbUser = new Column('dbUser', 'Database User Name');
        $dbPass = new Column('dbPass', 'PassWord');
        $diskSpace = new Column('diskSpace', 'Disk Space');
        $bandwidth = new Column('bandwidth', 'Band Width');
        $username = new Column('username', 'User Name');
        $password = new Column('password', 'PassWord');
      //  $subDomainUser = new Column('subDomainUser', 'Sub Domain User');
      //  $subDomainPass = new Column('subDomainPass', 'Sub Domain Pass');


        $grid->addColumns(array($id, $clientName, $clientEmail, $clientDomain, $dbName, $dbUser, $dbPass, $diskSpace, $bandwidth, $username, $password/*, $subDomainUser, $subDomainPass*/));
        // $grid->addDeleteSelectedButton();
        $this->viewModel->setTemplate('online-order/online-order/sub-domains');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
        ));
        return $this->viewModel;
    }

    public function onlineOrderValidate()
    {
        $params = $this->params()->fromRoute('params');
        $params = unserialize(base64_decode($params));
        $data = getSM('payment_table')->getStatus($params['payerId']);
        if ($data) {
            if (isset($data['data']['validate']['params']['id'])) {
                getSM('customer_table')->update(array('refCode' => $params['payerId']), array('id' => $data['data']['validate']['params']['id']));
                $config = getSM('config_table')->getByVarName('online-order');
                if (isset($config->varValue['orderValidate'])) {
                    $mailTemplateId = $config->varValue['orderValidate'];
                    $html = App::RenderTemplate($mailTemplateId, array(
                        '__PAYERID__' => $params['payerId'],
                        '__ORDERID__' => $data['data']['validate']['params']['id'],
                        '__TIME__' => time(),
                    ));
                } else {
                    $this->viewModel->setTerminal(true);
                    $this->viewModel->setTemplate('online-order/online-order/online-order-validate');
                    $this->viewModel->setVariables(array(
                        'payerId' => $params['payerId'],
                        'orderId' => $data['data']['validate']['params']['id'],
                        'time' => time(),
                    ));
                    $html = $this->render($this->viewModel);
                }

                send_mail(
                    $data['data']['email'],
                    Mail::getFrom('mail_config'),
                    t('Payment Information'),
                    $html,
                    \OnlineOrder\Module::ENTITY_TYPE,
                    0
                );
                $this->flashMessenger()->addSuccessMessage('A sample has been sent to your email');
                $this->viewModel->setTerminal(false);
                return $this->viewModel;
            } else
                return $this->flashMessenger()->addErrorMessage('Invalid Request !');
        } else
            return $this->flashMessenger()->addErrorMessage('Invalid Request !');
    }

    public function extensionAction()
    {
        $orderId = $this->params()->fromRoute('id');
        if ($orderId) {
            $api = new \OnlineOrder\API\OnlineOrder();
            $count = $api->getOnlineOrderCountDomain();
            $allowedAmount = $api->getOnlineOrderAmount();
            $model = getSM('customer_table')->get($orderId);
            $model->date = time();
            $domains = unserialize($model->domains);
            $countDomain = 0;
            if (isset($domains['domain']) && !empty($domains['domain'])) {
                foreach ($domains['domain'] as $row)
                    $countDomain++;
                $allowedCount = $count[$model->groupId];

                if ($countDomain > $allowedCount) {
                    $additionalCount = $countDomain - $allowedCount;
                    $configRow = getSM('config_table')->getByVarName('online-order');
                    if (isset($configRow->varValue['domainPrice']))
                        $additionalAmount = $configRow->varValue['domainPrice'];
                    if (empty($additionalAmount))
                        $additionalAmount = 0;

                    $model->amount = ($additionalCount * $additionalAmount) + $allowedAmount[$model->groupId];
                } else
                    $model->amount = $allowedAmount[$model->groupId];

                $model->id = null;
                $id = getSM()->get('customer_table')->save($model);
                $model->id = $id;
                if ($id) {
                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    return $this->viewFactorAction($model, array());
                }
            }
        } else {
            $this->flashMessenger()->addErrorMessage('Invalid Request !');
            return $this->redirect()->toRoute('admin/online-order/orders');
        }
    }

}


