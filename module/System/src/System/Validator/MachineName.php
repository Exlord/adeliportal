<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 6/14/14
 * Time: 12:26 PM
 */

namespace System\Validator;


use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

class MachineName extends AbstractValidator
{
    const INVALID = 'invalid';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "A valid machine name starts with a letter or underscore, followed by any number of letters, numbers, or underscores",
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
        $pattern = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
        if (!preg_match($pattern, $value)) {
            $this->error(self::INVALID);
            return false;
        }
        return true;
    }
}