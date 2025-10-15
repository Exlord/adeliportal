<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/2/13
 * Time: 3:12 PM
 */

namespace FormsManager\Form;


use Application\API\App;
use System\Captcha\CaptchaFactory;
use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Form\Element\Captcha;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

class FormTemplate extends BaseForm implements InputFilterProviderInterface
{
    protected $loadInputFilters = false;
    private $_captcha = false;

    public function __construct($captcha = false, $id = 0)
    {
        $this->_captcha = $captcha;
        parent::__construct('form_template');
        $this->setAttribute('id', 'dynamic_form_template_' . $id);
    }

    protected function addElements()
    {
        if ($this->_captcha) {
//            $image = new \Zend\Captcha\Image();
//            $image->setFont(PUBLIC_PATH . '/fonts/arial.ttf');
//            $image->setImgDir(PUBLIC_FILE . '/captcha');
//            $image->setImgUrl(App::siteUrl() . PUBLIC_FILE_PATH . '/captcha');
//            $image->setDotNoiseLevel(5);
//            $image->setWordlen(4);
//            $image->setFontSize(35);
//            $image->setWidth(150);
//            $image->setHeight(80);
//            $captcha = new Captcha('captcha');
//            $captcha->setCaptcha($image);
//            $captcha->setAttribute('class', 'captcha');

            $this->add(CaptchaFactory::create());
        }
        $buttons = new Buttons('form_template');
        $this->add($buttons);
    }

    public function addInputFilters($filters)
    {
        parent::addInputFilters($filters);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return $this->inputFilters;
    }
}