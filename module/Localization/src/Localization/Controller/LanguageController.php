<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Localization\Controller;


use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Select;
use Localization\Model\LanguageTable;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;


class LanguageController extends BaseAbstractActionController
{
    public function indexAction()
    {
        $grid = new DataGrid('language_table');
        $grid->route = 'admin/languages';

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);

        $langName = new Column('langName', 'Name');
        $langSign = new Column('langSign', 'Sign');

        $status = new Select('status', 'Status',
            array('0' => t('Inactive'), '1' => t('Active')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px')));
        $status->lockedValue = 2;

        $default = new Select('default', 'Default',
            array('0' => t('Inactive'), '1' => t('Active')),
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px')));
        $default->lockedValue = 2;


        // $del = new DeleteButton();
        // $edit = new EditButton();

        $grid->addColumns(array($id, $langName, $langSign, $status, $default));
        // $grid->addDeleteSelectedButton();
        // $grid->addNewButton('New Block');

        $this->viewModel->setTemplate('localization/language/index');
        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
            ));
        return $this->viewModel;


    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);

            if ($id && $field && has_value($value)) {
                $langTable = $this->getTable();

                if ($field == 'status' && $value != 2) {
                    $langTable->update(array($field => $value), array('id' => $id));
                    return new JsonModel(array(
                        'status' => 1,
                    ));
                }
                if ($field == 'default') {

                    $langTable->update(array($field => 0));
                    if ($value == '0') {
                        $langTable->setDefaultByLang('fa');
                    } elseif ($value == '1')
                        $langTable->setDefaultById($id);

                    $lang = $langTable->getDefault(true);
                    $callBack = url('admin/languages', array('lang' => $lang));
                    $callBack = sprintf('window.location="%s";', $callBack);

                    return new JsonModel(array(
                        'status' => 1,
                        'callback' => $callBack
                    ));
                }
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    /**
     * @return LanguageTable
     */
    private function getTable()
    {
        return getSM('language_table');
    }
}
