<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/22/2014
 * Time: 1:11 PM
 */

namespace Sms\API\Service;


use Sms\API\SMS;

abstract class Panel
{
    /**
     * @var SMS
     */
    protected $api;

    /**
     * @param SMS $api
     */
    public function __construct($api)
    {
        $this->api = $api;
    }

    abstract public function Send($username, $password, $from, $to, $msg);
} 