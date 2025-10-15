<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 5/12/14
 * Time: 4:27 PM
 */

namespace System\Paginator\Adapter;


use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class DbSelect extends \Zend\Paginator\Adapter\DbSelect{
    /**
     * Returns the total number of rows in the result set.
     *
     * @return int
     */
    public function count()
    {
        if ($this->rowCount !== null) {
            return $this->rowCount;
        }

        $select = clone $this->select;
        $select->reset(Select::LIMIT);
        $select->reset(Select::OFFSET);
        $select->reset(Select::ORDER);

//        $countSelect = new Select;
//        $countSelect->columns(array('c' => new Expression('COUNT(1)')));
//        $countSelect->from(array('original_select' => $select));
        $select->columns(array('c' => new Expression('COUNT(1)')));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result    = $statement->execute();
        $row       = $result->current();

        $this->rowCount = $row['c'];

        return $this->rowCount;
    }
}