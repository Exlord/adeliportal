<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Page\Form;

use Zend\Form\Fieldset;

class PageConfig extends Fieldset
{
    public function __construct()
    {
        parent::__construct('config');
        $this->setAttribute('id', 'config');

        $this->add(array(
            'name' => 'viewAuthor',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_AUTHOR_ON_PAGE',
                'value_options' => array(
                    '2' => 'Inherited',
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'showTitle',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_TITLE',
                'value_options' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'name' => 'showTags',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_TAGS_ON_PAGE',
                'value_options' => array(
                    '2' => 'Inherited',
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'showDate',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_DATE_ON_PAGE',
                'value_options' => array(
                    '2' => 'Inherited',
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'showHits',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_HITS_ON_PAGE',
                'value_options' => array(
                    '2' => 'Inherited',
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'showCommentSection',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_COMMENT_ON_PAGE',
                'value_options' => array(
                    '2' => 'Inherited',
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'showRateSection',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_RATE_ON_PAGE',
                'value_options' => array(
                    '2' => 'Inherited',
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'commentStatus',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'When commenting, what is the situation?',
                'value_options' => array(
                    '0' => 'Not Approved',
                    '1' => 'Approved',
                    '2' => 'Inherited',
                    '3' => 'Authentication Manager',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

    }
}
