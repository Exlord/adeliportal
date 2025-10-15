<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/22/2014
 * Time: 1:07 PM
 */

namespace Sms\API\Service;


use SoapFault;

class PaneleSmsCom extends Panel
{
    public function Send($username, $password, $from, $to, $msg)
    {
        try {
            $client = new \Zend\Soap\Client('http://www.panelesms.com/post/send.asmx?wsdl');
            $client->setEncoding('UTF-8');
        } catch (\SoapFault $s) {
            $this->api->hasError = true;
            return 'ERROR: [' . $s->faultcode . '] ' . $s->faultstring;
        } catch (\Exception $e) {
            $this->api->hasError = true;
            return 'ERROR: ' . $e->getMessage();
        }
        if (!is_array($to))
            $to = explode(',', $to);
        $param = array(
            'username' => $username,
            'password' => $password,
            'from' => $from,
            'to' => $to,
            'text' => $msg,
            'isflash' => false,
            'udh' => ''
        );

        try {
            $result = $client->SendSMS($param);
        } catch (SoapFault $s) {
            $this->api->hasError = true;
            return 'ERROR: [' . $s->faultcode . '] ' . $s->faultstring;
        } catch (\Exception $e) {
            $this->api->hasError = true;
            return 'ERROR: ' . $e->getMessage();
        }

        if ($result->SendSmsResult && $result->SendSmsResult == 1)
            $result = "عملیات با موفقیت انجام شد.";
        return $result;
    }
} 