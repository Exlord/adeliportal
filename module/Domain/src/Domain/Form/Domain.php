<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Domain\Form;

use Application\API\App;
use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\Hostname;
use Zend\Validator\StringLength;
use Zend\Filter;

class Domain extends BaseForm
{
    private $domain = null;

    public function __construct($domain = null)
    {
        $this->domain = $domain;
        parent::__construct('domain');
        $this->setAttribute('class', 'normal-form ajax_submit');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'domain',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Domain',
                'description' => 'full (sub)domain name without www, including the extension.<br/>' .
                    'exp: mysite.com , sub1.mysite.com ...'
            ),
            'attributes' => array(),
        ));

        $this->add(new Buttons('domain'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        $NoRecordExists = array(
            'table' => 'tbl_domains',
            'field' => 'domain',
            'adapter' => App::getDbAdapter(),
            'messages' => array(
                NoRecordExists::ERROR_RECORD_FOUND => 'This domain name has been used before, domain should be unique.'
            )
        );
        if ($this->domain) {
            $NoRecordExists['exclude'] = array(
                'field' => 'domain',
                'value' => $this->domain
            );
        }

        $domain = $filter->get('domain');
        $domain
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 3, 'max' => 100)))
            ->attach(new NoRecordExists($NoRecordExists))
            ->attach(new Hostname(Hostname::ALLOW_DNS | Hostname::ALLOW_LOCAL));
        $domain->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());
    }
}
