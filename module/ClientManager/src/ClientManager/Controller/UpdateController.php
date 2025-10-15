<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ClientManager\Controller;

use ClientManager\API\License;
use ClientManager\Model\LicenseTable;
use DataView\Lib\DataGrid;
use System\Controller\BaseAbstractActionController;
use System\IO\Directory;
use \Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use \Zend\View\Model\ViewModel;

class UpdateController extends BaseAbstractActionController
{
    public function checkForUpdateAction()
    {
//        var_dump($_SERVER['REMOTE_ADDR']);
//        var_dump(gethostbyaddr($_SERVER['REMOTE_ADDR']));
//        var_dump(gethostbynamel('ipt24.ir'));
//        exit;

        $ip = $_SERVER['REMOTE_ADDR'];
        $allowedAddresses = array('130.185.72.107', '127.0.0.1', '91.109.17.61');
        $allowedAddresses[] = '188.158.242.195'; //my local ip

        $license = $this->params()->fromPost('license', false);
        if (!$license) {
            db_log_warning('attempted to update a client without submitting license, from ' . $ip);
            echo('Stolen and unlicensed codes are not allowed to update their client.');
            exit;
        }

        $license = $this->getLicenseTable()->getByLicense($license);
        if (!$license) {
            db_log_warning('attempted to update a client with invalid license, from ' . $ip);
            echo('Stolen and unlicensed codes are not allowed to update their client.');
            exit;
        }

        /* @var $licenseFile \ClientManager\Model\License */
        $licenseFile = License::decrypt($license->data, $license->key);
        if (!$licenseFile) {
            db_log_warning('unable to decrypt the license file');
            echo('We are unable to verify the license at this moment.');
            exit;
        }

        if (!in_array($ip, $allowedAddresses)) {
            db_log_warning('on unauthorized ip address tried to update its client from ' . $ip .
                ' the client id found in license is ' . $licenseFile->clientId);
            echo('Unauthorized ip detected.');
            exit;
        }

        if ($licenseFile->clientId != $license->clientId || $licenseFile->expireDate != $license->expireDate) {
            db_log_warning('attempted to update a client with invalid license, from ' . $ip .
                '.<br/>the license content dose not match the data stored in database' .
                '<br/>the license id found is : ' . $license->id .
                '<br/>the license value is : <br/>' . $license->data);
            echo('Stolen and unlicensed codes are not allowed to update their client.');
            exit;
        }

        if ($license->expireDate < time()) {
            db_log_warning('attempted to update a client with expired license, from ' . $ip);
            echo('your license is expired and you are not allowed to update your client.');
            exit;
        }

        if (!in_array($license->type, License::$allowedToUpdateTypes)) {
            return new JsonModel(array("status" => "false", 'msg' => "you don't have file update permission"));
        }

//        $modules = array();
//        $files = Directory::getFiles(ROOT . '/source');
//        foreach ($files as $f) {
//            $f_parts = explode('-', $f);
//            $name = $f_parts[0];
//            $version = $f_parts[1];
//            if (isset($modules[$name])) {
//                $otherVersion = $modules[$name]['version'];
//                if (version_compare($version, $otherVersion) == -1)
//                    continue;
//            }
//            $modules[$name] = array('version' => $version, 'hash' => md5_file(ROOT . '/source/' . $f));
//        }
//        return new JsonModel($modules);

        echo file_get_contents(ROOT . '/source/update-info');
        exit;
    }

    public function getUpdateFileAction()
    {
        include_once ROOT . '/library/Updater.php';
        $type = $this->params()->fromRoute('type', false);
        $file = $this->params()->fromRoute('file', false);
        $hash = $this->params()->fromRoute('hash', false);
        if (!$file) {
            echo(\Updater::NO_FILE);
            die();
        }

        if (!$hash) {
            echo(\Updater::NO_HASH);
            die();
        }

        $file = ROOT . '/source/' . $type . '/' . $file;
        if (file_exists($file)) {
            if (md5_file($file) != $hash) {
                echo(\Updater::INVALID_HASH);
                die();
            }
        }else{
            echo(\Updater::FILE_NOT_FOUND);
            die();
        }

        echo(file_get_contents($file));
        exit;
    }

    /**
     * @return LicenseTable
     */
    private function getLicenseTable()
    {
        return getSM('license_table');
    }
}
