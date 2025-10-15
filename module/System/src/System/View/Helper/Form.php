<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/17/14
 * Time: 9:59 AM
 */

namespace System\View\Helper;


use TwbBundle\Form\View\Helper\TwbBundleForm;

class Form extends TwbBundleForm
{
    /**
     * @see \Zend\Form\View\Helper\Form::__invoke()
     * @param \Zend\Form\FormInterface $oForm
     * @param string $sFormLayout
     * @return \TwbBundle\Form\View\Helper\TwbBundleForm|string
     */
    public function __invoke(\Zend\Form\FormInterface $oForm = null, $sFormLayout = null)
    {
        return parent::__invoke($oForm, $sFormLayout);
    }
} 