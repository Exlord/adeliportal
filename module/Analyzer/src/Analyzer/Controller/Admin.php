<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Analyzer\Controller;

use Analyzer\API\Analyzer;
use Analyzer\Form\Config;
use Localization\API\Date;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;

class Admin extends BaseAbstractActionController
{
    public function configAction()
    {
        /* @var $config Config */
        $config = getConfig('analyzer');
        $form = prepareConfigForm(new Config());
        $form->setData($config->varValue);


        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getConfigTable()->save($config);
                    db_log_info("Analyzer Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Analyzer configs saved successfully');
                }
            }
        }

        $this->viewModel->setVariables(array('form' => $form));
        $this->viewModel->setTemplate('analyzer/admin/config');
        return $this->viewModel;
    }

    public function reportAction()
    {

        /* @var $analyzer Analyzer */
        $analyzer = getSM('analyzer_api');
        $analyzer->archive();

        $series = array();
        $legends = array();
        $data = array();
        $today = strtotime("00:00:00");

//        $i = 0;
//        while ($i++ < 24) {
//            $time = strtotime('+' . $i . ' hour', $today);
////            $legends[] = Date::date('H:i', $time);
//            $data[$time] = array('0' => 0, '1' => 0);
//        }


//        $result = getSM('visits_table')->getAll(array('date >= ?' => $today));
//        $y = $m = $d = null;
//
//        foreach ($result as $row) {
//            $date = $row->date;
//            if (!$y) {
//                $y = date('Y', $date);
//                $m = date('m', $date);
//                $d = date('d', $date);
//            }
//            $h = date('H', $date);
//            $date = mktime($h, 0, 0, $m, $d, $y);
//
//            if (!isset($data[$date]))
//                $data[$date] = array('0' => 0, '1' => 0);
//
//            $data[$date][$row->type] += 1;
//        }
//
//        $unique = array();
//        $repeated = array();
//
//        foreach ($data as $date => $types) {
//            $unique[] = $types['1'];
//            $repeated[] = $types['0'];
//        }

        $this->viewModel->setVariables(array(
            'pointStart' => $today * 1000,
//            'unique' => implode(',', $unique),
//            'repeated' => implode(',', $repeated),
            'dateFunction' => \Locale::getDefault() == 'fa_IR' ? 'JDate' : 'Date'
        ));
        $this->viewModel->setTemplate('analyzer/admin/report');
        return $this->viewModel;
    }

    public function moreDataAction()
    {
        $month = (int)$this->params()->fromQuery('month', 0);

        if ($month == 0)
            $dateHigh = time();
        else
            $dateHigh = strtotime('-' . $month . ' months', time());

        $dateLow = strtotime('-1 months', $dateHigh);

        $json = new \stdClass();

        $data = getSM('visits_archive_table')->getData($dateLow, $dateHigh);
        if ($data) {
            foreach ($data as $row) {
                $json->data[$row->date] = array('unique' => $row->uniqueCount, 'repeated' => $row->count);
            }
        }
        if ($month == 0) {
            $today = strtotime("00:00:00");
            $result = getSM('visits_table')->getAll(array('date >= ?' => $today));
            $y = $m = $d = null;

            foreach ($result as $row) {
                $date = $row->date;
                if (!$y) {
                    $y = date('Y', $date);
                    $m = date('m', $date);
                    $d = date('d', $date);
                }
                $h = date('H', $date);
                $date = mktime($h, 0, 0, $m, $d, $y);

                if (!isset($json->data[$date]))
                    $json->data[$date] = array('unique' => 0, 'repeated' => 0);

                $type = $row->type == '1' ? 'unique' : 'repeated';
                $json->data[$date][$type] += 1;
            }

            $json->series = array('unique' => t('Unique Visitors'), 'repeated' => t('Repeated Visitors'));
        }

        getSM('analyzer_api')->loadStatisticsData($json, $month,$dateLow, $dateHigh);

        $json = (array)$json;
        if (count($json)) {
            return new JsonModel($json);
        } else return new JsonModel(array('msg' => t('No more data is available for display.')));
    }
}
