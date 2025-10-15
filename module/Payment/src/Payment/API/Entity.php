<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace Payment\API;

use Zend\Form\Element;

class Entity
{
    public function save($data)
    {
        if (isset($data['userId']) && $data['userId']) {
            $model = new \Payment\Model\PaymentEntity();
            if (isset($data['paymentId']) && $data['paymentId'])
                $model->paymentId = $data['paymentId'];
            if (isset($data['entityId']) && $data['entityId'])
                $model->entityId = $data['entityId'];
            if (isset($data['entityType']) && $data['entityType'])
                $model->entityType = $data['entityType'];
            if (isset($data['userId']) && $data['userId'])
                $model->userId = $data['userId'];
            getSM('payment_entity_table')->save($model);
        }
    }

    public function search($entityId, $entityType, $userId)
    {
        $result = false;
        if ($entityId && $entityType) {
            $select = getSM('payment_entity_table')->getAll(array('entityId' => $entityId, 'entityType' => $entityType, 'userId' => $userId))->current();
            if ($select)
                return true;
        }
        return $result;
    }

}