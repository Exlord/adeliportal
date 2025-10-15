<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 6/7/13
 * Time: 8:03 PM
 */

namespace Application\Form;


use Application\API\App;
use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\I18n\Filter\Alnum;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\StringLength;

class Alias extends BaseForm
{
    private $alias = null;

    public function __construct($alias = null)
    {
        $this->alias = $alias;
        parent::__construct('alias');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('data-cancel', 'admin/alias');
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'url',
            'options' => array(
                'label' => 'Url',
                'description' => 'the url witch you want to make an alias for.<br/>' .
                    'exp: for "http://www.mysite.com/my/real/url" take "/my/real/url"'
            ),
            'attributes' => array(
                'class' => 'left-align',
                'size' => '100'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'alias',
            'options' => array(
                'label' => 'Alias',
                'description' => 'an alias for your real url.<br/>' .
                    'this should be a valid url and should not contain any white space and special characters.<br/>' .
                    'exp: an alias for "http://www.mysite.com/my/real/url" could be something like "/my-alias-url".<br/>' .
                    'exp : Real Url:"http://www.mysite.com/fa/page/10/about-us" ,Alias:"/about-us"'
            ),
            'attributes' => array(
                'class' => 'left-align',
                'size' => '100'
            )
        ));


        $this->add(new Buttons('alias'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        $url = $filter->get('url');
        $url->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 1, 'max' => 500)));
        $url->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags());

        $NoRecordExists = array(
            'table' => 'tbl_alias_url',
            'field' => 'alias',
            'adapter' => App::getDbAdapter(),
            'messages' => array(
                NoRecordExists::ERROR_RECORD_FOUND => 'This Alias has been used before, Alias should be unique.'
            )
        );
        if ($this->alias) {
            $NoRecordExists['exclude'] = array(
                'field' => 'alias',
                'value' => $this->alias
            );
        }

        $alias = $filter->get('alias');
        $alias
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 1, 'max' => 500)))
            ->attach(new NoRecordExists($NoRecordExists));
        $alias->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags());
    }
}