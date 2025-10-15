<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/20/2014
 * Time: 12:54 PM
 */

namespace User\Model;


use System\DB\BaseTableGateway;

class Flood extends BaseTableGateway
{
    protected $table = 'tbl_user_flood';

    public function getFailedAttempts()
    {
        $ip = getSM('Request')->getServer('REMOTE_ADDR');
        $select = $this->getSql()->select();
        $select
            ->where(array('ip' => $ip))
            ->order(array('timestamp DESC'))
            ->limit(5);
        return $this->selectWith($select);
    }

    public function clearExpired($expireDistance = '-1 hour')
    {
        $timestamp = strtotime($expireDistance);
        if (!$timestamp)
            $timestamp = strtotime('-1 hour');
        $this->delete(array('timestamp < ?' => $timestamp));
    }
} 