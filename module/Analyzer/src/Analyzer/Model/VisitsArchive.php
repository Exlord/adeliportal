<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/23/14
 * Time: 10:28 AM
 */

namespace Analyzer\Model;


use System\Model\BaseModel;

class VisitsArchive extends BaseModel
{
    public $id;
    public $date;
    public $count = 0;
    public $uniqueCount = 0;

    /**
     * @param int $uniqueCount
     */
    public function setUniqueCount($uniqueCount)
    {
        $this->uniqueCount = $uniqueCount;
    }

    /**
     * @return int
     */
    public function getUniqueCount()
    {
        return $this->uniqueCount;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}