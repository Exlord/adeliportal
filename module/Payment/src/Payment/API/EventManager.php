<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/13/2014
 * Time: 10:47 AM
 */

namespace Payment\API;


use Menu\Form\MenuItem;
use Zend\EventManager\Event;
use Zend\Form\Fieldset;

class EventManager
{
    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');
        $form->menuTypes['payment'] = array(
            'label' => 'Online Payment',
            'note' => 'Payment page for users',
            'params' => array(array('route' => 'app/payment'),),
        );
    }

    public function onNewUser(Event $e)
    {
        $userId = $e->getParam('userId', false);
        if ($userId) {
            $config = getConfig('transactions_config')->varValue;
            if (isset($config['baseMoney']) && $config['baseMoney']) {
                $dataArray = array(
                    'userId' => $userId,
                    'note' => t('PAYMENT_USER_REGISTER'),
                    'amount' => $config['baseMoney'],
                    'date' => time(),
                    'adminId' => 0,
                );
                getSM('transactions_api')->insertTransactions($dataArray);
            }
        }
    }

    public function onCC_PointsConfigLoad(Event $e)
    {
        $moduleFieldset = $e->getParam('moduleFieldset');

        $amount = new Fieldset('Amount');
        $amount->setLabel('Payment Amounts');
        $amount->setOption('description', 'define a range of payment amounts and how many points the user gets for it');
        $moduleFieldset->add($amount);

        $amount->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'values',
            'options' => array(
                'count' => 0,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Payment\Form\CCPointsConfig'
                )
            ),
            'attributes' => array(
                'class' => 'collection-container'
            ),
        ));

        $amount->add(array(
            'name' => 'add_more_select_option',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'title' => t('Add More Select Options'),
                'value' => t('Add More'),
                'class' => 'btn btn-default add_collection_item',
            ),
        ));
    }

    public function onCC_CustomerRecords_Load($e)
    {
        $ccApi = $e->getTarget();
        $userId = $e->getParam('userId');
        $count = getSM('Payment_entity_table')->getPaymentCount($userId);

        if ($count && $count->count()) {
            $count = $count->current();
            if ($count)
                $ccApi->records['Payments'][] = array(t('Successful Online Payments'), $count['count']);
        }
    }
} 