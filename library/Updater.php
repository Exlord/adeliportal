<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/26/13
 * Time: 10:25 AM
 */
use Zend\Http\Request;
use Zend\Http\Client;

class Updater
{
    const NO_FILE = "File name not provided for download !";
    const NO_HASH = "File hash not provided !";
    const INVALID_HASH = "The provided hash is not matched to the file's hash !";
    const FILE_NOT_FOUND = "The Requested file not found for download";

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    private $db = null;
    private $config;
    private $messages = array();
    private $errors = array();
    private $output = array('status' => 'OK');
    private $license = null;

    private $loading = "<img class='loading' src='/images/ajax_loader_tiny.gif'>";
    private $ok = "<img src='/images/done.png'>";
    private $nok = "<img src='/images/dialog-error.png'>";
    private $indent = "<span class='indent'> - </span>";

    private $updateInfo = null;

    private $updateServer = "http://ipt24.ir";
//    private $updateServer = "http://iptcms";
    private $unpack = true;
    private $host;

    public function __construct()
    {
        if (!is_dir(TMP_DIR))
            mkdir(TMP_DIR, 0755, true);
        $this->config = include ROOT . '/config/application.config.php';
        $this->db = new \Zend\Db\Adapter\Adapter($this->config['db']);

        $this->updateInfo = @$_SESSION['updateInfo'];

        $licenseFile = ROOT . '/data/' . ACTIVE_SITE . '/license';
        if (file_exists($licenseFile))
            $this->license = file_get_contents($licenseFile);
        else {
            die('License Not Found ! <a href="http://ipt24.ir">buy a license</a>');
        }

        if (file_exists(ROOT . '/local')) {
            $config = json_decode(file_get_contents(ROOT . '/local'));
            if ($config) {
                if (isset($config->unpack))
                    $this->unpack = $config->unpack;
            }
        }

        $this->host = sprintf(
            "%s://%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['HTTP_HOST']
        );;
    }

    public function messages()
    {
        return implode('<br/>', $this->messages) . '<br/>';
    }

    public function output()
    {
        $this->output['messages'] = $this->messages();
        if (count($this->errors)) {
            $this->output['error'] = implode('<br/>', $this->errors) . '<br/>';
        }
        if (!isset($this->output['step'])) {
            if (!isset($this->output['keep-session']) || $this->output['keep-session'] != true)
                session_destroy();
        }
        echo(json_encode($this->output));
    }

    public function processRequest()
    {
        $op = @$_GET['op'];

        switch ($op) {
            //step0
            default:
                $this->start();
                break;
            case 'downloadUpdateInfo':
                $this->downloadUpdateInfo();
                break;
            case 'analyzeUpdateInfo':
                $this->analyzeUpdateInfo();
                break;
            case 'downloadAndUnpack':
                $this->downloadAndUnpack();
                break;
            case 'checkForDbUpdate':
                $this->checkForDbUpdate();
                break;
            case 'createDbBackup':
                $this->createDbBackup();
                break;
            case 'installDbUpdates':
                $this->installDbUpdates();
                break;
            case 'done':
                $this->done();
                break;

        }
    }

    private function getSystemVersion()
    {
        return parse_ini_file(ROOT . '/system.ini', true);
    }

    //------------------------------------STEPS----------------------------------------------

    private function start()
    {
        $request = new Request();
        $request->setUri($this->host . '/before-update');

        $client = new Client();
        $client->setEncType('application/x-www-form-urlencoded');

        $response = false;
        try {
            /* @var $response \Zend\Http\Response */
            $response = $client->dispatch($request);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            if ($e->getPrevious())
                $this->errors[] = $e->getPrevious()->getMessage();
        }

        if ($response && $response->isSuccess()) {
            $result = $response->getBody();
            if ($result[0] == '{') {
                $result = json_decode($result);
                if ($result->status == 'done') {
                    $this->messages[] = "system cached data cleared" . $this->ok;
                }
            } else
                $this->messages[] = $result . $this->nok;
        } else {
            if ($response)
                $this->messages[] = $response->getBody() . $this->nok;
            else
                $this->messages[] = "Got NULL response while waiting for cache clearing" . $this->nok;
        }

        $this->messages[] = "Updating System";
        $this->messages[] = 'Downloading update information from update server' . $this->loading;
        $this->output['step'] = 'downloadUpdateInfo';
    }

