<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/4/13
 * Time: 11:01 AM
 */

namespace ServerManager\API\Hosting\DA;


class Error extends \ServerManager\API\Hosting\Error
{
    public function __construct(array $error)
    {
        if (isset($error['number'])) {
            $this->number = $error['number'];
            unset($error['number']);
        }

        if (isset($error['string'])) {
            $this->string = $error['string'];
            unset($error['string']);
        }
        $this->error = $error;
    }
} 