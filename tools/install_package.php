<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/14/13
 * Time: 9:22 AM
 */
set_time_limit(0);
define('ROOT', str_replace('\\', '/', dirname(__DIR__)));
define("MODULE_DIR", ROOT . '/module');
define("SOURCE_DIR", ROOT . '/source');
define("ARCHIVE_DIR", SOURCE_DIR . '/archive');
define("LANG_DIR", ROOT . '/language');
define('TMP_DIR', ROOT . '/tmp');
chdir(ROOT);
include ROOT . '/init_autoloader.php';

include_once MODULE_DIR . '/System/src/System/IO/Directory.php';
include_once MODULE_DIR . '/System/src/System/Filter/Compress/ExZip.php';


$defaultModules = array('System', 'Application', 'Assetic', 'AssetManager', 'Category', 'Components', 'Cron', 'DataView', 'File', 'GeographicalAreas', 'JQuery', 'Localization', 'Logger', 'Mail', 'Menu', 'Notify', 'Page', 'SiteMap', 'Theme', 'Thumbnail', 'User');
$modules = \System\IO\Directory::getDirs(MODULE_DIR, false, array_merge(array('Sample'), $defaultModules));
$langs = \System\IO\Directory::getDirs(LANG_DIR);

function unzip($file, $destination)
{
    $filter = new \Zend\Filter\Decompress(array(
        'adapter' => 'Zip',
        'options' => array(
            'target' => $destination,
            'archive' => $file,
        )
    ));
    $result = $filter->filter($file);
}

?>
    <style>
        td{ border-bottom:solid 1px darkgray; }
    </style>
    <form method="post">
        <table cellspacing="5" cellpadding="5">
            <tr>
                <td>Alias</td>
                <td>
                    no space, don't start with number, all english
                    <br/>
                    <input type="text" name="alias" value="<?= @$_POST['alias'] ?>">
                </td>
            </tr>
            <tr>
                <td>Domains</td>
                <td>
                    1 domain per row
                    <br/>
                    <textarea cols="20" rows="5" name="sites"><?= @$_POST['sites'] ?></textarea>
                </td>
            </tr>
            <tr>
                <td>DataBase Name</td>
                <td><input type="text" name="db_name" value="<?= @$_POST['db_name'] ?>"></td>
            </tr>
            <tr>
                <td>DataBase UserName</td>
                <td><input type="text" name="db_user" value="<?= @$_POST['db_user'] ?>"></td>
            </tr>
            <tr>
                <td>DataBase Password</td>
                <td><input type="text" name="db_pass" value="<?= @$_POST['db_pass'] ?>"></td>
            </tr>
            <tr>
                <td>Modules</td>
                <td>
                    <? foreach ($modules as $m) {
                        $checked = false;
                        if (@in_array($m, @$_POST['modules']))
                            $checked = 'checked="checked"';
                        ?>
                        <input type="checkbox" name="modules[]" value="<?= $m ?>" <?= $checked ?>> <?= $m ?> <br/>
                    <? } ?>
                </td>
            </tr>
            <tr>
                <td>Languages</td>
                <td>
                    <? foreach ($langs as $l) {
                        $checked = false;
                        if (@in_array($l, @$_POST['languages']))
                            $checked = 'checked="checked"';
                        ?>
                        <input type="checkbox" name="languages[]" value="<?= $l ?>" <?= $checked ?>> <?= $l ?> <br/>
                    <? } ?>
                </td>
            </tr>
            <tr>
                <td>ServerAdmin Password</td>
                <td><input type="text" name="serverAdmin" value="<?= @$_POST['serverAdmin'] ?>"></td>
            </tr>
            <tr>
                <td>Admin Password</td>
                <td><input type="text" name="admin" value="<?= @$_POST['admin'] ?>"></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="submit" value="create"></td>
            </tr>
        </table>
    </form>

