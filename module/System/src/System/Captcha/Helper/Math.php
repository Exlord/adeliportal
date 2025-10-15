<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/18/14
 * Time: 1:17 PM
 */

namespace System\Captcha\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\Captcha\AbstractWord;
use Zend\Form\Exception;

class Math extends AbstractWord
{
    public function render(ElementInterface $element)
    {
        $element->setAttribute('style', 'max-width:40px;display:inline-block;text-align:center;');
        /* @var $captcha \System\Captcha\Math */
        $captcha = $element->getCaptcha();

        if ($captcha === null || !$captcha instanceof \System\Captcha\Math) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute of type System\Captcha\Operation; none found',
                __METHOD__
            ));
        }

        $captcha->generate();

        $pattern = "<span class='operand1'>%s</span> <span class='operator'>%s</span> <span class='operand2'>%s</span> <span class='equal'>%s</span>";
        $text = sprintf($pattern,
            $captcha->getOperand1(),
            $captcha->getOperator(),
            $captcha->getOperand2(),
            $captcha->getEqualSign()
        );

        $position = $this->getCaptchaPosition();
        $separator = $this->getSeparator();
        $captchaInput = $this->renderCaptchaInputs($element);

        $pattern = '%s%s%s';
        if ($position == self::CAPTCHA_PREPEND) {
            return sprintf($pattern, $captchaInput, $separator, $text);
        }

        return sprintf($pattern, $text, $separator, $captchaInput);
    }
} 