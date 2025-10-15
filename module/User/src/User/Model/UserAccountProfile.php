<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 5/21/13
 * Time: 8:29 PM
 */

namespace User\Model;


use System\Model\BaseModel;

class UserAccountProfile extends BaseModel
{
    public $roles = array();
    public $account = array();
    public $profile = array();
    public $profile2 = array();

    /**
     * @param array $profile2
     */
    public function setProfile2($profile2)
    {
        $this->profile2 = $profile2;
    }

    /**
     * @return array
     */
    public function getProfile2()
    {
        return $this->profile2;
    }


    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }


    /**
     * @param mixed $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param mixed $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

}