<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/14/14
 * Time: 9:54 AM
 */

namespace Notify\API;


use Mail\API\Mail;

class SystemRecipient
{
    public $id;
    public $email;
    public $mobile;
    public $name;

    public function from()
    {
        if (isset($this->email)) {
            return array($this->email => $this->name);
        } else
            return Mail::getFrom();
    }

    public function to()
    {
        if (isset($this->email)) {
            return array($this->email => $this->name);
        } else
            return Mail::getTo();
    }
} 