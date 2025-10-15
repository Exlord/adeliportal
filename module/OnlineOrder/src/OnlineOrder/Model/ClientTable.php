<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/17/12
 * Time: 11:18 AM
 * To change this template use File | Settings | File Templates.
 */
namespace OnlineOrder\Model;

use Zend\Db;
use Zend\Db\TableGateway;
use System;

class ClientTable extends \System\DB\BaseTableGateway
{
    protected $table = 'tbl_clients';
    protected $model = 'OnlineOrder\Model\Client';
    protected $caches = null;

    public function save2(Client $client)
    {
        $client->modules = serialize($client->modules);
        return parent::save($client);
    }

    public function get($id)
    {
        $client = parent::get($id);
        $client->modules = unserialize($client->modules);
        return $client;
    }

    public function getSearchDomains($domains)
    {
        $select = $this->getSql()->select();
        $select->columns(array('id' => new \Zend\Db\Sql\Expression('COUNT(*)')))
            ->where(array(
                    'clientDomain' => $domains
                )
            );
        $result = $this->selectWith($select)->current();
        $count = $result->id;
        if ($count)
            return false;
        else
            return true;

    }

    public function updateDomains($beforeDomains,$newDomains)
    {
        foreach($beforeDomains as $key=>$val)
        {
            $select = $this->getAll(array('clientDomain'=>$val))->current();
            $this->update(array('clientDomain'=>$newDomains[$key]),array('id'=>$select->id));
        }
    }


}