    private function downloadUpdateInfo()
    {
        $updateInfoOK = false;
        $updateInfo = null;

        $request = new Request();
        $request->setUri($this->updateServer . '/check-for-update');
        $request->setMethod('POST');
        $request->getPost()->set('license', $this->license);

        $client = new Client();
        $client->setEncType('application/x-www-form-urlencoded');

        $response = false;
        try {
            /* @var $response \Zend\Http\Response */
            $response = $client->dispatch($request);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            if ($e->getPrevious())
                $this->errors[] = $e->getPrevious()->getMessage();
        }

        if ($response && $response->isSuccess()) {
            $updateInfo = $response->getBody();
            if ($updateInfo[0] == '{') {
                $updateInfoOK = true;
            }
        }

        if ($updateInfoOK) {
            $_SESSION['updateInfo'] = $updateInfo;
            $this->messages[] = 'Analyzing downloaded update information' . $this->loading;
            $this->output['step'] = 'analyzeUpdateInfo';
        } else {
            echo "Error : update information is unavailable.expected json data but we got :<br/>" . var_export($updateInfo, true);
            exit;
        }
    }

    private function analyzeUpdateInfo()
    {
        $updateInfo = json_decode($this->updateInfo);
        if (isset($updateInfo->status) && $updateInfo->status == "false") {
            $this->messages[] = $this->indent . $updateInfo->msg;
            $this->messages[] = "Checking for DataBase updates" . $this->loading;
            $this->output['step'] = 'checkForDbUpdate';
            return;
        }

        $version = $this->getSystemVersion();

        $downloadAndUnpack = array();
        //check for zend update
        if (version_compare(\Zend\Version\Version::VERSION, $updateInfo->core->zend->version) == -1) {
            $file = 'zend-' . $updateInfo->core->zend->version . '-source.zip';
            $downloadAndUnpack['core'][$file] = $updateInfo->core->zend->hash;
        } else {
            $this->messages[] = $this->indent . "Core/Zend is up to date";

            //check for system core update
            if (version_compare($version['version'], $updateInfo->core->system->version) == -1) {
                $file = 'core-' . $updateInfo->core->system->version . '-source.zip';
                $downloadAndUnpack['core'][$file] = $updateInfo->core->system->hash;
            } else {
                $this->messages[] = $this->indent . "Core/System is up to date";

                //check for modules update
                $result = $this->_getLocalModules();
                if ($result) {
                    foreach ($_SESSION['localModules'] as $name => $version) {
                        if (isset($updateInfo->module->{$name})) {
                            if (version_compare($version, $updateInfo->module->{$name}->version) == -1) {
                                $file = $name . '-' . $updateInfo->module->{$name}->version . '-source.zip';
                                $downloadAndUnpack['module'][$file] = $updateInfo->module->{$name}->hash;
                            }
                        }
                    }
                }

                $this->_getLanguages();
                foreach ($_SESSION['languages'] as $name => $version) {
                    if (isset($updateInfo->language->{$name})) {
                        if (version_compare($version, $updateInfo->language->{$name}->version) == -1) {
                            $file = $name . '-' . $updateInfo->language->{$name}->version . '-source.zip';
                            $downloadAndUnpack['language'][$file] = $updateInfo->language->{$name}->hash;
                        }
                    }
                }
            }
        }

        if (count($downloadAndUnpack)) {
            $this->messages[] = 'Downloading and unpacking' . $this->loading;
            $_SESSION['downloadAndUnpack'] = json_encode($downloadAndUnpack);
            $this->output['step'] = 'downloadAndUnpack';
        } else {
            $this->messages[] = "Checking for DataBase updates" . $this->loading;
            $this->output['step'] = 'checkForDbUpdate';
        }
    }

    private function downloadAndUnpack()
    {
        $downloadAndUnpack = json_decode($_SESSION['downloadAndUnpack']);

        $isCoreUpdate = false;
        if (count($downloadAndUnpack)) {
            foreach ($downloadAndUnpack as $type => $files) {
                if ($type == 'core')
                    $isCoreUpdate = true;
                foreach ($files as $file => $hash) {
                    $dl = $this->_download($type, $file, $hash);
                    if ($dl)
                        $this->_unpack($type, $file);
                }
            }
        }

        if ($isCoreUpdate) {
            $this->messages[] = 'Analyzing downloaded update information' . $this->loading;
            $this->output['step'] = 'analyzeUpdateInfo';
        } else {
            $this->messages[] = "Checking for DataBase updates" . $this->loading;
            $this->output['step'] = 'checkForDbUpdate';
        }
    }

