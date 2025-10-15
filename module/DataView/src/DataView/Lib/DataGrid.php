<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/12/13
 * Time: 1:28 PM
 *
 * Note when Using Delete and Mass Delete : Your Action should take the id to delete from post witch would be an integer
 *  or an array of integers
 */
namespace DataView\Lib;

use DataView\Lib\Column;
use System\DB\BaseTableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class DataGrid extends SharedBase
{
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';
    /**
     * @var \System\DB\BaseTableGateway
     */
    private $_tableGateway;

    /**
     * @var \Zend\Db\Sql\Select
     */
    private $_select = null;
    private $_data = null;
    /**
     * @var array of GridColumns
     */
    private $_columns = array();
    private $_buttons = array();
    private $_selectFilters = array();
    private $_selectFilterHtml;
    private $_toolbarHtml = '';
    private $_rendered = false;
    private $_dataGridIsReload = false;
    private $_hasSelectFilterColumn = false;

    private $_wrapperTemplate = "<div class='grid-wrapper'>%s</div>";
    private $_template = "<div class='grid-container %s'><table %s>%s</table></div>";
    private $_headerTemplate = "<thead>%s</thead>";
    private $_bodyTemplate = "<tbody>%s</tbody>";
    private $_rowTemplate = "<tr class='%s'>%s</tr>";
    private $_emptyRowTemplate = "<tr><td colspan='%s'>%s</td></tr>";
    private $_footerTemplate = "<tfoot><tr><td colspan='%s'>%s</td></tr></tfoot>";
    private $_selectFilterColumnTemplate = "<div class='grid-filter-column'><h3>%s</h3>%s</div>";

    /**
     * @var callable
     */
    private $_paginationControl;
    private $_basePath;
    /**
     * @var callable
     */
    private $_cycle;


    public $pageNumber = 1;
    public $itemCountPerPage = 10;
    public $route = null;
    public $routeParams = array();
    public $routeOptions;
    /**
     * @var Column
     */
    public $defaultSort;
    /**
     * @var string defaults to self::SORT_ASC
     */
    public $defaultSortDirection = self::SORT_ASC;
    /**
     * @var Column
     */
    private $idCell;
    /**
     * @var bool Dose this grid has filter or order, if yes a button will be added to be able to reset this grid
     */
    public $hasFilter = false;

    public $attributes = array('class' => array('grid'), 'cellspacing' => 0);

    private function renderSelectFilters()
    {
        $filters = '';
        /* @var $col Column */
        foreach ($this->_selectFilters as $col) {
            $filters .= $col->renderSelectFilter();
        }
        $filters = trim($filters);
        if (!empty($filters)) {
            $this->_hasSelectFilterColumn = true;
            $filters = sprintf($this->_selectFilterColumnTemplate, t('Filters'), $filters);
            $this->_selectFilterHtml = $filters;
        }
        return $filters;
    }

    private function renderFiltersRow()
    {
        $filters = '';
        $hasTextFilter = false;
        /* @var $col Column */
        foreach ($this->_columns as $col) {
            if ($col->hasTextFilter) {
                $hasTextFilter = true;
            }
            $filters .= $col->renderFilter();
        }
        if ($hasTextFilter) {
            return sprintf($this->_rowTemplate, 'grid_filter_row', $filters);
        } else
            return '';
    }

    private function renderToolbar()
    {
        if ($this->hasFilter) {
            $this->addButton('Remove Filters',
                'Click this button to reset all filters and sorting to default state.',
                false, false, array('ajax_page_load', 'btn-default'), false, '', array(), array(), array(), 'glyphicon glyphicon-minus-sign text-danger');
        }

        $buttons = '';
        /* @var $button GridButton */
        foreach ($this->_buttons as $key => $button) {
            $buttons .= $button->render($key);
        }

        $this->_toolbarHtml = $buttons;
    }

    private function renderHeader()
    {
        $header = '';
        /* @var $col Column */
        foreach ($this->_columns as $col) {
            $header .= $col->renderHeader();
        }
        return sprintf($this->_headerTemplate, $header);
    }

    /**
     * @param Paginator $data
     * @return string
     */
    private function renderRows($data)
    {
        $body = '';
//        $pk = $this->_tableGateway->getPrimaryKey();
//        if (is_null($pk))
//            throw new \Exception("TableGateways primary key is required and cannot be $pk");

        //rendering each row
        foreach ($data as $data_row) {
//            $id = $data_row->{$pk};
            $row = '';
            //setting dataRow for each column before rendering so each cells data can be available to other cells
            /* @var $col Column */
            foreach ($this->_columns as $col) {
                $col->dataRow = $data_row;
            }
            //rendering each column
            /* @var $col Column */
            foreach ($this->_columns as $col) {
                $row .= $col->render();
            }
            $cycle = $this->_cycle;
            $row = sprintf($this->_rowTemplate, $cycle(array("row-odd", "row-even"))->next(), $row);
            $body .= $row;
        }
        return $body;
    }

    /**
     * @param Paginator $data
     * @return string
     */
    private function renderBody($data)
    {
        $filterRow = $this->renderFiltersRow();
        $body = '';
        if ($data->count()) {
            $body = $this->renderRows($data);
            $body = sprintf($this->_bodyTemplate, $body);
        } else {
            $body = sprintf($this->_emptyRowTemplate, count($this->_columns), $this->_generalHelper->noOutPut());
        }
        return $filterRow . $body;
    }

    /**
     * @param Paginator $data
     * @return string
     */
    private function renderFooter($data)
    {
        $footer = '';
        if ($data->count() > 1) {
            $p = $this->_paginationControl;
            $footer = $p($data,
                'Sliding',
                'application/pagination/search.phtml',
                array(
                    'ajax_page_load' => true,
                    'route' => $this->route,
                    'route_params' => $this->routeParams,
                    'route_options' => $this->routeOptions
                )
            );
            $footer = sprintf($this->_footerTemplate, count($this->_columns), $footer);
        }
        return $footer;
    }

    private function renderDialog()
    {
        $_dialogTemplate = "<div id='grid_dialog'><div id='grid_dialog_content'>" . t('Do you confirm deleting this item ?') . "</div></div>";
        $texts = array(
            'yes' => t('Yes'),
            'no' => t('No'),
            'template' => $_dialogTemplate,
            'title' => t('Confirm Deleting Item(s)'),
            'close_text' => t('Close This Window'),
            'no_item_selected' => t('At least 1 item should be selected for Delete !'),
            'confirm' => t('Do you confirm deleting this item ?'),
        );
        $script = "var GridDialog = " . json_encode($texts) . ';';

        if (!$this->_dataGridIsReload)
            $this->_headScript->appendScript($script);
    }

    /**
     * @return Paginator
     */
    private function getData()
    {
//        $old_resultset = $this->_tableGateway
        /* @var $sql \Zend\Db\Sql\Sql */
        $sql = $this->_tableGateway->getSql();
        $select = $this->getSelect();

        $where = array();
        //select filters
        /* @var $col Column */
        foreach ($this->_selectFilters as $col) {
            if ($col->hasSelectFilter) {
                $table = $col->getTableName();
                $name = $col->getName();
                $value = $col->getFilterValue();
                $operator = $col->filterOperator;
                if (has_value($value)) {
                    $this->hasFilter = true;
                    $where[$table . '.' . $name . " " . $operator . " ?"] = $value;
                }
            }
        }
        //text filters
        /* @var $col Column */
        foreach ($this->_columns as $col) {
            if ($col->hasTextFilter) {
                $table = $col->getTableName();
                $name = $col->getName();
                $value = $col->getFilterValue();
                if (has_value($value)) {
                    $this->hasFilter = true;
                    $where[$table . '.' . $name . " LIKE ?"] = "%" . $value . "%";
                }
            }
        }
        $select->where($where);
        //order
        $orderBy = $this->_paramsPlugin->fromQuery('orderBy', false);
        if ($orderBy) {
            $this->hasFilter = true;
            $orderBy = $orderBy ? $orderBy : $this->defaultSort;
            $dir = $this->_paramsPlugin->fromQuery('orderDir', $this->defaultSortDirection);
            $select->order($orderBy . ' ' . $dir);
        } elseif ($this->defaultSort) {
            $select->order($this->defaultSort->getName() . ' ' . $this->defaultSortDirection);
        }

        $adapter = new DbSelect($select, $sql);
        $pagination = new Paginator($adapter);
        $pagination->setCurrentPageNumber($this->pageNumber);
        $pagination->setItemCountPerPage($this->itemCountPerPage);
        return $pagination;
    }

    /**
     * @param string $tableGateway The name of the requires tableGateway service
     */
    public function __construct($tableGateway)
    {
        parent::__construct();
        if (is_scalar($tableGateway))
            $this->_tableGateway = getSM($tableGateway);
        elseif ($tableGateway instanceof BaseTableGateway)
            $this->_tableGateway = $tableGateway;
        else {
            throw new \Exception('Invalid tableGateway is provided.' . var_export($tableGateway) . ' need to be either a service or an instance of BaseTableGateway');
        }
        $this->_paginationControl = $this->_viewHelperManager->get('paginationControl');
        $this->pageNumber = $this->_paramsPlugin->fromQuery('page', 1);
        $this->_cycle = $this->_viewHelperManager->get('cycle');
        /** @var $basePath callable */
        $basePath = $this->_viewHelperManager->get('basePath');
        $this->_basePath = $basePath();

        $this->_dataGridIsReload = $this->_paramsPlugin->fromQuery('_dataGridIsReload', 0);
        if (!$this->_dataGridIsReload)
            $this->_headScript->appendFile($basePath() . '/js/data-grid-view.js');

        $this->routeOptions = array('query' => $this->_paramsPlugin->fromQuery());
        $this->routeOptions['query']['_dataGridIsReload'] = 1;

        $this->itemCountPerPage = $this->_paramsPlugin->fromQuery('per_page', $this->itemCountPerPage);
    }

    public function prependColumn(Column $column)
    {
        $column->setDataGrid($this);
        array_unshift($this->_columns, $column);
    }

    /**
     * @param Column $column
     */
    public function addColumn(Column $column)
    {
        $column->setDataGrid($this);
        $this->_columns[] = $column;
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

    /**
     * Render The Data grid
     * @return string
     */
    public function render()
    {
        $last = count($this->_columns) - 1;

        $this->_columns[0]->isFirstColumn = true;
        $this->_columns[$last]->isLastColumn = true;

        $header = $this->renderHeader();
        $data = $this->getData();
        $body = $this->renderBody($data);
        $footer = $this->renderFooter($data);
        $this->renderDialog();
        $this->renderFiltersRow();
        $this->renderToolbar();
        $this->_rendered = true;
        $filters = $this->renderSelectFilters();
        $class = $this->_hasSelectFilterColumn ? 'grid-has-filter-column' : '';
        $table = sprintf($this->_template, $class, $this->renderAttr($this->attributes), $header . $body . $footer);

        $messages =
            '<strong>*</strong> ' . t('to use columns filters type some characters and then press Enter.') . "<br/>" .
            '<strong>**</strong> ' . t('when using column filters _(single underline) would match any character.');

        $messages = sprintf("<div class='footer-note alert alert-success %s'>%s</div>", $class, $messages);

        return sprintf($this->_wrapperTemplate, $filters . $table . $messages);
    }

    /**
     * @return \System\DB\BaseTableGateway
     */
    public function getTableGateway()
    {
        return $this->_tableGateway;
    }

    /**
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect()
    {
        if (is_null($this->_select))
            $this->_select = $this->_tableGateway->getSql()->select();

        return $this->_select;
    }

    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @param string $label
     * @param string $title
     * @param string $routePostFix
     * @param bool $fullRoute
     * @param bool $iconOnly
     * @throws \Exception
     */
    public function addNewButton($label = 'New Item', $title = '', $routePostFix = '/new', $fullRoute = false,
                                 $iconOnly = false, $routeParams = array(), $routeOptions = array(), $attributes = array())
    {
        $this->addButton($label, $title, $routePostFix, $fullRoute, array('btn-default', 'ajax_page_load'),
            $iconOnly, 'toolbar_new_item', $routeParams, $routeOptions, $attributes, 'glyphicon glyphicon-plus-sign text-success');
    }

    /**
     * When using this button the delete action should not take any route parameters, instead we send id throw post
     * @param string $label
     * @param string $title
     * @param string $routePostFix Adds this value to the end of $grid->route to make the delete route
     * @param bool $fullRoute $routePostFix will be ignored and this value will be used for delete route
     * @param bool $iconOnly
     * @throws \Exception
     */
    public function addDeleteSelectedButton($label = 'Delete', $title = 'Delete Selected Items', $routePostFix = '/delete',
                                            $fullRoute = false, $iconOnly = false, $routeParams = array(),
                                            $routeOptions = array(), $attributes = array())
    {
        $this->addButton($label, $title, $routePostFix, $fullRoute, array('btn-default'),
            $iconOnly, 'toolbar_delete_selected', $routeParams, $routeOptions, $attributes, 'glyphicon glyphicon-remove text-danger');
        if (!isset($this->_columns[0]) || (isset($this->_columns[0]) && !$this->_columns[0] instanceof RowSelector)) {
            $this->prependColumn(new RowSelector());
        }
    }

    /**
     * Add a button to the toolbar
     * @param string $label
     * @param string $title
     * @param bool $routePostFix
     * @param bool $fullRoute
     * @param null $class
     * @param bool $iconOnly
     * @param string $id
     * @param array $routeParams
     * @param array $routeOptions
     * @throws \Exception
     */
    public function addButton($label = 'Button', $title = '', $routePostFix = false, $fullRoute = false, $class = null,
                              $iconOnly = false, $id = '', $routeParams = array(), $routeOptions = array(),
                              $attributes = array(), $icon = null)
    {
        if ($fullRoute)
            $route = $fullRoute;
        else {
            if (!$this->route)
                throw new \Exception(t('You need to set $grid->route before adding any buttons with $fullRoute not set'));
            $route = $this->route . $routePostFix;
        }

        $routeParams = array_merge($routeParams, $this->routeParams);
//        $routeOptions = array_merge($routeOptions,$this->routeOptions);
        $button = new GridButton($label, $title, $id, $iconOnly, $route, $routeParams, $routeOptions, $attributes);
        $button->icon = $icon;
        if ($class) {
            if (is_array($class))
                $button->class = array_merge($button->class, $class);
            else
                $button->class[] = $class;
        }

        $this->_buttons[] = $button;
    }

    public function getButtons()
    {
        if (!$this->_rendered)
            throw new \Exception(t('Grid need to be rendered first !'));
        return $this->_toolbarHtml;
    }

    /**
     * @param array $filters an array of Columns
     */
    public function setSelectFilters(array $filters)
    {
        /* @var $col Column */
        foreach ($filters as $col) {
            $col->hasSelectFilter = true;
            $col->setDataGrid($this);
        }
        $this->_selectFilters = $filters;
    }

    public function setIdCell(Column $cell)
    {
        $this->idCell = $cell;
    }

    public function getIdCell()
    {
        if (!$this->idCell)
            throw new \Exception('idCell needs to be set for the data grid');
        return $this->idCell;
    }
}