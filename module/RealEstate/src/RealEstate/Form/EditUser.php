<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Koushan
 * Date: 7/23/13
 * Time: 10:33 AM
 * To change this template use File | Settings | File Templates.
 */

namespace RealEstate\Form;


use System\Form\BaseForm;
use Zend\Form\Element;


class EditUser extends BaseForm
{
    public function __construct()
    {
        parent::__construct('EditUser');
        $this->setAttributes(array(
            'class'=> 'normal-form',
            'action'=> url('app/real-estate/edit-user'),
        ));
    }

    protected function addElements()
    {
        $csrf = new Element\Csrf('edit_user_Csrf');


        $id = new Element\Text('id');
        $id->setLabel('Realty Id');
        $id->setAttributes(array(
            'class'=>'el-per-txt'
        ));

        $passForEdit = new Element\Password('passForEdit');
        $passForEdit->setLabel('Password For Home Edit');
        $passForEdit->setAttributes(array(
            'class'=>'el-per-txt'
        ));

        $ownerMobile = new Element\Text('ownerMobile');
        $ownerMobile->setLabel('Mobile');
        $ownerMobile->setAttributes(array(
            'class'=>'el-per-txt'
        ));

        $submit = new Element\Submit('submit');
        $submit->setValue('Search');
        $submit->setAttributes(array(
            'class' => 'button search_button'
        ));

        $this->add($id);
        $this->add($passForEdit);
        $this->add($ownerMobile);
        $this->add($csrf);
        $this->add($submit);

    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        #filter by digit only
        $this->filterByDigit($filter, array(
            'id',
            'ownerMobile',
        ));

        $ownerName = $filter->get('ownerMobile');
        $ownerName->setRequired(true);
        $ownerName->getValidatorChain()
            ->attach(new \Zend\Validator\StringLength(array('max' => 11)));

        $this->filterByTrimAndTags($filter, array(
            'passForEdit',
        ));


    }
}