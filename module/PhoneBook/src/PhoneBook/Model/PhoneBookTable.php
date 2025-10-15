<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace PhoneBook\Model;

use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\PredicateSet;

class PhoneBookTable extends BaseTableGateway
{
    protected $table = 'tbl_phone_book';
    protected $model = 'PhoneBook\Model\PhoneBook';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getEmails()
    {
        $email = array('-1'=>t('-- Select --'));
        $sql = $this->getSql();
        $select = $sql->select();
        // $select->where($where);
        $select->order(array('id DESC'));
        $select->columns(array('email', 'nameAndFamily'));
        $data = $this->selectWith($select);
        foreach ($data as $row) {
            $email[$row->email] = $row->nameAndFamily;
        }
        return $email;
    }

    public function searchEmail($email)
    {
        $result = $this->getAll(array('email'=>$email))->count();
        if($result)
            return true;
        else
            return false;
    }

}


