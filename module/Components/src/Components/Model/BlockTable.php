<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/5/12
 * Time: 2:16 PM
 */
namespace Components\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class BlockTable extends BaseTableGateway
{
    protected $table = 'tbl_blocks';
    protected $model = 'Components\Model\Block';
    protected $caches = null;
    private $_blocks = null;

    public function getBlocks($position = null, $ids = null)
    {
        if (is_null($this->_blocks)) {
            $where = array('enabled' => 1);
            if ($ids && count($ids))
                $where['id'] = $ids;

            $select = $this->getSql()->select();
            $select->where($where)
                ->order(array('order ASC'));

            if (getSM()->has('domain_api')) {
                $domainApi = getSM('domain_api');
                $domainCount = getSM('domain_table')->getCount();
                if ($domainCount > 1)
                    $domainApi->filter($select, 'BLOCK', $this->table . '.id');
            }
            $blocks = $this->selectWith($select);

            $this->_blocks = array();
            foreach ($blocks as $row) {
                $this->_blocks[$row->position][$row->id] = $row;
            }
        }
        if ($position) {
            if (isset($this->_blocks[$position]))
                return $this->_blocks[$position];
        } else
            return $this->_blocks;

        return null;
    }

    public function getBlockByType($type, $locked = 0, $enabled = 1)
    {
        return $this->getAll(array('locked' => $locked, 'type' => $type, 'enabled' => $enabled));
    }

    public function save($model)
    {
        $id = parent::save($model);
        if (getSM()->has('domain_content_table')) {
            $domainContentTable = getSM('domain_content_table');
            $domainContentTable->removeByEntity($model->id, 'BLOCK');
            if (isset($model->domains)) {
                if (is_array($model->domains) && count($model->domains)) {
                    $domainContentTable->add($model->domains, $model->id, 'BLOCK');
                }
            }
        }

        return $id;
    }

    public function get($id, $columns = false)
    {
        $block = parent::get($id, $columns);
        if ($block) {
            if (getSM()->has('domain_content_table')) {
                $block->domains = getSM('domain_content_table')->getDomains($block->id, 'BLOCK');
            }
        }
        return $block;
    }


}
