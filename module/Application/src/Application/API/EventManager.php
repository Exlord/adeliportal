<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/24/14
 * Time: 2:54 PM
 */

namespace Application\API;


use Application\API\Backup\Db;
use Application\Model\Config;
use Application\Model\DbBackup;
use Application\Model\DbBackupTable;
use Components\API\Block;
use Components\Form\NewBlock;
use Cron\API\Cron;
use Menu\Form\MenuItem;
use System\API\BaseAPI;
use Zend\EventManager\Event;
use Zend\Form\Fieldset;
use Zend\Mvc\MvcEvent;

class EventManager extends BaseAPI
{
    public function loadingDashboard($controller)
    {
        $widgets = new Widgets($controller);
        $this->getEventManager()->trigger('Dashboard.Load', $widgets);
        return $widgets;
    }

    public function onLoadNotificationBar(Event $e)
    {
        $updates = false;
        $icon = 'glyphicon glyphicon-download fa-lg';
        if ($updates) {
            $title = t('There are updates available for your system');
            $icon .= ' text-success';
        } else {
            $title = t('System is up to date.');
            $icon .= ' text-muted';
        }
        $e->getTarget()->addMenu($icon, $title, 0);
    }

    public function onCronRun(Config $last_run)
    {
        $config = Db::getConfig();

        //region automated db backup
        if ($config['newDbBackupInterval']) {
            $start = microtime(true);
            $interval = $config['newDbBackupInterval'];

            $last = @$last_run->varValue['Application_newDbBackup_last_run'];

            if (Cron::ShouldRun($interval, $last)) {
                $api = $this->getDbBackupApi();
                $fileName = $api->backup();
                if ($fileName) {
                    $model = new DbBackup();
                    $model->userId = 0;
                    $model->created = time();
                    $model->file = $fileName;
                    $model->size = $api->fileSize;
                    $model->comment = t('automated cron backup');
                    $this->getDbBAckupTable()->save($model);
                    db_log_info(sprintf(t('Automated Database backup cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
                    $last_run->varValue['Application_newDbBackup_last_run'] = time();
                } else {
                    db_log_error($api->error);
                }
            }
        }
        //endregion

        //region Delete backups older than X
        if ($config['dbBackupCleanupInterval'] && $config['dbBackupCleanupInterval'] != '-1') {
            $start = microtime(true);
            $interval = '+6 month';
            $last = @$last_run->varValue['Application_dbBackupCleanup_last_run'];
            if (Cron::ShouldRun($interval, $last)) {

                $date = @strtotime($config['dbBackupCleanupInterval']);
                if ($date) {
                    $backups = $this->getDbBAckupTable()->getOldBackupsByDate($date);
                    if ($backups && $backups->count()) {
                        $ids = array();
                        foreach ($backups as $backup) {
                            $ids[] = $backup->id;
                            $this->getDbBackupApi()->delete($backup->file, $backup->size);
                        }
                        $this->getDbBAckupTable()->remove($ids);

                        db_log_info(sprintf(t('Automated Database backup cleanup(files older than X) cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
                        $last_run->varValue['Application_dbBackupCleanup_last_run'] = time();
                    }
                }
            }
        }
        //endregion

        //region dbBackupMaxCount
        $maxCount = (int)$config['dbBackupMaxCount'];
        if ($maxCount) {
            $start = microtime(true);
            $interval = '+6 month';
            $last = @$last_run->varValue['Application_dbBackupMaxCount_last_run'];
            if (Cron::ShouldRun($interval, $last)) {
                $backups = $this->getDbBAckupTable()->getBackupsOlderThanByCount($maxCount);
                if ($backups && $backups->count()) {
                    $ids = array();
                    foreach ($backups as $backup) {
                        $ids[] = $backup->id;
                        $this->getDbBackupApi()->delete($backup->file, $backup->size);
                    }
                    $this->getDbBAckupTable()->remove($ids);

                    db_log_info(sprintf(t('Automated Database backup cleanup(files more than X) cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
                    $last_run->varValue['Application_dbBackupMaxCount_last_run'] = time();
                }
            }
        }
        //endregion

        //region dbBackupMaxSize
        $maxSize = (int)$config['dbBackupMaxSize'];
        if ($maxSize) {
            $start = microtime(true);
            $interval = '+6 month';
            $last = @$last_run->varValue['Application_dbBackupMaxSize_last_run'];
            if (Cron::ShouldRun($interval, $last)) {

                $totalSize = $this->getDbBackupApi()->getTotalSize();
                if ($totalSize > $maxSize) {
                    $last = $this->getDbBAckupTable()->getLast();
                    if ($last && $last = $last->current()) {
                        $extraSize = $totalSize - $maxSize;
                        $extraItemsCount = (int)(($extraSize / $last->size) / 4 * 3);
                        $backups = $this->getDbBAckupTable()->getOldBackupsByCount($extraItemsCount);

                        if ($backups && $backups->count()) {
                            $ids = array();
                            foreach ($backups as $backup) {
                                $ids[] = $backup->id;
                                $this->getDbBackupApi()->delete($backup->file, $backup->size);
                            }
                            $this->getDbBAckupTable()->remove($ids);

                            db_log_info(sprintf(t('Automated Database backup cleanup(files larger than X) cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
                            $last_run->varValue['Application_dbBackupMaxSize_last_run'] = time();
                        }
                    }
                }
            }
        }
        //endregion

        //region Database Optimization
        $start = microtime(true);
        $interval = '+1 month';
        $last = @$last_run->varValue['Application_database_optimization_last_run'];
        if (Cron::ShouldRun($interval, $last)) {
            $q = "SHOW TABLES";
            $tables = App::getDbAdapter()->createStatement($q)->execute()->getResource()->fetchAll();
            $qTemp = "OPTIMIZE TABLE %s;\n";
            foreach ($tables as $t) {
                $table = current($t);
                $q = sprintf($qTemp, App::getDbAdapter()->getPlatform()->quoteIdentifier($table));
                App::getDbAdapter()->query($q)->execute();
            }

            db_log_info(sprintf(t('Automated Database optimization cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
            $last_run->varValue['Application_database_optimization_last_run'] = time();
        }
        //endregion

        //region Cache Cleaner
        $start = microtime(true);
        $interval = '+1 month';
        $last = @$last_run->varValue['Application_cache_clean_last_run'];
        if (Cron::ShouldRun($interval, $last)) {
            App::clearAllCache(ACTIVE_SITE);
            getSM('block_url_table')->clear();
            getSM('cache_url_table')->clear();
            App::clearAllPublicCache(true, true, true, true);

            db_log_info(sprintf(t('Cache cleaning cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
            $last_run->varValue['Application_cache_clean_last_run'] = time();
        }
        //endregion

        //region Cache Cleaner
        $start = microtime(true);
        $interval = '+2 month';
        $last = @$last_run->varValue['Application_session_clean_last_run'];
        if (Cron::ShouldRun($interval, $last)) {
            $db = App::getDbAdapter();
            $q = "DELETE FROM `tbl_session` WHERE `modified` < " . strtotime('-2 month') . ';';
            $db->query($q)->execute();

            db_log_info(sprintf(t('Session cleaning cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
            $last_run->varValue['Application_session_clean_last_run'] = time();
        }
        //endregion
    }

    public function onLoadBlockConfigs(Event $e)
    {
        /* @var $form NewBlock */
        $form = $e->getParam('form');
        $type = $e->getParam('type');
        $dataFieldset = $form->get('data');

        if ($type == 'marquee_block') {
            $blockInfo = Block::getBlockInfo($type);
            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel('Marquee block settings');
            $dataFieldset->add($fiedlset);

            $fiedlset->add(array(
                'name' => 'marquee-text',
                'type' => 'Zend\Form\Element\Textarea',
                'options' => array(
                    'label' => 'Mental text',
                    'description' => ''
                ),
                'attributes' => array(),
            ));

            $fiedlset->add(array(
                'name' => 'type',
                'type' => 'Zend\Form\Element\Hidden',
                'attributes' => array(
                    'value' => 'marquee',
                )
            ));
        }

        if ($type == 'custom_block') {
            $form->extraScripts[] = "/lib/ckeditor/ckeditor.js";
            $form->extraInlineScripts[] = "/js/block/custom-block.js";
            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel('Custom block settings');
            $dataFieldset->add($fiedlset);

            $fiedlset->add(array(
                'name' => 'customText',
                'type' => 'Zend\Form\Element\Textarea',
                'options' => array(
                    'label' => 'Custom text',
                    'description' => ''
                ),
                'attributes' => array(),
            ));

//            $fiedlset->add(array(
//                'name' => 'btnExit',
//                'type' => 'Zend\Form\Element\Select',
//                'options' => array(
//                    'label' => 'exit button have ?',
//                    'value_options' => array(
//                        '0' => 'No',
//                        '1' => 'Yes',
//                    ),
//                ),
//            ));


            $fiedlset->add(array(
                'name' => 'type',
                'type' => 'Zend\Form\Element\Hidden',
                'attributes' => array(
                    'value' => 'custom_block',
                )
            ));

            $fiedlset->add(array(
                'name' => 'lang',
                'type' => 'Zend\Form\Element\Hidden',
                'attributes' => array(
                    'value' => SYSTEM_LANG,
                )
            ));
        }

        if ($type == 'search_block') {
            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel('Search block settings');
            $dataFieldset->add($fiedlset);

            $fiedlset->add(array(
                'name' => 'open_position_vertical',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Result Vertical Position',
                    'description' => 'where should the result popup be opened?',
                    'value_options' => array(
                        'bottom' => 'Bottom',
                        'top' => 'Top',
                    )
                ),
                'attributes' => array(
                    'class' => 'select2',
                ),
            ));

            $fiedlset->add(array(
                'name' => 'open_position_horizontal',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Result Horizontal Position',
                    'description' => 'where should the result popup be opened?',
                    'value_options' => array(
                        'default' => 'Default',
                        'right' => 'Right',
                        'center' => 'Center',
                        'left' => 'Left',
                    )
                ),
                'attributes' => array(
                    'class' => 'select2',
                ),
            ));
        }
    }

    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $form->menuTypes['frontPage'] = array(
            'label' => 'Front Page',
            'note' => "A link to the site's front page.",
            'params' => array(array('route' => 'app/front-page')),
        );

        $form->menuTypes['introPage'] = array(
            'label' => 'Intro Page',
            'note' => "A link to the site's intro page.",
            'params' => array(array('route' => 'intro')),
        );
    }

    /**
     * @return DbBackupTable
     */
    private function getDbBackupTable()
    {
        return getSM('db_backup_table');
    }

    /**
     * @return Db
     */
    private function getDbBackupApi()
    {
        return getSM('db_backup_api');
    }
} 