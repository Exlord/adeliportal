<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/20/14
 * Time: 10:27 AM
 */

namespace System\Captcha;


use Zend\Captcha\Image;

class NumberImage extends Image
{
    protected function generateWord()
    {
        $word = '';
        $wordLen = $this->getWordLen();

        for ($i = 0; $i < $wordLen; $i = $i + 1) {
            // generate word with mix of vowels and consonants
            $word .= mt_rand(1, 9);
        }

        if (strlen($word) > $wordLen) {
            $word = substr($word, 0, $wordLen);
        }

        return $word;
    }
} 