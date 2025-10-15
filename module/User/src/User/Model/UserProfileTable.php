<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 5/21/13
 * Time: 7:41 PM
 */

namespace User\Model;


use System\DB\BaseTableGateway;

class UserProfileTable extends BaseTableGateway
{
    protected $table = 'tbl_user_profile';
    protected $model = 'User\Model\UserProfile';
    protected $caches = null;
    protected $primaryKey = 'id';

    public function removeByUserId($id)
    {
        $profile = $this->getByUserId($id);
        if ($profile) {
            if ($profile->image) {
                if (is_file(PUBLIC_PATH . $profile->image)) {
                    unlink(PUBLIC_PATH . $profile->image);
                } elseif (is_file($profile->image))
                    unlink($profile->image);
            }
            parent::remove($profile->id);
        }
    }

    public function getByUserId($uid)
    {
        $result = $this->select(array('userId' => $uid));
        if ($result)
            return $result->current();
        else
            return null;
    }
}