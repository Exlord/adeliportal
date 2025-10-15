<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace Analyzer\API;

use Analyzer\Model\Visits;
use Analyzer\Model\VisitsArchiveTable;
use Application\API\App;
use System\API\BaseAPI;

class Analyzer extends BaseAPI
{
    const LOADING_DATA = 'Analyzer.Data.Loading';
    /**
     * @var \Analyzer\Model\VisitsTable
     */
    private $visitsTable = null;

    /**
     * @var \Zend\Session\Container\PhpReferenceCompatibility
     */
    private $session = null;

    /**
     * @var VisitsArchiveTable
     */
    private $archiveTable = null;

    private $config = null;

    /**
     * @return \Analyzer\Model\VisitsTable
     */
    private function getVisitsTable()
    {
        if (is_null($this->visitsTable))
            $this->visitsTable = getSM('visits_table');

        return $this->visitsTable;
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

    private function getConfig()
    {
        if (is_null($this->config))
            $this->config = getConfig('analyzer')->varValue;

        return $this->config;
    }

    /**
     * @return \Zend\Session\Container\PhpReferenceCompatibility
     */
    private function getSession()
    {
        if (is_null($this->session)) {
            $this->session = App::getSession('AnalyzerVisits');
            $this->session->setExpirationSeconds(900);
        }
        return $this->session;
    }

    public function addVisitor()
    {
        $config = $this->getConfig();

        if (!isset($config['count_visits']))
            return false;

        if ($config['count_visits'] != '1')
            return false;

        if (isset($config['only_unique_visits']))
            if ($config['only_unique_visits'] == '1')
                if ($this->getSession()->visited) //this user has not visited any page in the last 5 minutes
                    return false;

        $visitor = new Visits();
        $visitor->date = time();
        $visitor->type = $this->getSession()->visited ? 0 : 1;
        $this->getVisitsTable()->save($visitor);

        $this->getSession()->visited = true;
    }

    public function archive()
    {
        $today = strtotime("00:00:00");
        $result = $this->getVisitsTable()->getAll(array('date < ?' => $today), array('date DESC'));
        $data = array();
        $rowsToDelete = array();
        if ($result && $result->count()) {
            foreach ($result as $row) {
                $date = $row->date;
                $y = date('Y', $date);
                $m = date('m', $date);
                $d = date('d', $date);
                $h = date('H', $date);
                $date = mktime($h, 0, 0, $m, $d, $y);
                if (!isset($data[$date]))
                    $data[$date] = array('0' => 0, '1' => 0);

                $data[$date][$row->type] += 1;
                $rowsToDelete[] = $row->id;
            }
        }

        if (count($data)) {
            foreach ($data as $date => $count) {
                $this->getArchiveTable()->archive($date, $count['0'], $count['1']);
            }
        }
        if (count($rowsToDelete)) {
            $this->getVisitsTable()->remove($rowsToDelete);
        }
    }

    public function loadStatisticsData($json, $month, $dateLow, $dateHigh)
    {
        $this->getEventManager()->trigger(self::LOADING_DATA, $this, array('json' => $json, 'month' => $month, 'dateLow' => $dateLow, 'dateHigh' => $dateHigh));
    }
}