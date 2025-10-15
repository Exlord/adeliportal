<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/17/13
 * Time: 12:01 PM
 */

namespace Payment\API;


class Saman extends Payment
{
    private $sb24PaymentAddress = 'https://sep.shaparak.ir/Payment.aspx';
    private $sb24WSDL = 'https://acquirer.samanepay.com/payments/referencepayment.asmx?WSDL';
    private $sb24WSDL_Rev = 'https://acquirer.samanepay.com/payments/referencepayment.asmx?WSDL';
    private $TransResult = array(
        'OK' => ' تراکنش با موفقیت انجام شد ',
        'Canceled By User' => ' تراکنش توسط خريدار کنسل شده است. ',
        'Invalid Amount' => ' مبلغ سند برگشتي، از مبلغ تراکنش اصلي بيشتر است. ',
        'Invalid Transaction' => ' درخواست برگشت يک تراکنش رسيده است، در حالي که تراکنش اصلي پيدا نمي شود. ',
        'Invalid Card Number' => 'شماره کارت اشتباه است.',
        'No Such Issuer' => 'چنين صادر کننده کارتي وجود ندارد.',
        'Expired Card Pick Up' => ' از تاريخ انقضاي کارت گذشته است و کارت ديگر معتبر نيست. ',
        'Allowable PIN Tries Exceeded Pick Up' => ' رمز کارت (PIN) 3 مرتبه اشتباه وارد شده است در نتيجه کارت غير فعال خواهد شد. ',
        'Incorrect PIN' => ' خريدار رمز کارت (PIN) را اشتباه وارد کرده است. ',
        'Exceeds Withdrawal Amount Limit' => ' مبلغ بيش از سقف برداشت مي باشد. ',
        'Transaction Cannot Be Completed' => ' تراکنش Authorize شده است ( شماره PIN و PAN درست هستند) ولي امکان سند خوردن وجود ندارد. ',
        'Response Received Too Late' => ' تراکنش در شبکه بانکي Timeout خورده است. ',
        'Suspected Fraud Pick Up' => ' خريدار يا فيلد CVV2 و يا فيلد ExpDate را اشتباه زده است. ( يا اصلا وارد نکرده است) ',
        'No Sufficient Funds' => ' موجودي به اندازي کافي در حساب وجود ندارد. ',
        'Issuer Down Slm' => ' سيستم کارت بانک صادر کننده در وضعيت عملياتي نيست. ',
        'TME Error' => ' کليه خطاهاي ديگر بانکي باعث ايجاد چنين خطايي مي گردد. ',
        'Unknown' => ' خطای نامشخصی در عملیت بانکی به وجود آمده است. ',
        'Used' => ' این رسید قبلا استفاده شده است '
    );
    private $ErrorCodes = array(
        1 => 'مبلغ پرداخت شده برابر با کل هزینه نبوده و کل مبلغ برگشت داده شد.',
        -1 => 'خطاي داخلي شبکه مالي.',
        -2 => 'سپرده‌ها برابر نيستند. ',
        -3 => 'ورودي‌ها حاوي کارکترهاي غيرمجاز مي‌باشند.',
        -4 => 'Merchant Authentication Failed ( کلمه عبور يا کد فروشنده اشتباه است)',
        -5 => 'Database Exception',
        -6 => 'سند قبلا برگشت کامل يافته است.',
        -7 => 'رسيد ديجيتالي تهي است.',
        -8 => 'طول ورودي‌ها بيشتر از حد مجاز است.',
        -9 => 'وجود کارکترهاي غيرمجاز در مبلغ برگشتي.',
        -10 => 'رسيد ديجيتالي به صورت Base64 نيست (حاوي کارکترهاي غيرمجاز است).',
        -11 => 'طول ورودي‌ها کمتر از حد مجاز است.',
        -12 => 'مبلغ برگشتي منفي است.',
        -13 => 'مبلغ برگشتي براي برگشت جزئي بيش از مبلغ برگشت نخورده‌ي رسيد ديجيتالي است.',
        -14 => 'چنين تراکنشي تعريف نشده است.',
        -15 => 'مبلغ برگشتي به صورت اعشاري داده شده است.',
        -16 => 'خطاي داخلي سيستم',
        -17 => 'برگشت زدن جزيي تراکنشي که با کارت بانکي غير از بانک سامان انجام پذيرفته است.',
        -18 => 'IP Address  فروشنده نا معتبر است.',
    );

    private $TransactionState;
    private $ReferenceNumber;
    private $ReservationNumber; // haman id table ast ke be onvan resId miferestim
    private $className = 'Payment\API\Saman';
    private $sb24MerchantID;
    private $sb24MerchantPass;
    private $TraceNo;

    public function __construct($amount = null)
    {
        if ($amount)
            parent::__construct($amount);
        $selectBankInfo = getSM()->get('bank_info_table')->getAll(array('className' => $this->className))->current();
        $this->sb24MerchantID = $selectBankInfo->terminalId;
        $this->sb24MerchantPass = $selectBankInfo->passWord;
    }