<?
$post = $_POST;
if (isset($post['submit'])) {
    $alias = $post['alias'];

    //------------------------------ create base folders
    $dirs = array(
        TMP_DIR . '/' . $alias,
        TMP_DIR . '/' . $alias . '/config',
        TMP_DIR . '/' . $alias . '/config' . '/autoload',
        TMP_DIR . '/' . $alias . '/config' . '/clients',
        TMP_DIR . '/' . $alias . '/data',
        TMP_DIR . '/' . $alias . '/data/' . $alias,
        TMP_DIR . '/' . $alias . '/data/' . $alias . '/cache',
        TMP_DIR . '/' . $alias . '/data/' . $alias . '/files',
        TMP_DIR . '/' . $alias . '/language',
        TMP_DIR . '/' . $alias . '/module',
        TMP_DIR . '/' . $alias . '/public_html',
        TMP_DIR . '/' . $alias . '/public_html/clients',
        TMP_DIR . '/' . $alias . '/public_html/clients/' . $alias,
        TMP_DIR . '/' . $alias . '/public_html/clients/' . $alias,
        TMP_DIR . '/' . $alias . '/public_html/clients/' . $alias . '/files',
    );
    $packageFolder = TMP_DIR . '/' . $alias;

    foreach ($dirs as $d) {
        if (!is_dir($d))
            mkdir($d, 0755, true);
    }
    //------------------------------ put empty files in empty folders
    file_put_contents(TMP_DIR . '/' . $alias . '/public_html/clients/' . $alias . '/files/empty', 'junk');
    file_put_contents(TMP_DIR . '/' . $alias . '/data/' . $alias . '/cache/empty', 'junk');
    file_put_contents(TMP_DIR . '/' . $alias . '/data/' . $alias . '/files/empty', 'junk');

    //------------------------------ a list of selected modules
    $modules = array();
    foreach ($post['modules'] as $m) {
        $modules[] = $m;
    }

    //------------------------------ get latest core zip file and unzip it
    $coreDirs = \System\IO\Directory::getFiles(ARCHIVE_DIR . '/core');
    $latestCoreFile = '0';
    $latestZendFile = '0';
    foreach ($coreDirs as $f) {
        $f_parts = explode('-', $f);
        if ($f_parts[0] == 'core') {
            if (version_compare($f_parts[1], $latestCoreFile) == 1)
                $latestCoreFile = $f_parts[1];

        } else {
            if (version_compare($f_parts[1], $latestZendFile) == 1)
                $latestZendFile = $f_parts[1];
        }
    }
    echo('Core version : ' . $latestCoreFile . '<br/>');
    unzip(ARCHIVE_DIR . '/core/core-' . $latestCoreFile . '-source.zip', $packageFolder);
    echo('Zend version : ' . $latestZendFile . '<br/>');
    unzip(ARCHIVE_DIR . '/core/zend-' . $latestZendFile . '-source.zip', $packageFolder);

    //------------------------------ get latest selected languages
    $languages = $_POST['languages'];
    $languageFiles = \System\IO\Directory::getFiles(ARCHIVE_DIR . '/language');
    $latestLangs = array();
    foreach ($languages as $l) {
        $latestLangs[$l] = '0';
    }
    foreach ($languageFiles as $f) {
        $f_parts = explode('-', $f);
        if (in_array($f_parts[0], $languages)) {
            if (version_compare($f_parts[1], $latestLangs[$f_parts[0]]) == 1)
                $latestLangs[$f_parts[0]] = $f_parts[1];

        }
    }
    foreach ($latestLangs as $l => $v) {
        echo('Language (' . $l . ') version : ' . $v . '<br/>');
        unzip(ARCHIVE_DIR . '/language/' . $l . '-' . $v . '-source.zip', $packageFolder . '/language');
    }

    //------------------------------ get latest selected modules
    $latestModules = array();
    $moduleFiles = \System\IO\Directory::getFiles(ARCHIVE_DIR . '/module');
    $modules = array_merge($defaultModules, $modules);
    foreach ($modules as $m) {
        $latestModules[$m] = '0';
    }
    foreach ($moduleFiles as $f) {
        $f_parts = explode('-', $f);
        if (in_array($f_parts[0], $modules)) {
            if (version_compare($f_parts[1], $latestModules[$f_parts[0]]) == 1)
                $latestModules[$f_parts[0]] = $f_parts[1];

        }
    }
    foreach ($latestModules as $m => $v) {
        echo('Module (' . $m . ') version : ' . $v . '<br/>');
        unzip(ARCHIVE_DIR . '/module/' . $m . '-' . $v . '-source.zip', $packageFolder . '/module');
    }

    //------------------------------ create config file
    $stringModules = $modules;
    if ($index = array_search('System', $stringModules))
        unset($stringModules[$index]);
    array_unshift($stringModules, 'System');
    array_walk($stringModules, function (&$item, $index) {
        $item = "'" . $item . "'";
    });
    $stringModules = implode(",\n", $stringModules);
    $config = "array(
        'modules' => array(
            $stringModules
        ),
        'db' => array(
            'driver' => 'Pdo_Mysql',
            'database' => '{$post['db_name']}',
            'hostname' => 'localhost',
            'driver_options' => array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \\'UTF8\\''
            ),
            'username' => '{$post['db_user']}',
            'password' => '{$post['db_pass']}',
        ),
    );";
    $config = "<? return " . $config;
    file_put_contents($packageFolder . '/config/clients/' . $alias . '.config.php', $config);

    //------------------------------ make sites file
    $sites = explode("\n", $post['sites']);
    $sitesFile = array('default' => $alias);
    foreach ($sites as $s) {
        $sitesFile[str_replace(array("\n", "\r", "\n\r", "\r\n"), '', $s)] = $alias;
    }
    $sitesFile = "<? return " . var_export($sitesFile, true) . ";";
    file_put_contents($packageFolder . '/config/sites.php', $sitesFile);


    //------------------------------ zip the files into a package
    $filter = new \Zend\Filter\Compress(array(
        'adapter' => 'System\Filter\Compress\ExZip',
        'options' => array(
            'archive' => TMP_DIR . '/' . $alias . '.zip',
            'ignore' => array(
                'self' => true,
            )
        ),
    ));
    $file = $filter->filter($packageFolder);


    //------------------------------ make modules install script
    $moduleFiles = \System\IO\Directory::getDirs($packageFolder . '/module');
    $installScript = "";
    foreach ($moduleFiles as $m) {
        $installFile = $packageFolder . '/module/' . $m . '/install.sql';
        if (file_exists($installFile))
            $installScript .= file_get_contents($installFile);
    }

    //------------------------------ make user password and install script
    $bcrypt = new \Zend\Crypt\Password\Bcrypt();
    $serverAdmin = $bcrypt->create($_POST['serverAdmin']);
    $admin = $bcrypt->create($_POST['admin']);
    $q = "\nINSERT INTO `tbl_users` (`username`, `password`, `accountStatus`, `displayName`) VALUES
                ('serverAdmin','" . $serverAdmin . "',1,'serverAdmin'),
                ('admin','" . $admin . "',1,'admin');";

    $installScript .= $q;

    //------------------------------ make modules list install script
    $values = array();
    foreach ($latestModules as $m => $v) {
        $values[] = "('" . $m . "','" . $v . "')";
    }
    $placeHolder = implode(",\n", $values);
    $q = "\n\ninsert into `tbl_modules` (`name`,`dbVersion`) values \n" . $placeHolder . ";";
    $installScript .= $q;

    //-------------------------------make language install script
    $langNames = array(
        'fa' => 'فارسی',
        'en' => 'English'
    );
    $values = array();
    foreach ($latestLangs as $l => $v) {
        $values[] = "('" . $l . "','" . $langNames[$l] . "', 1)";
    }
    $placeHolder = implode(",\n", $values);
    $q = "\n\nINSERT INTO `tbl_languages` (`langSign`, `langName`, `status`) VALUES \n $placeHolder;";
    $installScript .= $q;
    //------------------------------ remove the files
    file_put_contents(TMP_DIR . '/' . $alias . '.sql', $installScript);
    \System\IO\Directory::clear($packageFolder, true);
}