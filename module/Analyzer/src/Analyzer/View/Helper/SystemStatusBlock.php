<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/12/13
 * Time: 10:15 AM
 */

namespace Analyzer\View\Helper;


use Analyzer\Model\VisitsArchiveTable;
use Analyzer\Model\VisitsTable;
use System\View\Helper\BaseHelper;

class SystemStatusBlock extends BaseHelper
{
    private $today = null;
    private $archiveTable = null;
    private $config = null;
    private $unique = false;
    private $multiplier = 1;

    private function getConfig()
    {
        if (is_null($this->config))
            $this->config = getConfig('analyzer')->varValue;

        return $this->config;
    }

    /**
     * @return VisitsTable
     */
    private function getVisitsTable(){
        return getSM('visits_table');
    }

    private function getTodayCount()
    {
        if (is_null($this->today)) {
            $this->today = $this->getVisitsTable()->getCount($this->unique);
        }

        return $this->today;
    }

    /**
     * @return VisitsArchiveTable
     */
    private function getArchiveTable()
    {
        if (is_null($this->archiveTable))
            $this->archiveTable = getSM('visits_archive_table');

        return $this->archiveTable;
    }

    public function __invoke($block)
    {
        $config = $this->getConfig();
        if (isset($config['display_only_unique_visits']))
            if ($config['display_only_unique_visits'] == '1')
                $this->unique = true;

        if (isset($config['multiplier']))
            $this->multiplier = (int)$config['multiplier'];

        if ($this->multiplier > 10)
            $this->multiplier = 10;

        if ($this->multiplier < 1)
            $this->multiplier = 1;

        $today = strtotime("00:00:00");
        $now = time();
        $month = date('m');
        $year = date('Y');

        $cacheKey = "all_visits_counts";
        if (!$cache = getCacheItem($cacheKey))
            $cache = array();
        if (!isset($cache[$block->id]))
            $cache[$block->id] = array();

        $block->data['class'] .= ' visitors-block';
        $block->blockId = 'visitors-block-' . $block->id;

        $data = array();

        $report_periods = $block->data['system_status_block']['report_periods'];
        foreach ($report_periods as &$report) {
            $type = $report['type'];
            $label = $report['label'];
            $count = (int)$report['count'];

            $visitCount = 0;
            $class = '';
            $dateLow = null;
            $dateHigh = null;
            switch ($type) {
                case 'day':
                    if (!in_array($label, array('date1', 'date2', 'name', 'name_date1'))) {
                        $label = 'name_date1';
                    }

                    if ($count == 0) {
                        $visitCount = $this->getTodayCount();
                    } else {

                        $dateLow = strtotime('-' . $count . ' days', $today);
                        $dateHigh = strtotime('+1 day', $dateLow);
                        if (isset($cache[$block->id][$dateLow . $dateHigh])) {
                            $_data = $cache[$block->id][$dateLow . $dateHigh];
                            $label = key($_data);
                            $visitCount = current($_data);
                        } else {
                            switch ($label) {
                                case 'date1':
                                    $label = $this->view->dateFormat($dateLow, 0, 0, null, 'm/d');
                                    break;
                                case 'date2':
                                    $label = $this->view->dateFormat($dateLow, 0, 0, null, 'Y/m/d');
                                    break;
                                case 'name':
                                    $label = $this->view->dateFormat($dateLow, 0, 0, null, 'l');
                                    break;
                                case 'name_date1':
                                    $label = $this->view->dateFormat($dateLow, 0, 0, null, 'l m/d');
                                    break;
                            }


                            $visitCount = $this->getArchiveTable()->getCount($dateLow, $dateHigh, $this->unique);
                            $cache[$block->id][$dateLow . $dateHigh][$label] = $visitCount;
                        }
                    }
                    if ($count == 0) {
                        $class = 'today';
                        $label = t('Today');
                    } elseif ($count == 1) {
                        $class = 'yesterday';
                        $label = t('Yesterday');
                    } else
                        $class = $count . '-days';

                    $class = 'days ' . $class;

                    break;
                case 'month':
                    if (!in_array($label, array('date3', 'date2', 'name', 'name_date3'))) {
                        $label = 'name_date3';
                    }

                    $thisMonth = mktime(0, 0, 0, $month, 1, $year);
                    if ($count != 0)
                        $dateLow = strtotime('-' . $count . ' months', $thisMonth);
                    else
                        $dateLow = $thisMonth;

                    if ($count != 0)
                        $dateHigh = strtotime('+1 month', $dateLow);
                    else
                        $dateHigh = $today;

                    if (isset($cache[$block->id][$dateLow . $dateHigh])) {
                        $_data = $cache[$block->id][$dateLow . $dateHigh];
                        $label = key($_data);
                        $visitCount = current($_data);
                    } else {

                        switch ($label) {
                            case 'date2':
                            case 'date3':
                                $label = $this->view->dateFormat($dateLow, 0, 0, null, 'Y/m');
                                break;
                            case 'name':
                                $label = $this->view->dateFormat($dateLow, 0, 0, null, 'F');
                                break;
                            case 'name_date3':
                                $label = $this->view->dateFormat($dateLow, 0, 0, null, 'F Y/m');
                                break;
                        }

                        $visitCount = $this->getArchiveTable()->getCount($dateLow, $dateHigh, $this->unique);
                        $cache[$block->id][$dateLow . $dateHigh][$label] = $visitCount;
                    }
                    if ($count == 0) {
                        $class = 'this-month';
                        $label = t('This Month');
                        $visitCount += $this->getTodayCount();
                    } elseif ($count == 1) {
                        $class = 'last-month';
                        $label = t('Last Month');
                    } else
                        $class = $count . '-months';

                    $class = 'months ' . $class;
                    break;
                case 'year':
                    $thisYear = mktime(0, 0, 0, 1, 1, $year);
                    if ($count != 0)
                        $dateLow = strtotime('-' . $count . ' years', $thisYear);
                    else
                        $dateLow = $thisYear;

                    if ($count != 0)
                        $dateHigh = strtotime('+1 year', $dateLow);
                    else
                        $dateHigh = $today;

                    if (isset($cache[$block->id][$dateLow . $dateHigh])) {
                        $_data = $cache[$block->id][$dateLow . $dateHigh];
                        $label = key($_data);
                        $visitCount = current($_data);
                    } else {
                        $label = $this->view->dateFormat($dateLow, 0, 0, null, 'Y');
                        $visitCount = $this->getArchiveTable()->getCount($dateLow, $dateHigh, $this->unique);
                        $cache[$block->id][$dateLow . $dateHigh][$label] = $visitCount;
                    }

                    if ($count == 0) {
                        $class = 'this-year';
                        $label = t('This Year');
                        $visitCount += $this->getTodayCount();
                    } elseif ($count == 1) {
                        $class = 'last-year';
                        $label = t('Last Year');
                    } else
                        $class = $count . '-years';

                    break;
            }
            $data[$label] = array('class' => $class, 'count' => $visitCount);
        }

        if (isset($block->data['system_status_block']['total']) && $block->data['system_status_block']['total'] == '1') {

            if (isset($cache[$block->id]['total'])) {
                $_data = $cache[$block->id]['total'];
                $label = key($_data);
                $total = current($_data);
            } else {
                $total = $this->getArchiveTable()->getCount(0, 0, $this->unique);
                $label = t('ANALYZER_VISITS_TOTAL');
                $cache[$block->id]['total'][$label] = $total;
            }
            $data[$label] = array('class' => 'total', 'count' => $total + $this->getTodayCount());
        }

        setCacheItem($cacheKey, $cache);
        return $this->view->render('analyzer/helper/visits', array('data' => $data, 'multiplier' => $this->multiplier));
    }
}