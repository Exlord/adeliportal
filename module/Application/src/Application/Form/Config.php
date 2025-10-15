<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Application\Form;

use Application\API\Backup\Db;
use Application\Form\Config\Domains;
use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Extension;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\MimeType;

class Config extends BaseForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        $this->setAttribute('class', 'normal-form ajax_submit');
        parent::__construct('system_config');
        $this->setAttribute('action', url('admin/configs/system'));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'default_route',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Default Route',
                'description' => sprintf(t(
                        'Sites Default Route. Could be an internal route like : %scms , blog , forum%s or on external url like http://www.azaript.com'
                        . '<br/>' . "for relative internal urls copy everything after language. for example for %s/fa/content/1%s use %s/content/1%s"),
                    '<span class="left-align">', '</span>', '<span class="left-align" dir="ltr">', '</span>', '<span class="left-align" dir="ltr">', '</span>')
            ),
            'attributes' => array(
                'value' => '/',
                'dir' => 'ltr',
                'class' => 'left-align',
                'style' => 'max-width:300px;'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'intro',
            'options' => array(
                'label' => 'Application_Config_HasIntroPage',
                'description' => 'a standalone intro view script will be rendered as the sites first page'
            ),
        ));

        if (getSM()->has('domain_table')) {
            $domains = getSM('domain_table')->getArray();
            if (count($domains)) {
                $this->add(new Domains($domains));
            }
        }

        $this->add(array(
            'name' => 'browserTitle',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => "Site's Title in Browser",
                // 'description' => ''
            ),
            'attributes' => array()
        ));


        $this->add(array(
            'name' => 'favIcon',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(),
            'options' => array(
                'label' => 'Browser Favicon',
                'description' => 'choose a icon for your browser(favicon). allowed extensions: gif,ico'
            ),
        ));

        $this->add(array(
            'name' => 'adminLogo',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(),
            'options' => array(
                'label' => 'APP_ADMIN_LOGO',
                // 'description' => 'choose a icon for your browser(favicon). allowed extensions: gif,ico'
            ),
        ));

        $this->add(array(
            'name' => 'favIconUrl',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'adminLogoUrl',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'siteTitle',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => "Site's main title",
                // 'description' => ''
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'siteSubTitle',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => "Site's secondary title",
                'description' => 'displays in smaller font under the main title'
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Description',
                'description' => "descriptions of your site's content, separated with comma, keep it short and simple"
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'keywords',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Keywords',
                'description' => 'Comma separated keywords to witch search engines look for to index the content. For example : text1,text2,text3'
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'newDbBackupInterval',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Auto backup',
                'description' => 'how often do you want to create a new database backup?',
                'value_options' => Db::$autoInterval
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'name' => 'dbBackupCleanupInterval',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Delete backups older than',
                'description' => 'delete backups older than this period',
                'value_options' => Db::$autoCleanupDistance
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'name' => 'dbBackupMaxCount',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Delete backups more than',
                'description' => 'delete oldest backups if you have more than this many backups. to keep all of them enter 0',
            ),
            'attributes' => array(
                'class' => 'spinner'
            )
        ));

        $this->add(array(
            'name' => 'dbBackupMaxSize',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Delete backups larger than',
                'description' => 'delete oldest backups if your backups total size is bigger that this amount. exp: 500MB. to keep all of them enter 0',
                'add-on-append' => 'B/KB/MB/GB/TB'
            ),
            'attributes' => array(
                'sir' => 'ltr',
                'class' => 'left-align',
            ),

        ));

        $this->add(new Buttons('system_config'));
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
            'dbBackupMaxCount' => array(
                'name' => 'dbBackupMaxCount',
                'required' => false,
                'allow_empty' => true,
                'filters' => array(
                    new StringTrim(),
                    new StripTags()
                )
            ),
            'dbBackupMaxSize' => array(
                'name' => 'dbBackupMaxSize',
                'required' => false,
                'allow_empty' => true,
                'filters' => array(
                    new StringTrim(),
                    new StripTags()
                )
            ),
            'favIcon' => array(
                'name' => 'favIcon',
                'required' => false,
                'allow_empty' => true,
                'validators' => array(
                    new Extension('gif,ico'),
                    new MimeType('image')
                )
            ),
            'adminLogo' => array(
                'name' => 'adminLogo',
                'required' => false,
                'allow_empty' => true,
                'validators' => array(
                    new Extension('gif,jpg,png,jpeg'),
                    new MimeType('image'),
                   // new FilesSize(512000),
                )
            ),
        );
    }
}
