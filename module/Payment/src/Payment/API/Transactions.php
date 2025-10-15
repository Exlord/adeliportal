<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace Payment\API;

use Zend\Form\Element;

class Transactions
{
    public function insertTransactions($data)
    {
        if (isset($data['userId']) && $data['userId']) {
            $userIdArray = explode(',', $data['userId']);
            $dataArray = array();
            foreach ($userIdArray as $val) {
                $dataArray[] = array(
                    'userId' => $val,
                    'note' => $data['note'],
                    'amount' => $data['amount'],
                    'date' => time(),
                    'adminId' => $data['adminId'],
                );
            }
            getSM('transactions_table')->multiSave($dataArray);
            getSM('user_amount_table')->updateOrInsertAmount($userIdArray, $data['amount']);

            if ($data['amount'] > 0) {
                $desc = t('PAYMENT_INCREASE_BALANCE');
                $labelEl = '<label class="bg-success text-muted">';
            } else {
                $desc = t('PAYMENT_DECREASE_BALANCE');
                $labelEl = '<label class="bg-danger text-muted">';
            }
            if ($notifyApi = getNotifyApi()) {
                $notifyApi->getInternal()->uId = $userIdArray;
                $notifyApi->notify('Payment', 'change_balance', array(
                    '__NOTE__' => $data['note'],
                    '__AMOUNT__' => $labelEl . $data['amount'] . "</label>",
                    '__DESC__' => $desc,
                ));
            }
            return true;
        }
    }

    public function getTransactions($userId)
    {
        if ($userId) {
            return getSM('user_amount_table')->getAmount($userId);
        }
        return 0;
    }

}