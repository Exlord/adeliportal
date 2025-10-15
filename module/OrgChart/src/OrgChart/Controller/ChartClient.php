<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace OrgChart\Controller;

use DataView\Lib\DataGrid;
use System\Controller\BaseAbstractActionController;
use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;

class ChartClient extends BaseAbstractActionController
{
    public function indexAction()
    {
        $chartId = $this->params()->fromRoute('chartId', 0);

        if ($chartId) {
            $viewTypeNode = 1;
            $fieldName = array();
            $selectField = array();
            $fieldId = array();
            $config = array();
            $chartConfig = getSM('org_chart_table')->getById($chartId);
            $configChart = getConfig('OrgChart')->varValue;
            if (isset($configChart['viewTypeNode']))
                $viewTypeNode = $configChart['viewTypeNode'];
            if (isset($chartConfig->config))
                $config = unserialize($chartConfig->config);
            if (is_array($config)) {
                foreach ($config as $key => $row)
                    if (isset($row['view']) && $row['view'])
                        $fieldId[] = $key;
                if (!empty($fieldId))
                    $selectField = getSM('fields_table')->getById($fieldId)->toArray();
                if ($selectField) {
                    foreach ($selectField as $row)
                        $fieldName[] = $row['fieldMachineName'];
                }
            }
            /* @var $fields_api \Fields\API\Fields */
            $fields_api = $this->getFieldsApi();
            $fields_table = $this->getFieldsApi()->init('user_profile');
            $data = getSM('chart_node_table')->getTreeNode($chartId, $fieldName, $fields_table, $fields_api, $selectField);
            $this->viewModel->setTemplate('org-chart/chart-client/index');
            $this->viewModel->setVariables(array(
                'data' => $data,
                'fieldName' => $fieldName,
                'viewTypeNode' => $viewTypeNode,
            ));
            return $this->viewModel;
        }
    }
}
