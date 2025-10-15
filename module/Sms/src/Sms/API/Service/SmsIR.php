<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/22/2014
 * Time: 1:06 PM
 */

namespace Sms\API\Service;


class SmsIR extends Panel
{
    public function Send($username, $password, $from, $to, $msg)
    {
        try {
            $client = new \Zend\Soap\Client('http://n.sms.ir/ws/SendReceive.asmx?wsdl');
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

        array_walk($to, function (&$item) {
            $item = doubleval($item);
        });

        $parameters['userName'] = $username;
        $parameters['password'] = $password;
        $parameters['mobileNos'] = $to;
        $parameters['messages'] = array($msg);
        $parameters['lineNumber'] = $from;
        $parameters['sendDateTime'] = date("Y-m-d") . "T" . date("H:i:s");


        try {
            $result = $client->SendMessageWithLineNumber($parameters);
        } catch (\SoapFault $s) {
            $this->api->hasError = true;
            return 'ERROR: [' . $s->faultcode . '] ' . $s->faultstring;
        } catch (\Exception $e) {
            $this->api->hasError = true;
            return 'ERROR: ' . $e->getMessage();
        }

        if ($result) {
            $result = (array)$result;
            if (isset($result['message']) && !empty($result['message'])) {
                $this->api->hasError = true;
                return $result['message'];
            }
        }

//        if ($result->SendSmsResult && $result->SendSmsResult == 1)
        $result = "عملیات با موفقیت انجام شد.";
        return $result;
    }
}