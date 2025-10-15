<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/4/13
 * Time: 11:02 AM
 */

namespace ServerManager\API\Hosting\DA;


class Result extends \ServerManager\API\Hosting\Result
{
    public function __construct(array $result)
    {
        if (isset($result['error'])) {
            $this->error = $result['error'];
            unset($result['error']);
        }

        if (isset($result['text'])) {
            $this->text = $result['text'];
            unset($result['text']);
        }

        if (isset($result['details'])) {
            $this->details = $result['details'];
            unset($result['details']);
        }
        $this->result = $result;
    }
} 