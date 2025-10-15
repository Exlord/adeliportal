<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/19/14
 * Time: 11:21 AM
 */

namespace System\Validator;


use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

class SpamGuard extends AbstractValidator
{
    const SPAM = 'float';

    protected $messageTemplates = array(
        self::SPAM => 'Spam Detected ! Shame on you spammers are not allowed to submit data.'
    );


    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     * @return bool
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value)
    {
        if ($value == '')
            return true;
        else {
            $this->error(self::SPAM);
            return false;
        }
    }
}