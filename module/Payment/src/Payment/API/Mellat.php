<?php
/**
 * Created by PhpStorm.
 * User: Ali
 * Date: 11/17/13
 * Time: 3:00 PM
 */

namespace Payment\API;


class Mellat extends Payment
{

    const STATUS_UNKNOWN = 0;
    const STATUS_RETURNED = 1;
    const STATUS_VERIFIED = 2;
    const STATUS_NOT_VERIFIED = 3;
    const STATUS_SETTLED = 4;
    const STATUS_NOT_SETTLED = 5;

    private static $ErrorCodes = array(
        0 => 'تراكنش با موفقيت انجام شد',
        11 => 'شماره كارت نامعتبر است',
        12 => 'موجودي كافي نيست',
        13 => 'رمز نادرست است',
        14 => 'تعداد دفعات وارد كردن رمز بيش از حد مجاز است',
        15 => 'كارت نامعتبر است',
        17 => 'كاربر از انجام تراكنش منصرف شده است',
        18 => 'تاريخ انقضاي كارت گذشته است',
        111 => 'صادر كننده كارت نامعتبر است',
        112 => 'خطاي سوييچ صادر كننده كارت',
        113 => 'پاسخي از صادر كننده كارت دريافت نشد',
        114 => 'دارنده كارت مجاز به انجام اين تراكنش نيست',
        21 => 'پذيرنده نامعتبر است',
        22 => 'ترمينال مجوز ارايه سرويس درخواستي را ندارد.',
        23 => 'خطاي امنيتي رخ داده است',
        24 => 'اطلاعات كاربري پذيرنده نامعتبر است',
        25 => 'مبلغ نامعتبر است',
        31 => 'پاسخ نامعتبر است',
        32 => 'فرمت اطلاعات وارد شده صحيح نمي باشد',
        33 => 'حساب نامعتبر است',
        34 => 'خطاي سيستمي',
        35 => 'تاريخ نامعتبر است',
        41 => 'شماره درخواست تكراري است',
        42 => 'تراکنش sale یافت نشد',
        43 => 'قبلا درخواست verify داده شده است',
        44 => 'درخواست verify یافت نشد',
        45 => 'تراکنش settle شده است',
        46 => 'تراکنش settle نشده است',
        47 => 'نراکنش settle  یافت نشد',
        48 => 'تراکنش reverce شده است',
        49 => 'تراکنش refund یافت نشد',
        412 => 'شناسه قبض نادرست است',
        413 => 'شناسه پرداخت نادرست است',
        414 => 'سازمان صادر كننده قبض نامعتبر است',
        415 => 'زمان جلسه كاري به پايان رسيده است',
        416 => 'خطا در ثبت اطلاعات',
        417 => 'شناسه پرداخت كننده نامعتبر است',
        418 => 'اشكال در تعريف اطلاعات مشتري',
        419 => 'تعداد دفعات ورود اطلاعات از حد مجاز گذشته است',
        421 => 'IP پذیرنده نامعتبر است',
        51 => 'تراكنش تكراري است',
        52 => 'سرويس درخواستي موجود نمي باشد',
        54 => 'تراكنش مرجع موجود نيست',
        55 => 'تراكنش نامعتبر است',
        61 => 'خطا در واريز'
    );

    private $id;
    private $TerminalId;
    private $UserName;
    private $Password;
    private $OrderId;
    private $Amount;
    private $LocalDate;
    private $LocalTime;
    private $AdditionalData;
    private $PayerId;
    private $ResCode;
    private $refId;
    private $SaleReferenceId;
    private $ERROR = false;

    private $_webservice = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';
//    private $_test_webservice = 'https://pgwstest.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl';

    private $_postUrl = 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat';
//    private $_test_postUrl = 'https://pgwtest.bpm.bankmellat.ir/pgwchannel/startpay.mellat';

    private $namespace = 'http://interfaces.core.sw.bps.com/';

    private static $THIS_FOLDER;
    private $className = 'Payment\API\Mellat';


    public function __construct($amount = null)
    {
        parent::__construct($amount);
        $selectBankInfo = getSM()->get('bank_info_table')->getAll(array('className' => $this->className))->current();
        $this->TerminalId = $selectBankInfo->terminalId;
        $this->UserName = $selectBankInfo->userName;
        $this->Password = $selectBankInfo->passWord;
        $this->OrderId = rand(1000000, 9999999);
        $this->LocalDate = date('Ymd');
        $this->LocalTime = date('Hi');
        $this->PayerId = 0;
        $this->__InitClient();
    }

    public function init($data = array())
    {
        $refId = $this->PAY_REQUEST($this->model->amount);
        $data['orderId'] = $this->OrderId;
        $this->model->data = $data;
        $this->model->refId = $refId;

        $id = $this->save();
        return $this->renderForm('payment/template/mellat',
            array(
                'action' => $this->_postUrl,
                'refId' => $refId,
            )
        );

    }

