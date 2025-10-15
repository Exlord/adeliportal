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

class Grid
{
    private $_columns = array();
    private $template = "<table class='grid' cellspacing='0'><thead><tr>%s</tr></thead><tbody>%s</tbody></table>%s";
    private $route;
    protected $route_params = array();
    protected $route_options = array();
    private $routeParams;
    /**
     * @var \System\DB\BaseTableGateway
     */
    private $table;
    private $itemCountPerPage = 20;
    private $pageNumber = 1;
    /**
     * @var \Zend\Mvc\Controller\Plugin\Params
     */
    private $paramsPlugin;
    private $generalHelper;
    private $paginationControl;
    private $data;
    private $select;
    private $order;
    private $where;

    /**
     * @param $table string|array
     */
    public function __construct($table, $data = null)
    {
        $this->table = getSM()->get($table);
        $this->data = $data;
        $this->paramsPlugin = getSM()->get('ControllerPluginManager')->get('params');
        $this->generalHelper = getSM()->get('viewhelpermanager')->get('general');
        $this->paginationControl = getSM()->get('viewhelpermanager')->get('paginationControl');
        $this->pageNumber = $this->paramsPlugin->fromQuery('page', 1);
    }

    /**
     * @param Column $column
     */
    public function addColumn(Column $column)
    {
        $column->setParent($this);
        $this->_columns[$column->getName()] = $column;
    }

    /**
     * @param array $columns
     */
    public function addColumns(array $columns)
    {
        /* @var $col Column */
        foreach ($columns as $col) {
            $this->addColumn($col);
        }
    }

    public function render()
    {
        $header = '';
        $filter_row = '';
        $body = '';
        $footer = '';

        /* @var $data \Zend\Paginator\Paginator */
        $data = $this->getData();
        /* @var $col Column */
        foreach ($this->_columns as $col) {
            $header .= $col->getHeader();
        }

        if (is_array($data))
            $count = count($data);
        else
            $count = $data->count();

        if ($count) {
            foreach ($data as $data_row) {
                $pk = $this->table->getPrimaryKey();
                $id = $data_row->{$pk};

                $row = '<tr>';
                /* @var $col Column */
                foreach ($this->_columns as $col) {
                    $row .= $col->render($data_row);
                }
                $row .= "</tr>";
                $body .= $row;
            }
        } else {
            $body = "<tr><td colspan='" . count($this->_columns) . "'>" . $this->generalHelper->noOutPut() . "</td></tr>";
        }

        if ($count > 1 && !is_array($data)) {
            $p = $this->paginationControl;
            $footer = "<div class='pager'>";
            $footer .= $p($data,
                'Sliding',
                //TODO query string
                'application/pagination/search.phtml', array('route' => $this->route, 'route_params' => $this->routeParams, 'route_options' => $this->route_options));
            $footer .= "</div>";
        }

        $table = sprintf($this->template, $header, $filter_row . $body, $footer);
        return $table;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    private function getData()
    {
        //data is on array of data
        if (is_array($this->data))
            return $this->data;

        /* @var $sql \Zend\Db\Sql\Sql */
        $sql = $this->table->getSql();
        $select = $this->getSelect();

//        if ($this->_filters) {
//            $where = array();
//            foreach ($this->_filters as $key => $filter) {
//                $where[$key . " LIKE ?"] = "%" . $filter . "%";
//            }
//            if (count($where))
//                $select->where($where);
//        }
//        if (count($this->_orders))
//            $select->order($this->_orders);

//        var_dump($select->getSqlString());
        $adapter = new DbSelect($select, $sql);
        $pagination = new Paginator($adapter);
        $pagination->setCurrentPageNumber($this->pageNumber);
        $pagination->setItemCountPerPage($this->itemCountPerPage);
        return $pagination;
    }

    /**
     * @return \System\DB\BaseTableGateway
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect()
    {
        if ($this->select == null) {
            $this->select = $this->table->getSql()->select();
        }
        return $this->select;
    }

    /**
     * @param $key
     * @param $value
     * @return Grid
     */
    public function setRouteParam($key, $value)
    {
        $this->route_params[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return Grid
     */
    public function setRouteOption($key, $value)
    {
        $this->route_options[$key] = $value;
        return $this;
    }

    public function getRouteParams()
    {
        return $this->route_params;
    }

    public function getRouteOptions()
    {
        return $this->route_options;
    }
}