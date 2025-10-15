<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class UriNormalize extends BaseFilter
{
    protected $label = 'UriNormalize';
    protected $attributes = array(
        'id' => 'filter_UriNormalize',
        'name' => 'Zend\Filter\UriNormalize'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('This filter can set a scheme on an URI, if a scheme is not present. If a scheme is present, that scheme will not be affected, even if a different scheme is enforced.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'defaultScheme',
            'options' => array(
                'label' => 'Default Scheme',
                'description' => 'This option can be used to set the default scheme to use when parsing scheme-less URIs.'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'enforcedScheme',
            'options' => array(
                'label' => 'Enforced Scheme',
                'description' => 'Set a URI scheme to enforce on schemeless URIs.'
            )
        ));
    }
} 