    public function init($data = array())
    {
        $this->model->data = $data;
        $id = $this->save();
        return $this->renderForm('payment/template/saman',
            array(
                'action' => $this->sb24PaymentAddress,
                'price' => $this->model->amount,
                'mid' => $this->sb24MerchantID,
                'resid' => $id,
                'redirect' => $this->redirect
            )
        );
    }

    public function validate($params = array())
    {
        $this->__initSoapClient();
        if (isset($params) && !empty($params) && $params) { //az arguman begir
            $this->ReferenceNumber = $params['RefNum'];
            $this->ReservationNumber = $params['ResNum'];
            $this->TransactionState = $params['State'];
            $this->TraceNo = $params['TraceNo'];
        }
        return $this->__validate();
    }

    private function __initSoapClient()
    {
        $this->client = new \Zend\Soap\Client($this->sb24WSDL); // 'wsdl' arquman dovvaom bud
        $this->client->setEncoding('UTF-8');
        $this->client->setWSDLCache(false);
        $this->client->setSoapVersion(SOAP_1_1);
//        $this->client->decode_utf8 = false;
    }

    /**
     * @return string
     */
    private function __validate()
    {
        //resid digitali bargashti az bank khalist
        if (!$this->ReferenceNumber || $this->ReferenceNumber == '') {
            if ($this->TransactionState == 'OK')
                return $this->TransResult['Unknown'];
        }
        //natijeye transaction OK ast
        if ($this->TransactionState == 'OK') {

            $ref = getSM()->get('Payment_table')->getAll(array('refId' => $this->ReferenceNumber))->count();
            /*$q = "select count(*) from site_payments where refId = '%s' ";//Ref == uniqueId
            $ref = BaseDAL::GetInstance()->GetScalar($q, array($this->ReferenceNumber));*/

            //resid digitali daryaf shode az bank gablan estefade shode
            if ($ref != 0) {
                return $this->TransResult['Used'];
            }

            $qresult = getSM()->get('Payment_table')->getAll(array('id' => $this->ReservationNumber))->toArray();
            /*$q = "select * from site_payments where id = '%s'";
            $qresult = BaseDAL::GetInstance()->GetTable($q, array($this->ReservationNumber));*/

            $result = $this->VerifyTransaction();
            //transaction as suye bank verify nashod
            if ((int)$result < 0) {
                return $this->ErrorCodes[$result];
            } //pardakht ba movafagiat anjam shode ast
            elseif ((int)$result == (int)$qresult[0]['amount']) {
                $this->payedAmount = $result;
                $date = date('Ymd');

                getSM()->get('Payment_table')->update(array(
                    'refId' => $this->ReferenceNumber,
                    'status' => 1,
                    'payDate' => $date,
                ), array('id' => $this->ReservationNumber));
                /*$q = "update site_payments set refId='%s', status='%s', pay_date=$date where id='%s'";
                BaseDAL::GetInstance()->ExecuteNoneQuery($q, array($this->ReferenceNumber, 'payed', $this->ReservationNumber));*/

                return array(
                    'success' => true,
                    'id' => $this->ReservationNumber,
                    'trackingCode' => $this->TraceNo,
                    'msg' => $this->TransResult['OK']
                );
            } //mablage pardakhti kamtar az mablage darkhastist
            //if ($result != $qresult[0]['amount'])
            else {
                return $this->ReverseTransaction();
            } //mablage pardakht shode bishtar az mablag darkhastist
//            elseif ($result > $qresult[0]['amount']) {
//                $res = $this->ReverseTransaction($result - $qresult[0]['amount']);
//                if ($res == 1)
//                    return $this->ErrorCodes[2];
//                else
//                    return $this->ErrorCodes[$res];
//            }
        } else
            return $this->TransResult[$this->TransactionState];
    }

    /**
     * @return int
     */
    private function VerifyTransaction()
    {
        $result = $this->client->VerifyTransaction($this->ReferenceNumber, $this->sb24MerchantID);
        return $result;
    }

    /**
     * @param $amount
     * @return int
     */
    private function ReverseTransaction()
    {
        /*$param = array(
            'RefNum' => $this->ReferenceNumber,
            'MerchantID' => $this->APPSET->sb24MerchantID,
            'Password' => $this->APPSET->sb24MerchantPass,
            'RevAmount' => $amount
        );*/

        //$this->client->endpoint = $this->APPSET->sb24WSDL_Rev;
        //$result = $this->client->call('reverseTransaction', $param);
        $proxy = $this->client;
        $result = $proxy->ReverseTransaction($this->ReferenceNumber, $this->sb24MerchantID, $this->sb24MerchantPass);
        if (is_array($result)) {
            $msg = 'Bank Server Error -> <br/>';
            foreach ($result as $key => $val) {
                $msg .= $key . ' -> ' . $val . "<br/>";
            }
            return $msg;
        } else {
            return $this->ErrorCodes[$result];
        }
    }
}