    private function checkForDbUpdate()
    {
        $result = $this->_getLocalModules();
        if ($result) {
            $localFileVersions = $_SESSION['localModules'];
            $dbVersions = $_SESSION['installedModules'];

            $updatedDbModules = array();
            foreach ($dbVersions as $name => $installedVersion) {
                $newVersion = $localFileVersions[$name];
                if (version_compare($installedVersion, $newVersion) == -1) {
                    $updatedDbModules[$name] = array('oldVersion' => $installedVersion, 'newVersion' => $newVersion);
                    $this->messages[] = $this->indent . "db update available for " . $name . ' ' . $newVersion;
                }
            }

            if (count($updatedDbModules)) {
                $_SESSION['updatedDbModules'] = json_encode($updatedDbModules);
                $this->messages[] = 'backing up database ' . $this->loading;
                $this->output['step'] = 'createDbBackup';
                return;
            } else {
                $this->messages[] = 'Database is up to date' . $this->ok;
                $this->output['step'] = 'done';
                return;
            }
        }
        $this->messages[] = "Error checking for database updates" . $this->nok;
        $this->output['step'] = 'done';
    }

    private function createDbBackup()
    {
        $date = date(DATE_RFC2822);
        $request = new Request();
        $request->setUri($this->host . '/en/db-backup');
        $request->setMethod('POST');
        $request->getPost()->set('comment', "backup before db update ");

        $response = false;

        $client = new Client();
        $client->setEncType('application/x-www-form-urlencoded');
        try {
            /* @var $response \Zend\Http\Response */
            $response = $client->dispatch($request);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            if ($e->getPrevious()) {
                $this->errors[] = $e->getPrevious()->getMessage();
            }
        }

        $backupOk = false;
        $error = '';
        if ($response && $response->isSuccess()) {
            $result = $response->getBody();
            if ($result[0] == '{') {
                $result = json_decode($result);
                if ($result->done == true) {
                    $this->messages[] = $this->indent . "db backup created @" . $date . ' ,' . $result->file . $this->ok;
                    $this->messages[] = 'installing db updates ' . $this->loading;
                    $this->output['step'] = 'installDbUpdates';
                    $backupOk = true;
                }
            } else
                $this->errors[] = $result;
        } else {
            if ($response)
                $this->errors[] = $response->getBody();
            else
                $this->errors[] = "Got NULL response while waiting for db backup creation";
        }

        if (!$backupOk) {
            $this->messages[] = $this->indent . "unable to create db backup" . $this->nok;
            $this->messages[] = "<a href='#continue' class='continue' data-step='installDbUpdates' data-msg='installing db updates'>continue installing db updates without backing up database OR I've already created a database backup</a>";
            $this->output['status'] = false;
            $this->output['keep-session'] = true;
        }
    }

    private function installDbUpdates()
    {
        $tableName = $this->db->platform->quoteIdentifier('tbl_modules');
        $versionColumn = $this->db->platform->quoteIdentifier('dbVersion');
        $nameColumn = $this->db->platform->quoteIdentifier('name');
        $q = "update {$tableName} set {$versionColumn}=? where {$nameColumn}=?";
        include_once ROOT . '/module/System/src/System/DB/BaseInstall.php';
        foreach (json_decode($_SESSION['updatedDbModules']) as $name => $versions) {
            $newVersion = $versions->newVersion;
            $version = $versions->oldVersion;
            $installFile = ROOT . '/module/' . $name . '/src/' . $name . '/Install.php';
            $className = $name . '\Install';
            if (is_file($installFile)) {
                include_once $installFile;
                if (class_exists($className)) {
                    /* @var $installer \System\DB\BaseInstall */
                    $installer = new $className($this->db, $name);
                    $error = $installer->update($version, $newVersion);
                    if (count($error)) {
                        $this->errors = array_merge($this->errors, $error);
                    }
                    $this->messages[] = $this->indent . $name . " updated" . $this->ok;
                } else
                    $this->messages[] = $this->indent . $className . ' Not found' . $this->nok;
            } else {
                $this->messages[] = $this->indent . $installFile . ' Not found' . $this->nok;
            }
            $this->db->query($q, array($newVersion, $name));
        }

        $this->messages[] = 'Finished ' . $this->loading;
        $this->output['step'] = 'done';
    }

    private function done()
    {
        $this->messages[] = $this->indent . "Go to <a href='/fa/admin/updates'>Admin Area</a>";
        $this->messages[] = $this->indent . "Go to <a href='/fa/updates'>Client Area</a>";
    }

