<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 6/7/13
 * Time: 8:03 PM
 */

namespace Application\Form;


use System\Form\BaseForm;
use System\IO\Directory;
use Zend\InputFilter\InputFilterProviderInterface;

class Cache extends BaseForm implements InputFilterProviderInterface
{
    private $folders, $files;

    public function __construct($folders, $files)
    {
        $this->folders = $folders;
        $this->files = $files;
        $this->setAction(url('admin/cache'));
        //don't submit this form with ajax
        $this->setAttribute('class', 'force-normal');
        parent::__construct('cache');
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'clear_cache',
            'options' => array(
                'label' => 'Clear All Cache',
                'help-block' => 'Clear all systems cache files.',
                //'twb-layout' => 'inline'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'block_url',
            'options' => array(
                'label' => 'Clear Blocks',
                'help-block' => 'clear cached blocks list per each url',
                //'twb-layout' => 'inline'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'cache_url',
            'options' => array(
                'label' => 'Clear Cached Urls',
                'help-block' => 'clear cached urls for matched routes',
                //'twb-layout' => 'inline'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'public_css',
            'options' => array(
                'label' => 'Cached Css',
                'help-block' => 'Delete css files cached in public folder by all the modules.',
                //'twb-layout' => 'inline'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'public_js',
            'options' => array(
                'label' => 'Cached Js',
                'help-block' => 'Delete js files cached in public folder by all the modules.',
                //'twb-layout' => 'inline'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'thumb',
            'options' => array(
                'label' => 'Image thumbnails',
                'help-block' => 'Delete image thumbnail files cached in public folder by all the modules.',
                //'twb-layout' => 'inline'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'captcha',
            'options' => array(
                'label' => 'Captcha',
                'help-block' => 'Delete captcha created image files',
                //'twb-layout' => 'inline'
            ),
        ));

        $foldersOptions = array();
        if (is_array($this->folders) && count($this->folders)) {
            foreach ($this->folders as $f) {
                $foldersOptions[$f] = $f;
            }
        }

        $filesOptions = array();
        if (is_array($this->files) && count($this->files)) {
            foreach ($this->files as $f) {
                $filesOptions[$f] = $f;
            }
        }

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'public_folder',
            'options' => array(
                'label' => 'Public Folder',
                'help-block' => 'Clean out everything unnecessary in public folder',
                'value_options' => array(
                    'folders' => array('label' => 'Folders', 'options' => $foldersOptions),
                    'files' => array('label' => 'Files', 'options' => $filesOptions)
                ),
                'label_attributes' => array(),
            ),
            'attributes' => array(
                'multiple' => 'multiple',
                'size' => 10,
                'style' => 'width:150px;'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Button',
            'name' => 'submit',
            'options' => array(
                'help-block' => array(),
                'label' => "Process",
                'glyphicon' => "remove",
//                'twb-layout' => "inline",
            ),
            'attributes' => array(
                'value' => 'Process',
                'class' => 'btn btn-danger',
                'type' => 'submit'
            ),
        ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'public_folder' => array(
                'required' => false,
                'allow_empty' => true
            )
        );
    }
}