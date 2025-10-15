<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/6/2014
 * Time: 11:31 AM
 */

namespace File\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use System\Validator\FileName;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\I18n\Validator\Alnum;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Extension;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;
use Zend\Validator\File\UploadFile;

class PrivateFile extends BaseForm implements InputFilterProviderInterface
{
    private $isEdit = false;

    public function __construct($isEdit = false)
    {
        $this->isEdit = $isEdit;
        parent::__construct('private_file');
        $this->setAttribute('class', 'ajax_submit');
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Text',
            'name' => 'name',
            'options' => array(
                'label' => 'Name',
                'description' => 'this name will be displayed as the download links text'
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'type' => 'Text',
            'name' => 'title',
            'options' => array(
                'label' => 'Title',
                'description' => 'the title attribute of the download link'
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'type' => 'Text',
            'name' => 'downloadAs',
            'options' => array(
                'label' => 'Download Name',
                'description' => 'the file will be downloaded with this name'
            ),
            'attributes' => array(
                'class' => 'text-left dir-ltr'
            )
        ));

        $validators = \File\API\PrivateFile::getUploadValidators();

        $this->add(array(
            'name' => 'path',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(),
            'options' => array(
                'label' => 'File',
                'description' =>
                    sprintf(t('max allowed file size is %s'), $validators['max_upload_size']) . "<br/>" .
                    sprintf(t('allowed file extensions are %s'), implode(' , ', $validators['extensions'])) . "<br/>",
            ),
        ));

        $_roles = getSM('role_table')->getVisibleRoles(true);
        $roles = array();
        foreach ($_roles as $id => $role) {
            $roles[$id] = t($role->roleName);
        }
        $this->add(array(
            'type' => 'MultiCheckbox',
            'name' => 'accessibility',
            'options' => array(
                'label' => 'File Accessibility',
                'value_options' => $roles,
                'description' => 'witch user groups are allowed to download this file',
                'inline' => false,
            )
        ));

        $this->add(new Buttons('private_file'));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $validators = \File\API\PrivateFile::getUploadValidators();
        $input = array(
            'name' => array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    new StringTrim(),
                    new StripTags()
                ),
            ),
            'title' => array(
                'name' => 'title',
                'required' => false,
                'filters' => array(
                    new StringTrim(),
                    new StripTags()
                ),
            ),
            'downloadAs' => array(
                'name' => 'downloadAs',
                'required' => true,
                'filters' => array(
                    new StringTrim(),
                    new StripTags(),
                ),
                'validators' => array(
                    new FileName()
                )
            ),
            'path' => array(
                'name' => 'path',
                'required' => true,
                'allow_empty' => false,
                'validators' => array(
                    new Size($validators['max_upload_size']),
                    new Extension($validators['extensions']),
                    new MimeType($validators['mime_types']),
                    new UploadFile()
                )
            ),
            'accessibility' => array(
                'name' => 'accessibility',
                'required' => false,
                'allow_empty' => true,
            )
        );

        if ($this->isEdit) {
            $input['path']['required'] = false;
        }

        return $input;
    }
}