    public function validate($params = array())
    {
        $SaleOrderId = $params['SaleOrderId'];
        $this->SaleReferenceId = $SaleReferenceId = $params['SaleReferenceId'];
        if (isset($params['RefId']))
            $this->refId = $params['RefId'];
        else
            $this->refId = null;
        $this->ResCode = $params['ResCode'];

        if ($this->ResCode != 0) {
            $this->ERROR = true;
            return self::$ErrorCodes[$this->ResCode];
        }
        if (empty($this->refId) || $this->refId == ' ') {
            $this->ERROR = true;
            return 'مقادیر بازگشت از بانک صحیح نمیباشد.';
        }

        $where = array('refId' => $this->refId);

        getSM('payment_table')->update(array('status' => Mellat::STATUS_RETURNED), $where);

        $data = getSM('payment_table')->getAll(array('refId' => $this->refId))->current();
        $this->id = $data->id;
        $this->payedAmount = $this->Amount = $data->amount;
        $data->data = unserialize($data->data);
        $parameters = array(
            'terminalId' => $this->TerminalId,
            'userName' => $this->UserName,
            'userPassword' => $this->Password,
            'orderId' => $data->data['orderId'],
            'saleOrderId' => $SaleOrderId,
            'saleReferenceId' => (float)$SaleReferenceId
        );
        try {
            $result = $this->client->bpVerifyRequest($parameters)->return;
        } catch (\SoapFault $s) {
            $this->ERROR = true;
            getSM('payment_table')->update(array('status' => Mellat::STATUS_NOT_VERIFIED), $where);
            return 'ERROR: [' . $s->faultcode . '] ' . $s->faultstring;
        } catch (\Exception $e) {
            $this->ERROR = true;
            getSM('payment_table')->update(array('status' => Mellat::STATUS_NOT_VERIFIED), $where);
            return 'ERROR: ' . $e->getMessage();
        }

        if ($result != 0) {
            $this->ERROR = true;
            getSM('payment_table')->update(array('status' => Mellat::STATUS_NOT_VERIFIED), $where);
            return self::$ErrorCodes[$result];
        } else {

            getSM('payment_table')->update(array('status' => Mellat::STATUS_VERIFIED), $where);

            try {
                $result = $this->client->bpSettleRequest($parameters)->return;
            } catch (\SoapFault $s) {
                $this->ERROR = true;
                getSM('payment_table')->update(array('status' => Mellat::STATUS_NOT_SETTLED), $where);
                return 'ERROR: [' . $s->faultcode . '] ' . $s->faultstring;
            } catch (\Exception $e) {
                $this->ERROR = true;
                getSM('payment_table')->update(array('status' => Mellat::STATUS_NOT_SETTLED), $where);
                return 'ERROR: ' . $e->getMessage();
            }

            if ($result != 0) {
                $this->ERROR = true;
                getSM('payment_table')->update(array('status' => Mellat::STATUS_NOT_SETTLED), $where);
                return self::$ErrorCodes[$result];
            } else {
                $data->data['saleReferenceId'] = (float)$SaleReferenceId;
                $data->data['saleOrderId'] = $SaleOrderId;
                $data->data['status'] = Mellat::STATUS_SETTLED;

                $data_update = serialize($data->data);
                getSM('payment_table')->update(array('data' => $data_update), $where);
                return array('status' => true, 'id' => $data->id, 'trackingCode' => (float)$SaleReferenceId);
            }
        }
    }

    private function __InitClient()
    {
        try {
            $this->client = new \Zend\Soap\Client($this->_webservice,
                array(
                    'soapVersion' => SOAP_1_1,
                )
            );
        } catch (\Exception $ex) {
            $this->ERROR = true;
            return $ex;
        }
    }

    private function PAY_REQUEST($amount)
    {
        $this->Amount = $amount;

        $parameters = array(
            'terminalId' => $this->TerminalId,
            'userName' => $this->UserName,
            'userPassword' => $this->Password,
            'orderId' => $this->OrderId,
            'amount' => $this->Amount,
            'localDate' => $this->LocalDate,
            'localTime' => $this->LocalTime,
            'additionalData' => $this->AdditionalData,
            'callBackUrl' => $this->redirect,
            'payerId' => $this->PayerId
        );

        try {
            $result = $this->client->bpPayRequest($parameters)->return;
        } catch (\SoapFault $s) {
            $this->ERROR = true;
            return 'ERROR: [' . $s->faultcode . '] ' . $s->faultstring;
        } catch (\Exception $e) {
            $this->ERROR = true;
            return 'ERROR: ' . $e->getMessage();
        }

        //get the result
        $res = explode(',', $result);
        $this->ResCode = $res[0];
        $this->refId = @$res[1];

        if ($this->ResCode != 0) {
            $this->ERROR = true;
            return self::$ErrorCodes[$this->ResCode];
        } else {
            $allData = array(
                'amount' => $this->Amount,
                'refId' => $this->refId,
                'status' => 0
            );
//            $data = array(
//                'orderId' => $this->OrderId,
//                'localDate' => $this->LocalDate,
//                'localTime' => $this->LocalTime,
//                'additionalData' => $this->AdditionalData,
//                'saleReferenceId' => new \Zend\Db\Sql\Expression('NULL'),
//                'saleOrderId' => new \Zend\Db\Sql\Expression('NULL'),
//                'payerId' => $this->PayerId,
//            );
//            $allData['data'] = serialize($data);
//
//            $this->id = getSM('payment_table')->save($allData);
            return $this->refId;
        }
//

    }

    public function reverse($amount, $paymentId)
    {
        return false;
    }
}