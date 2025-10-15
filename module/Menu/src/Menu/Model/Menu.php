<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:13 PM
 */
namespace Menu\Model;
use System\Model\BaseModel;

/*
 *   `id` int(11) NOT NULL AUTO_INCREMENT,
  `menuTitle` varchar(400) DEFAULT NULL,
  `menuName` varchar(200) DEFAULT NULL,
 */

/**
 * Class Menu
 * @package Menu\Model
 */
class Menu extends BaseModel
{
    public $id;
    public $menuTitle;
    public $menuName;

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

    /**
     * @param mixed $menuName
     */
    public function setMenuName($menuName)
    {
        $this->menuName = $menuName;
    }

    /**
     * @return mixed
     */
    public function getMenuName()
    {
        return $this->menuName;
    }

    /**
     * @param mixed $menuTitle
     */
    public function setMenuTitle($menuTitle)
    {
        $this->menuTitle = $menuTitle;
    }

    /**
     * @return mixed
     */
    public function getMenuTitle()
    {
        return $this->menuTitle;
    }


}
