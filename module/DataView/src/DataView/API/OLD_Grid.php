<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace DataView\API;

use System\API\BaseAPI;
use System\DB\BaseTableGateway;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class OLD_Grid extends BaseAPI
{
    /**
     * @var array
     *  KEY : field db name
     *  VALUE : \DataView\API\GridColumn
     */
    private $_columns = array();

    public $Route;
    public $RouteParams;
    public $FilterColumns = array();
    public $OrderColumns = array();

    /**
     * @var \System\DB\BaseTableGateway
     */
    private $_table;
    /**
     * @var ServiceLocatorInterface
     */
    private $sm;
    private $_pageNumber = 1;
    private $_filters;
    private $_orders;
    private $_url_params = array();
    private $_toolBarButtons = '';


    public function __construct($table, ServiceLocatorInterface $sm)
    {
        $this->_table = $sm->get($table);
        $this->sm = $sm;
        $this->_itemCountPerPage = 20;
        $params = $sm->get('ControllerPluginManager')->get('params');
        $this->_pageNumber = $params->fromQuery('page', 1);
    }

    /**
     * @return array
     */
    public function getUrlParams()
    {
        return $this->_url_params;
    }


    /**
     * @param GridColumn $column
     * @return $this
     */
    public function addColumn(GridColumn $column)
    {
        $this->_columns[$column->Name] = $column;
        return $this;
    }

    /**
     * @param $name
     * @param string $header
     * @param string $width
     * @param string $type
     * @return GridColumn
     */
    public function makeColumn($name, $header = '', $width = '', $type = GridColumn::TYPE_TEXT)
    {
        $col = new GridColumn($name);
        $col->Header = $header;
        $col->setWidth($width);
        $col->Type = $type;
        $this->addColumn($col);
        return $col;
    }

    /**
     * @param $column
     * @param $value
     * @return Grid
     */
    public function setFilter($column, $value)
    {
        $this->_filters[$column] = $value;
        return $this;
    }

    /**
     * @param $column
     * @return Grid
     */
    public function setOrder($column)
    {
        $this->_orders[] = $column;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return Grid
     */
    public function setRouteParam($key, $value)
    {
        $this->RouteParams[$key] = $value;
        return $this;
    }

    private function init()
    {
        $params = $this->sm->get('ControllerPluginManager')->get('params');

        foreach ($this->FilterColumns as $key => $filter) {
            if (!is_array($filter)) {
                $filter_val = $params->fromQuery('filter_' . $filter, '');
                if (has_value($filter_val)) {
                    $this->_filters[$filter] = $filter_val;
                    $this->_url_params['filter_' . $filter] = $filter_val;
                }
            }
        }

        $per_page = $params->fromQuery('per_page', false);
        if (has_value($per_page)) {
            $this->_url_params['per_page'] = $per_page;
            $this->_itemCountPerPage = (int)$per_page;
        }

        $order_val = $params->fromQuery('order', '');
        if ($order_val) {
            if (strpos($order_val, 'asc') > -1) {
                $colName = str_replace('asc_', '', $order_val);
                $this->_orders[] = $colName . ' ASC';
            } elseif (strpos($order_val, 'desc') > -1) {
                $colName = str_replace('desc_', '', $order_val);
                $this->_orders[] = $colName . ' DESC';
            }
            $this->_url_params['order'] = $order_val;
        }

//        foreach ($params->fromQuery() as $key => $value) {
//            $this->_url_params[$key] = $value;
//        }
    }

    /**
     * @return string rendered grid html
     */
    public function render()
    {
        $this->init();
        $translate = $this->sm->get('viewhelpermanager')->get('translate');
        $general = $this->sm->get('viewhelpermanager')->get('general');
        $headScript = $this->sm->get('viewhelpermanager')->get('headScript');
        $basePath = $this->sm->get('viewhelpermanager')->get('basePath');
        $paginationControl = $this->sm->get('viewhelpermanager')->get('paginationControl');
        $router = $this->sm->get('router');
        $url = $this->sm->get('ControllerPluginManager')->get('url');

        /*************************************** Column Headers **********************************************/
        $header = '';
        $header_temp = "<th width='%s'>%s</th>";
        /* @var $col GridColumn */
        foreach ($this->_columns as $colName => $col) {
            switch ($col->Type) {
                case GridColumn::TYPE_ORDER:
                    $header_content = $translate($col->Header);
                    $update_order = "<button title='%s' id='update_order' data-url='%s' class='grid_button save_button'>%s</button>";
                    $update_order = sprintf($update_order, $translate('Save Order'), $url->fromRoute($col->DataUrl), $translate('Save'));
                    $header_content .= $update_order;
                    break;
                default:
                    $header_content = $translate($col->Header);
                    break;
            }
            $sort = '';
            if ($col->Sortable) {
                $sort_title_desc = $translate('Sort Descending');
                $sort_title_asc = $translate('Sort Ascending');
                $sort_params = $this->_url_params;
                $sort_params['order'] = 'asc_' . $colName;
                //TODO query string
                $url_sort = $url->fromRoute($this->Route, array(), array('query' => $sort_params));
                $sort = "<a title='$sort_title_asc' href='$url_sort' class='sort-button sort-button-up'><span class='ui-icon ui-icon-triangle-1-n'>▲</span></a>";

                $sort_params['order'] = 'desc_' . $colName;
                //TODO query string
                $url_sort = $url->fromRoute($this->Route, array(), array('query' => $sort_params));
                $sort .= "<a title='$sort_title_desc' href='$url_sort' class='sort-button sort-button-down'><span class='ui-icon ui-icon-triangle-1-s'>▼</span></a>";

                $sort = "<span class='grid-sort-buttons'>$sort</span>";
            }
            $header .= sprintf($header_temp, $col->getWidth(), $sort . $header_content);


        }
        /*************************************** Column Filters **********************************************/
        $filters = '';
        if (count($this->FilterColumns)) {
            /* @var $col GridColumn */
            foreach ($this->_columns as $colName => $col) {
                if (in_array($colName, $this->FilterColumns)) {
                    $filters_temp = "<td><input type='text' name='%s' value='%s'></td>";
                    $filters .= sprintf($filters_temp, 'filter_' . $colName, @$this->_filters[$colName]);
                } else {
                    $filters .= "<td></td>";
                }
            }
            $filters = "<tr class='filters_row'>" . $filters . "</tr>";
        }

        /*************************************** ROWS **********************************************/
        $rows = '';
        /* @var $data \Zend\Paginator\Paginator */
        $data = $this->getData();
        if ($data->count()) {
            foreach ($data as $data_row) {
                $pk = $this->_table->getPrimaryKey();
                $id = $data_row->{$pk};

                $row = '<tr>';
                /* @var $col GridColumn */
                foreach ($this->_columns as $colName => $col) {
                    switch ($col->Type) {
                        case GridColumn::TYPE_TEXT:
                            $row .= "<td>" . $data_row->{$colName} . "</td>";
                            break;
                        case GridColumn::TYPE_ORDER:
                            $row_content = "<input class='spinner tiny_spinner updateAble_order' type='text'
                                   value='%s' name='%s' data-id='%s' data-min='-999' data-max='999' data-step='1' size='5'>";
                            $row .= "<td>" . sprintf($row_content, $data_row->{$colName}, $colName, $id) . "</td>";
                            break;
                        case GridColumn::TYPE_STATUS:
                            $row_content = "<td title='%s' class='%s'>
                                        <input class='updateAble_checkbox' type='checkbox'
                                            name='%s' %s data-url='%s' data-id='%s'></td>";
                            $row .= sprintf($row_content,
                                $translate('Click here to Disable/Enable this item'),
                                $data_row->{$colName} ? 'active' : 'inactive',
                                $colName,
                                $data_row->{$colName} ? "checked='checked'" : '',
                                $url->fromRoute($col->DataUrl),
                                $id);
                            break;
                        case GridColumn::TYPE_BUTTON:
                            $row_content = "<td align='center'>
                                                <a class='grid_button %s' title='%s' href='%s'>&nbsp;</a>
                                            </td>";
                            $class = $col->Class;
                            if (is_array($class))
                                $class = implode(' ', $class);

                            $href = '';
                            $params = array();
                            if (!is_closure($col->DataUrl)) {
                                $params = array($this->_table->getPrimaryKey() => $id);
                                $href = $url->fromRoute($col->DataUrl, $params);
                            } else {
                                $href = $col->DataUrl;
                                $href = $href($data_row, $col);
                            }

                            $row .= sprintf($row_content, $class, $translate($colName), $href);
                            break;
                        case GridColumn::TYPE_CLOSURE:
                            $col_value = $col->Closure;
                            $row .= $col_value($data_row, $col);
                            break;
                    }
                }
                $row .= "</tr>";
                $rows .= $row;
            }
        } else {
            $rows = "<tr><td colspan='" . count($this->_columns) . "'>" . $general->noOutPut() . "</td></tr>";
        }

        $table = "<table class='grid' cellspacing='0'><thead><tr>%s</tr></thead><tbody>%s</tbody></table>";
        $table = sprintf($table, $header, $filters . $rows);
        if ($data->count() > 1) {
            $table .= "<div class='pager'>";
            $table .= $paginationControl($data,
                'Sliding',
                //TODO query string
                'application/pagination/search.phtml', array('route' => $this->Route, 'route_params' => $this->RouteParams, 'query' => $this->_url_params));
            $table .= "</div>";
        }
        $script = "<script type='text/javascript'>
                    var grid_filters = " . json_encode($this->_url_params) . ";
                    var grid_route = '" . $url->fromRoute($this->Route) . "';
                   </script>";
        $headScript->appendFile($basePath() . '/js/data-grid-view.js');
        return $table . $script;
    }

    private function getData()
    {
        /* @var $sql \Zend\Db\Sql\Sql */
        $sql = $this->_table->getSql();
        $select = $sql->select();

        if ($this->_filters) {
            $where = array();
            foreach ($this->_filters as $key => $filter) {
                $where[$key . " LIKE ?"] = "%" . $filter . "%";
            }
            if (count($where))
                $select->where($where);
        }
        if (count($this->_orders))
            $select->order($this->_orders);

//        var_dump($select->getSqlString());
        $adapter = new DbSelect($select, $sql);
        $pagination = new Paginator($adapter);
        $pagination->setCurrentPageNumber($this->_pageNumber);
        $pagination->setItemCountPerPage($this->_itemCountPerPage);
        return $pagination;
    }

    /**
     * @param $Route
     * @return Grid
     */
    public function setRoute($Route)
    {
        $this->Route = $Route;
        return $this;
    }

    /**
     * @param $FilterColumns
     * @return Grid
     */
    public function setFilterColumns($FilterColumns)
    {
        $this->FilterColumns = $FilterColumns;
        return $this;
    }

    public function getToolBarButtons()
    {
        return $this->_toolBarButtons;
    }


}