<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/16/14
 * Time: 10:22 AM
 */

namespace EducationalCenter\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\InputFilter\InputFilterProviderInterface;

class AttendanceCancel extends BaseForm implements InputFilterProviderInterface
{
    private $hasRefund = false;

    public function __construct($hasRefund = false)
    {
        $this->hasRefund = $hasRefund;
        parent::__construct('attendance_cancel');
        $this->setAttribute('class', 'ajax_submit');
    }

    protected function addElements()
    {
        if ($this->hasRefund) {
            $this->add(array(
                'type' => 'Text',
                'name' => 'refund',
                'options' => array(
                    'label' => 'Refund',
                    'add-on-append' => t(getCurrency()),
                )
            ));
        }

        $this->add(array(
            'type' => 'Textarea',
            'name' => 'cancelReason',
            'options' => array(
                'label' => 'Cancel Reason/Note'
            )
        ));

        $this->add(new Buttons('attendance_cancel', array(Buttons::SAVE, Buttons::CANCEL . Buttons::SPAM, Buttons::CSRF)));
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
            'cancelReason' => array(
                'name' => 'cancelReason',
                'filters' => array(
                    new StringTrim(),
                    new StripTags()
                )
            )
        );
    }
}