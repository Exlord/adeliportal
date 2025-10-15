<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Comment\Form;

use Application\API\App;
use System\Captcha\CaptchaFactory;
use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Filter;

class CommentForm extends BaseForm
{

    private $type; //new or edit
    public function __construct($type)
    {
        $this->type = $type;
        parent::__construct('comment_form');
        $this->setAttributes(array(
            'class'=> 'normal-form',
            'action' => ''
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'entityId',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'parentId',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'entityType',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name'
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
        ));

        $this->add(array(
            'name' => 'comment',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Comment'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf_comment_form'
        ));

        if ($this->type == 'new') {
//            $image = new \Zend\Captcha\Image();
//            $image->setFont(PUBLIC_PATH . '/fonts/arial.ttf');
//            $image->setImgDir(PUBLIC_FILE . '/captcha');
//            $image->setImgUrl(App::siteUrl() . '/clients/' . ACTIVE_SITE . '/files/captcha');
//            $image->setDotNoiseLevel(5);
//            $image->setWordlen(4);
//            $image->setFontSize(35);
//            $image->setWidth(150);
//            $image->setHeight(80);
//            $captcha = new Element\Captcha('captcha');
//            $captcha->setCaptcha($image);
//            $captcha->setAttribute('class', 'captcha');

            $this->add(CaptchaFactory::create());
        }

        if ($this->type == 'edit') {
            $this->add(array(
                'type' => 'Zend\Form\Element\Submit',
                'name' => 'submitEdit',
                'attributes' => array(
                    'value' => 'Edit',
                    'class' => 'button edit_button',
                ),
            ));
        }


    }


    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();

        //    $sampleFiled = $filter->get('sampleField');
    }

}
