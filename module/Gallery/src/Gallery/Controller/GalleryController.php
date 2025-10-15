<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Gallery\Controller;

use Application\API\App;
use DataView\Lib\Button;
use Mail\API\Mail;
use Theme\API\Themes;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use File\API\File;
use Localization\API\Date;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class GalleryController extends BaseAbstractActionController
{
    public function indexAction($type = null)
    {
        if (!$type)
            $type = $this->params()->fromRoute('type');
        $grid = new DataGrid('gallery_table');
        $grid->route = 'admin/' . $type . '/groups';
        $grid->routeParams = array('type' => $type);
        $grid->getSelect()->where(array('type' => $type));
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $title = new Column('groupName', 'Name');
        $desc = new Column('groupText', 'Description');

        $publish = new Custom('Status', 'Publish', function (Column $col) {
            $dateFormat = getSM()->get('viewhelpermanager')->get('DateFormat');
            $class = 'glyphicon glyphicon-ok-circle text-success grid-icon';
            $status = t('Published');
            if ($col->dataRow->publishDown < time()) {
                $status = t('Unpublished');
                $class = 'glyphicon glyphicon-remove-circle text-danger grid-icon';
            }

            if ($col->dataRow->publishUp > time()) {
                $class = 'glyphicon glyphicon-calendar text-info grid-icon';
                $status = t('Future Publish');
            }


            // $col->attr['class'][] =$class;
            $html = '<div class=tooltip-publish>
                    <div>
                    <label>' . t('Status') . ' : ' . $status . '</label>
                    </div>
                    <div>
                    <label>' . t('Publish Up') . ' : ' . $dateFormat($col->dataRow->publishUp, 4) . '</label>
                    <br/>
                    <label>' . t('Publish Down') . ' : ' . $dateFormat($col->dataRow->publishDown, 4) . '</label>
                    </div>
            </div>';


            return '<div data-tooltip="' . $html . '" class="' . $class . '" ></div>';
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
        ), true);

        $status = new Select('status', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $reloadType = new Select('reloadType', 'Reload Type',
            array('0' => t('Page Load'), '30' => t('30 Second'), '60' => t('60 Second'), '300' => t('5 Minuets'), '600' => t('10 Minuets'), '900' => t('15 Minuets')),
            array(),
            array('headerAttr' => array('width' => '100px'))
        );

        $displayType = new Custom('', 'Type of Display', function (Column $col) {
            $displayType = unserialize($col->dataRow->config);
            if ($displayType['displayType'] == 1)
                $message = t('Random');
            elseif ($displayType['displayType'] == 2)
                $message = t('Order');
            return $message;
        });

        $edit = new EditButton();
        $delete = new DeleteButton();

        $columnArray = array();
        $columnArray[] = $id;
        $columnArray[] = $title;
        $columnArray[] = $desc;

        if ($type == 'banner' || $type == 'imageBox') {
            $columnArray[] = $displayType;
            $columnArray[] = $reloadType;
            $columnArray[] = $status;
            $columnArray[] = $publish;
        } else {
            $columnArray[] = $status;
        }

        $columnArray[] = $edit;
        $columnArray[] = $delete;

        $grid->addColumns($columnArray);
        $grid->addNewButton('New Group');
        $grid->addNewButton('Items', 'Items', false, 'admin/' . $type . '/item');
        $grid->addDeleteSelectedButton();

        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
            'type' => $type,
        ));
        $this->viewModel->setTemplate('gallery/gallery/index');
        return $this->viewModel;
    }

    public function newAction($model = false)
    {
        $oldImage = '';
        $groupId = '';
        $type = $this->params()->fromRoute('type');
        $bannerPosition = array();
        if ($type == 'banner')
            $bannerPosition = getSM('banner_size_table')->getViewPosition();
        $form = new \Gallery\Form\Gallery($type, $bannerPosition);

        if (!$model) {
            $model = new \Gallery\Model\Gallery();
            $form->setAttribute('action', url('admin/' . $type . '/groups/new', array('type' => $type)));
        } else {
            $form->setAttribute('action', url('admin/' . $type . '/groups/edit', array('type' => $type, 'id' => $model->id)));
            $form->get('buttons')->remove('submit-new');
            $oldImage = $model->image;
            $model->config = unserialize($model->config);
            $groupId = $model->id;
        }
        $form->setAttribute('data-cancel', url('admin/' . $type, array('type' => $type)));
        $form->bind($model);

        if ($this->request->isPost()) {
            ini_set('memory_limit', '128M');
            $post = $this->params()->fromPost();
            $post = array_merge($post, $this->request->getFiles()->toArray());

            if (isset($post['buttons']['cancel'])) {
                return $this->indexAction($type);
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {

                $form->setData($post);

                if ($form->isValid()) {

                    if (!empty($model->publishUp))
                        $model->publishUp = Date::jalali_to_gregorian($model->publishUp);

                    if (empty($model->publishUp))
                        $model->publishUp = time();

                    if (!empty($model->publishDown))
                        $model->publishDown = Date::jalali_to_gregorian($model->publishDown);

                    if (empty($model->publishDown))
                        $model->publishDown = 0;

                    $model->type = $type;

                    $file = $model->image;

                    if ($type == 'gallery') {
                        if (isset($file['name']) && !empty($file['name'])) {
                            $model->image = File::MoveUploadedFile($file['tmp_name'], PUBLIC_FILE . '/Gallery/' . $type, $file['name']);
                            $model->fileType = $file['type'];
                            if ($oldImage)
                                @unlink(PUBLIC_PATH . $oldImage);
                        } else
                            $model->image = $oldImage;
                    }

                    $model->config = serialize($model->config);

                    $id = getSM()->get('gallery_table')->save($model);

                    if ($type == 'banner') {
                        //get User Info
                        $selectUser = getSM('user_table')->getUser(current_user()->id, array('table' => array('email'), 'profile' => 'mobile'));
                        //end
                        $position = getSM('banner_size_table')->getPositionById($model->position);
                        //insert banner table
                        $bannerModel = new \Gallery\Model\Banner();
                        $bannerModel->groupId = $id;
                        $bannerModel->mobile = $selectUser['mobile'];
                        $bannerModel->email = $selectUser['email'];
                        $bannerModel->position = $position;
                        $bannerModel->created = time();
                        $bannerModel->expire = $model->publishDown;
                        getSM('banner_table')->save($bannerModel);
                        //end
                    }

                    $this->flashMessenger()->addSuccessMessage('Your information was successfully Saved');
                    $this->getServiceLocator()->get('logger')->log(LOG_INFO, "new gallery with id:$id is created");

                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->indexAction($type);
                    } else
                        $form->setData(array());
                }
            }
        }

        $this->viewModel->setTemplate('gallery/gallery/new');
        $this->viewModel->setVariables(array(
            'form' => $form,
            'oldImage' => $oldImage,
            'groupId'=>$groupId,
            'type'=>$type
        ));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $model = getSM()->get('gallery_table')->get($id);
        $dateFormat = getSM()->get('viewhelpermanager')->get('DateFormat');
        $model->publishUp = $dateFormat($model->publishUp, 3);
        $model->publishDown = $dateFormat($model->publishDown, 3);

        return $this->newAction($model);
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $field) {
                if ($field == 'status') {
                    $this->getServiceLocator()->get('gallery_table')->update(array($field => $value), array('id' => $id));
                    return new JsonModel(array('status' => 1));
                }
                if ($field == 'reloadType') {
                    $this->getServiceLocator()->get('gallery_table')->update(array($field => $value), array('id' => $id));
                    return new JsonModel(array('status' => 1));
                }
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $selectItem = getSM('gallery_item_table')->getAll(array('groupId' => $id));
                foreach ($selectItem as $row) {
                    if ($row->image)
                        unlink(PUBLIC_PATH . $row->image);
                    getSM('gallery_item_table')->remove($id);
                }
                getSM('gallery_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function bannerConfigAction()
    {
        $config = getSM('config_table')->getByVarName('banner_config');
        $mailTemplate = getSM('template_table')->getArray();

        $position = Themes::getBlockPositions();
        $form = prepareConfigForm(new \Gallery\Form\BannerConfig($position, $mailTemplate));
        $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getServiceLocator()->get('config_table')->save($config);
                    db_log_info("Banner Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Banner configs saved successfully');
                }
            }
        }

        $this->viewModel->setTemplate('gallery/gallery/banner-config');
        $this->viewModel->setVariables(array('form' => $form));
        return $this->viewModel;
    }

    public function orderBannerAction()
    {
        $config = getSM('config_table')->getByVarName('banner_config')->varValue;
        $dataArray = getSM('banner_size_table')->getArray();
        $position = getSM('banner_size_table')->getPosition();
        $expirePosition = getSM('banner_table')->getFirstExpireDateGroup($position);
        $countPosition = getSM('banner_table')->getCountBannerPosition($position);

        foreach ($countPosition as $key => $val)
            if (isset($config['countPosition'][$key]) && $config['countPosition'][$key])
                $config['countPosition'][$key] = $config['countPosition'][$key] - $val;

        $positionForm = array();
        foreach ($dataArray as $row)
            $positionForm[$row['id']] = $row['viewPosition'];
        $form = prepareConfigForm(new \Gallery\Form\OrderBanner($positionForm));

        $model = new \Gallery\Model\OrderBanner();
        $form->bind($model);

        if ($this->request->isPost()) {

            $post = $this->request->getPost();
            $postFiles = $this->request->getFiles()->toArray();
            $postFiles = $postFiles['banner_image_box']['imageBox'];
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());

                if ($form->isValid()) {
                    //Upload Image
                    $dataImage = array();
                    $countImage = 0;
                    foreach ($postFiles as $row) {
                        if (isset($row['image']['name']) && $row['image']['name']) {
                            $dataImage[] = array(
                                'url' => $this->getFileApi()->MoveUploadedFile($row['image']['tmp_name'], PUBLIC_FILE . '/orderBanner', $row['image']['name']),
                                'type' => $row['image']['type'],
                            );
                            $countImage++;
                        }
                    }
                    //end
                    if ($countImage > 0) {
                        $model->images = serialize($dataImage);
                        //get Price
                        if (isset($dataArray[$model->position]['price']) && $dataArray[$model->position]['price'])
                            $amount = (int)$dataArray[$model->position]['price'];
                        else
                            $amount = 0;
                        $amount = $amount * (int)$model->countMonth;

                        if (isset($dataArray[$model->position]['addPrice']) && $dataArray[$model->position]['addPrice'])
                            $imageCost = (int)$dataArray[$model->position]['addPrice'];
                        else
                            $imageCost = 0;

                        if ($countImage > 1)
                            $amount = $amount + (($countImage - 1) * (int)$imageCost);
                        $model->price = $amount;
                        //end
                        $model->date = time();
                        $model->userId = current_user()->id;
                        $id = getSM('order_banner_table')->save($model);
                        if ($id) {
                            db_log_info("Order Banner Configs changed");
                            $this->flashMessenger()->addInfoMessage(sprintf(t('s% is the tracking code for your order. Please do not forget to track your order, order code.'), $id));
                            $this->flashMessenger()->addSuccessMessage('Your order has been successfully completed.');
                            //payment for order banner
                            $paymentParams = array(
                                'amount' => $amount,
                                'email' => $model->email,
                                'comment' => 'Pay For Create a Banner Block',
                                'validate' => array(
                                    'route' => 'app/order-banner-validate',
                                    'params' => array(
                                        'id' => $id,
                                    ),
                                )
                            );
                            $paymentParams = serialize($paymentParams);
                            $paymentParams = base64_encode($paymentParams);
                            return $this->redirect()->toRoute('app/payment', array(), array('query' => array('routeParams' => $paymentParams)));
                            //end
                        } else
                            $this->flashMessenger()->addErrorMessage('Registration problem. Please, please try again.');
                    } else
                        $this->flashMessenger()->addErrorMessage('At least one image must be selected.');

                }
            }
        }
        $this->viewModel->setTemplate('gallery/gallery/order-banner');
        $this->viewModel->setVariables(array(
            'form' => $form,
            'dataArray' => $dataArray,
            'expirePosition' => $expirePosition,
            'countPosition' => $config['countPosition'],
        ));
        return $this->viewModel;
    }

    public function orderBannerValidate()
    {
        $params = $this->params()->fromRoute('params');
        $params = unserialize(base64_decode($params));
        $data = getSM('payment_table')->getStatus($params['payerId']);
        if ($data) {
            if (isset($data['data']['validate']['params']['id'])) {
                getSM('order_banner_table')->update(array('payerCode' => $params['payerId']), array('id' => $data['data']['validate']['params']['id']));
                $config = getSM('config_table')->getByVarName('banner_config');
                if (isset($config->varValue['orderBannerValidate'])) {
                    $mailTemplateId = $config->varValue['orderBannerValidate'];
                    $html = App::RenderTemplate($mailTemplateId, array(
                        '__PAYERID__' => $params['payerId'],
                        '__ORDERID__' => $data['data']['validate']['params']['id'],
                        '__TIME__' => time(),
                    ));
                } else {
                    $this->viewModel->setTerminal(true);
                    $this->viewModel->setTemplate('gallery/gallery/order-banner-validate');
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

    public function orderBannerListAction()
    {
        $statusArrayFilter = array('1' => t('Approved'), '0' => t('Not Approved'));
        $grid = new DataGrid('order_banner_table');
        $grid->route = 'admin/banner/list';
        if (!isAllowed(\Gallery\Module::ADMIN_BANNER_LIST_ALL))
            $grid->getSelect()->where(array('userId' => current_user()->id));
        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '40px')));
        $grid->setIdCell($id);
        $name = new Column('name', 'Name');
        $title = new Column('title', 'Title');
        $url = new Column('url', 'Url Address');
        $email = new Column('email', 'Email');
        $mobile = new Column('mobile', 'Mobile');

        $payerCode = new Custom('payerCode', 'Payment Status', function (Column $col) {
            if ($col->dataRow->payerCode) {
                $class = 'done icon-error-done';
                $status = t('Done') . " . Code : " . $col->dataRow->payerCode;
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

        $status = new Select('status', 'Status',
            array('0' => t('Not Approved'), '1' => t('Approved')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );


        $extension = new Button('Extension', array(), array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('grid_button', 'search_button', 'btn-extension'))
        ));

        $statusFilter = new Column('status', 'Status');
        $statusFilter->selectFilterData = $statusArrayFilter;

        $edit = new EditButton();
        $delete = new DeleteButton();
        $grid->addColumns(array($id, $name, $title, $email, $mobile, $url, $payerCode, $status, $extension, $edit, $delete));
        $grid->setSelectFilters(array($statusFilter));
        $grid->addDeleteSelectedButton();
        $this->viewModel->setTemplate('gallery/gallery/order-banner-list');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function orderBannerUpdateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $field) {
                if ($field == 'status' && (int)$value == 1) {
                    $select = getSM('order_banner_table')->get($id);
                    //create gallery model
                    $modelGallery = new \Gallery\Model\Gallery();
                    $modelGallery->groupName = t('Order Banner') . '_' . $select->id;
                    $modelGallery->groupText = t('Order Banner') . '_' . $select->id;
                    $modelGallery->publishUp = time();
                    $timeStr = '+ ' . $select->countMonth . ' month';
                    $modelGallery->publishDown = strtotime($timeStr, time());
                    $modelGallery->status = 1;
                    $modelGallery->reloadType = 0;
                    $modelGallery->type = 'banner';
                    $modelGallery->position = $select->position;
                    $modelGallery->config = serialize(array(
                        'transparent' => 0,
                        'displayType' => 1,
                    ));
                    $galleryId = getSM('gallery_table')->save($modelGallery);
                    //end

                    if ($galleryId) {
                        //create gallery items
                        $images = unserialize($select->images);
                        foreach ($images as $val) {
                            $modelGalleryItem = new \Gallery\Model\GalleryItem();
                            $modelGalleryItem->groupId = $galleryId;
                            $modelGalleryItem->order = 0;
                            $modelGalleryItem->hits = 0;
                            $modelGalleryItem->url = $select->url;
                            $modelGalleryItem->image = $val['url'];
                            $modelGalleryItem->alt = $select->title;
                            $modelGalleryItem->title = '';
                            $modelGalleryItem->status = 1;
                            $modelGalleryItem->type = 'banner';
                            $modelGalleryItem->fileType = $val['type'];
                            $size = getSM('banner_size_table')->getSize($select->position);
                            $modelGalleryItem->width = $size['width'];
                            $modelGalleryItem->height = $size['height'];
                            getSM('gallery_item_table')->save($modelGalleryItem);
                        }
                        //end
                        //insert banner table
                        $bannerModel = new \Gallery\Model\Banner();
                        $bannerModel->groupId = $galleryId;
                        $bannerModel->mobile = $select->mobile;
                        $bannerModel->email = $select->email;
                        $position = getSM('banner_size_table')->getPositionById($select->position);
                        $bannerModel->position = $position;
                        $bannerModel->created = time();
                        $bannerModel->expire = strtotime($timeStr, time());
                        getSM('banner_table')->save($bannerModel);
                        //end

                        getSM('order_banner_table')->update(array('status' => 1), array('id' => $select->id));

                        return new JsonModel(array('status' => 1));
                    } else
                        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
                }
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    public function orderBannerDeleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $select = getSM('order_banner_table')->get($id);
                $images = unserialize($select->images);
                foreach ($images as $val) {
                    unlink(PUBLIC_PATH . $val['url']);
                }
                getSM('order_banner_table')->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function orderBannerEditAction()
    {
        $id = $this->params()->fromRoute('id');
        $images = array();
        if ($id) {
            $model = getSM('order_banner_table')->get($id);
            $images = unserialize($model->images);
            if ($model->status != 0) {
                $this->flashMessenger()->addErrorMessage('You may not edit because the order has already been approved.');
                $callBack = url('admin/banner/list');
                $callBack = sprintf('window.location="%s";', $callBack);
                return new JsonModel(array(
                    'status' => 1,
                    'callback' => $callBack
                ));
            }
        } else
            $model = new \Gallery\Model\OrderBanner();

        $dataArray = getSM('banner_size_table')->getArray();
        $positionForm = array();
        foreach ($dataArray as $row)
            $positionForm[$row['id']] = $row['viewPosition'];
        $form = prepareConfigForm(new \Gallery\Form\OrderBanner($positionForm, 'edit'));
        $form->setAttribute('action', url('admin/banner/list/edit', array('id' => $id)));
        $form->bind($model);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $postFiles = $this->request->getFiles()->toArray();
            $postFiles = $postFiles['banner_image_box']['imageBox'];
            $dataImage = array();
            $form->setData($this->request->getPost());
            if ($form->isValid()) {

                //Upload Image
                $countImage = 0;
                foreach ($postFiles as $row) {
                    if (isset($row['image']['name']) && $row['image']['name']) {
                        $dataImage[] = array(
                            'url' => $this->getFileApi()->MoveUploadedFile($row['image']['tmp_name'], PUBLIC_FILE . '/orderBanner', $row['image']['name']),
                            'type' => $row['image']['type'],
                        );
                        $countImage++;
                    }
                }
                //end

                $selectOrder = getSM('order_banner_table')->get($post->id);
                $saveImages = unserialize($selectOrder->images);
                $saveImages = array_merge($saveImages, $dataImage);
                $model->images = serialize($saveImages);
                getSM('order_banner_table')->save($model);
                $this->flashMessenger()->addSuccessMessage('Your information was successfully Updated');
                return $this->orderBannerListAction();
            }
        }
        $this->viewModel->setTemplate('gallery/gallery/order-banner-edit');
        $this->viewModel->setVariables(array(
            'form' => $form,
            'images' => $images,
        ));
        return $this->viewModel;

    }

    public function orderBannerDeleteImageAction()
    {
        if ($this->request->isPost()) {
            $url = $this->params()->fromPost('url', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $url) {
                $select = getSM('order_banner_table')->get($id);
                $images = unserialize($select->images);
                $data = array();
                foreach ($images as $row)
                    if ($row['url'] != $url)
                        $data[] = array(
                            'url' => $row['url'],
                            'type' => $row['type'],
                        );
                unlink(PUBLIC_PATH . $url);
                getSM('order_banner_table')->update(array('images' => serialize($data)), array('id' => $id));
                return new JsonModel(array('status' => 1));
            }
        }
        return new JsonModel(array('status' => 0));
    }

    public function extensionAction()
    {
        $data = $this->params()->fromPost();
        if ((isset($data['id']) && $data['id']) && (isset($data['countMonth']) && $data['countMonth'])) {
            $model = getSM('order_banner_table')->get($data['id']);
            $images = unserialize($model->images);
            if ($images > 0) {

                //get Price
                $selectBannerSize = getSM('banner_size_table')->get($model->position);
                if (isset($selectBannerSize->price) && $selectBannerSize->price)
                    $pricePosition = (int)$selectBannerSize->price;
                else
                    $pricePosition = 0;

                if (isset($selectBannerSize->addPrice) && $selectBannerSize->addPrice)
                    $imageCost = (int)$selectBannerSize->addPrice;
                else
                    $imageCost = 0;


                $amount = $pricePosition * (int)$data['countMonth'];
                if (count($images) > 1)
                    $amount = $amount + ((count($images) - 1) * (int)$imageCost);
                $model->price = $amount;
                $model->date = time();
                $model->id = null;
                $model->status = 0;
                $id = getSM('order_banner_table')->save($model);
                if ($id) {
                    db_log_info("Order Banner Configs changed");
                    $this->flashMessenger()->addInfoMessage(sprintf(t('s% is the tracking code for your order. Please do not forget to track your order, order code.'), $id));
                    $this->flashMessenger()->addSuccessMessage('Your order has been successfully completed.');
                    //payment for order banner
                    $paymentParams = array(
                        'amount' => $amount,
                        'email' => $model->email,
                        'comment' => 'Pay For Create a Banner Block',
                        'validate' => array(
                            'route' => 'app/order-banner-validate',
                            'params' => array(
                                'id' => $id,
                            ),
                        )
                    );
                    $paymentParams = serialize($paymentParams);
                    $paymentParams = base64_encode($paymentParams);
                    return $this->redirect()->toUrl(url('app/payment', array(), array('query' => array('routeParams' => $paymentParams))));
                    //end
                } else
                    $this->flashMessenger()->addErrorMessage('Registration problem. Please, please try again.');
            }
        } else
            $this->flashMessenger()->addErrorMessage('Invalid Request !');

        return $this->orderBannerListAction();
    }

    public function galleryPageListAction()
    {
        $term = $this->params()->fromQuery('term');
        $data = getSM('gallery_table')->search($term);
        $json = array();
        foreach ($data as $row) {
            $json[] = array(
                'id' => $row->id,
                'title' => $row->groupName,
            );
        }
        return new JsonModel($json);
    }

    public function deleteImgAction()
    {
        $groupId = $this->params()->fromPost('groupId');
        if ($groupId) {
            $selectGroup = getSM('gallery_table')->get($groupId);
            if ($selectGroup) {
                $image = $selectGroup->image;
                @unlink(ROOT . $image);
                getSM('gallery_table')->update(array('image' => null,'alt'=>null), array('id' => $groupId));
                return new JsonModel(array(
                    'status' => 1
                ));
            }
        }
        return new JsonModel(array(
            'status' => 0
        ));
    }

}
