<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 10:21 AM
 */

namespace User\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\I18n\Filter\Alnum;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\EmailAddress;
use Zend\Validator\File\Extension;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;
use Zend\Validator\File\UploadFile;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;
use Zend\Filter;

class EditImage extends BaseForm
{

    public function __construct()
    {
        $this->setAttribute('class', 'normal-form ajax_submit');
        parent::__construct('user');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'image',
            'type' => 'Zend\Form\Element\File',
            'options' => array(
                'label' => 'Image',
                'description' =>
                    'Allowed extensions are jpg, jpeg, png and gif<br/>' .
                    'Min size : 100x100 and Max size : 1000x1000<br/>' .
                    'Max file size : 500KB',
            ),
        ));
        $this->add(new Buttons('edit_profile_image'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $image FileInput
         */
        $filter = $this->getInputFilter();

        $image = $filter->get('image');
        $image
            ->getValidatorChain()
            ->attach(new Extension(array('jpg', 'jpeg', 'png', 'gif')))
            ->attach(new ImageSize(100, 100, 1000, 1000))
            ->attach(new IsImage())
            ->attach(new MimeType(array('image/gif', 'image/jpg', 'image/png', 'image/jpeg')))
            ->attach(new Size(array('max' => '500KB')))
            ->attach(new UploadFile());
    }
}
