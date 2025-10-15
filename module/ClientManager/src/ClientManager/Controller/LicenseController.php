<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ClientManager\Controller;

use ClientManager\Model\License;
use ClientManager\Model\LicenseTable;
use DataView\Lib\DataGrid;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;

class LicenseController extends BaseAbstractActionController
{
    /**
     * Create a license file by accepting a license file and checking its permissions
     */
    public function makeWithLicenseAction()
    {
    }

    /**
     * Create a license file by checking permission on acl
     */
    public function makeWithAclAction()
    {

    }

    /**
     * @return LicenseTable
     */
    private function getLicenseTable()
    {
        return getSM('license_table');
    }
}