    private function _download($type, $file, $hash)
    {
        $downloaded = false;
        $filePatch = TMP_DIR . '/' . $type . '/' . $file;
        if (file_exists($filePatch) && md5_file($filePatch) == $hash) {
            $this->messages[] = $this->indent . $this->indent . $file . ' already downloaded.' . $this->ok;
            $downloaded = true;
        } else {
            $url = $this->updateServer . '/get-update-file/' . $type . '/' . $file . '/' . $hash;
            $contents = file_get_contents($url);
            if (!$contents) {
                $this->messages[] = $this->indent . $this->indent . 'Error downloading update file "' . $file . '" ' . $this->nok;
            } else {
                if ($contents == self::NO_HASH ||
                    $contents == self::NO_FILE ||
                    $contents == self::INVALID_HASH ||
                    $contents == self::FILE_NOT_FOUND
                ) {
                    $this->messages[] = $this->indent . $this->indent . $file . '&nbsp;&nbsp;' . $contents . ' ' . $this->nok;
                } else {
                    if (!is_dir(TMP_DIR . '/' . $type))
                        mkdir(TMP_DIR . '/' . $type, 0755, true);
                    file_put_contents($filePatch, $contents);
                    $dlHash = md5($contents);
                    if ($dlHash != $hash) {
                        $this->messages[] =
                            $this->indent . $this->indent . 'Hash check failed for the downloaded file  "' . $file . '" .' . $this->nok;
                        unlink($filePatch);
                    } else {
                        $this->messages[] = $this->indent . $this->indent . $file . ' downloaded.' . $this->ok;
                        $downloaded = true;
                    }
                }
            }
        }

        return ($downloaded);
    }

    private function _unpack($type, $file)
    {
        $fileName = TMP_DIR . '/' . $type . '/' . $file;
        if (!file_exists($fileName)) {
            $this->messages[] = $this->indent . $this->indent . 'Update file not found !' . $fileName . $this->nok;
            return;
        }

        if ($this->unpack) {
            switch ($type) {
                case 'core':
                    $target = ROOT;
                    break;
                case 'module':
                    $target = MODULE_DIR;
                    break;
                case 'language':
                    $target = ROOT . '/language';
            }

            $filter = new \Zend\Filter\Decompress(array(
                'adapter' => 'Zip',
                'options' => array(
                    'target' => $target,
                    'archive' => $fileName,
                )
            ));
            $result = $filter->filter($fileName); //result is the file decompress patch

            if (str_replace('\\', '/', $target) . '/' != str_replace('\\', '/', $result)) {
                $this->messages[] = $this->indent . $this->indent . 'There was on error unpacking the file "' . $fileName . '"' . $this->nok;
            } else {
                $fileNameParts = explode('-', $fileName);
                $this->messages[] = $this->indent . $this->indent . end(explode('/', $fileNameParts[0])) . ' ' . $fileNameParts[1] . ' installed.' . $this->ok;
            }
        } else {
            $this->messages[] = $this->indent . $this->indent . "skipped unpacking " . $file;
        }
        unlink($fileName);
    }

    private function _getLocalModules()
    {
        $modules = array();
        $result = $this->_getInstalledModules();
        if ($result) {
            foreach ($_SESSION['installedModules'] as $name => $dbVersion) {
                $configFile = MODULE_DIR . '/' . $name . '/module.ini';
                if (file_exists($configFile)) {
                    $config = parse_ini_file(MODULE_DIR . '/' . $name . '/module.ini');
                    $version = $config['version'];
                    $modules[$name] = $version;
                } else {
                    $this->messages[] = $this->indent . "ini file not found for " . $name;
                }
            }
            $_SESSION['localModules'] = $modules;
            return true;
        } else
            return false;
    }

    private function _getInstalledModules()
    {
        $q = "select * from tbl_modules";
        $result = $this->db->query($q)->execute();
        if ($result && $result->count()) {
            $modules = array();
            foreach ($result as $m) {
                $modules[$m['name']] = $m['dbVersion'];
            }
            $_SESSION['installedModules'] = $modules;
            return true;
        } else {
            $this->messages[] = $this->indent . "Unable to load modules list from db " . $this->nok;
            return false;
        }
    }

    private function _getLanguages()
    {
        $langs = array();
        $dirs = \System\IO\Directory::getDirs(ROOT . '/language');
        foreach ($dirs as $l) {
            $ini = ROOT . '/language/' . $l . '/lang.ini';
            $version = '1.0';
            if (file_exists($ini)) {
                $version = parse_ini_file($ini);
                $version = $version['version'];
            }
            $langs[$l] = $version;
        }
        $_SESSION['languages'] = $langs;
    }
}