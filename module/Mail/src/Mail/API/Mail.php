<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace Mail\API;

use Mail\Model\MailArchiveTable;
use Mail\Model\MailTable;
use Mail\Model\SendTable;
use System\API\BaseAPI;
use Zend\Form\Element;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mime\Mime;
use Zend\View\Model\ViewModel;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class Mail extends BaseAPI
{
    const MAX_PER_HOUR = 200;

    public static $DefaultSender = 'no-reply@ipt-system.com';
    public static $DefaultRecipient = 'info@ipt-system.com';
    public static $DefaultSenderName = 'IPT System';
    public static $DefaultRecipientName = 'IPT System';
    private static $_isLocal = null;

    private static function _isLocal()
    {
        if (is_null(self::$_isLocal))
            self::$_isLocal = file_exists(ROOT . '/local');

        return self::$_isLocal;
    }

    public static function getDefaultSender()
    {
        $config = getSM('config_table')->getByVarName('mail_config')->varValue;
        if (!isset($config['emailFrom']))
            $config['emailFrom'] = self::$DefaultSender;
        if (!isset($config['emailFromName']))
            $config['emailFromName'] = self::$DefaultSenderName;
        return $config;
    }

    public static function getDefaultRecipient()
    {
        $config = getSM('config_table')->getByVarName('mail_config')->varValue;
        if (!isset($config['emailTo']))
            $config['emailTo'] = self::$DefaultRecipient;
        if (!isset($config['emailToName']))
            $config['emailToName'] = self::$DefaultRecipientName;
        return $config;
    }

    public static function getFrom($config_name = 'mail_config')
    {
        $d = self::getDefaultSender();
        if ($config_name != 'mail_config') {
            $config = getSM('config_table')->getByVarName($config_name)->varValue;
            if (isset($config['emailFrom']))
                $d['emailFrom'] = $config['emailFrom'];
            if (isset($config['emailFromName']))
                $d['emailFromName'] = $config['emailFromName'];
        }
        return array($d['emailFrom'] => $d['emailFromName']);
    }

    public static function getTo($config_name = 'mail_config')
    {
        $d = self::getDefaultRecipient();
        if ($config_name != 'mail_config') {
            $config = getSM('config_table')->getByVarName($config_name)->varValue;
            if (isset($config['emailTo']))
                $d['emailTo'] = $config['emailTo'];
            if (isset($config['emailFromName']))
                $d['emailToName'] = $config['emailToName'];
        }
        return array($d['emailTo'] => $d['emailToName']);
    }

    public function addToQueue($to, $from, $subject, $body, $entityType, $queued = 1)
    {
        $address = array();
        if (is_scalar($to)) {
            $to = explode(',', $to);
        }

        foreach ($to as $key => $value) {
            if (is_numeric($key)) {
                $name = $value;
                $email = $value;
            } else {
                $name = $value;
                $email = $key;
            }
            $name = explode('@', $name);
            $name = $name[0];
            $address[$email] = $name;
        }
        $to_stack = array_chunk($address, 50, true);

        if (is_scalar($from)) {
            $fromName = $from;
        } else {
            $fromName = current($from);
            $from = key($from);
        }
        $fromName = explode('@', $fromName);
        $fromName = $fromName[0];
        $from = array($from => $fromName);

        if (!has_value($subject))
            throw new \Exception('Mail subject can not be empty');
        if (!has_value($body))
            throw new \Exception('Mail body can not be empty');
        else {
            $view = new ViewModel();
            $view->setTemplate('mail/template/wrapper');
            $view->setTerminal(true);
            $view->setVariables(array('content' => $body));
            $body = getSM('ViewRenderer')->render($view);
        }


        $model = new \Mail\Model\Mail();
        $model->from = serialize($from);
        $model->subject = $subject;
        $model->body = $body;
        $model->entityType = $entityType;
        $model->domain = ACTIVE_SITE;
        $model->queued = $queued;
        $model->filters = array('sendTime', 'message');

        foreach ($to_stack as $to) {
            $model->count = count($to);
            $model->to = serialize($to);
            $this->getTable()->save($model);
        }
        if (!$queued && !self::_isLocal())
            $this->send();
    }

    public function send()
    {
        $mails = $this->getTable()->getMails();
        $count = 0;
        $ids = array();
        foreach ($mails as $email) {
            $ids[] = $email['id'];
        }
        $this->getTable()->update(array('status' => 1), array('id' => $ids));
        $ids = null;

        /* @var $email \Mail\Model\Mail */
        foreach ($mails as $email) {

            $email['to'] = unserialize($email['to']);
            $email['from'] = unserialize($email['from']);

            $html = new MimePart($email['body']);
            $html->charset = "UTF-8";
            $html->type = Mime::TYPE_HTML;
            $html->disposition = Mime::DISPOSITION_INLINE;

            $body = new MimeMessage();
            $body->setParts(array($html));

            $mail = new Message();
            $mail->setBody($body);
            $mail->setFrom($email['from']);
            $mail->setTo($email['to']);
            $mail->setSubject($email['subject']);

            if ($mail->isValid()) {
                $model = $email;
                $model['status'] = 2;
                try {
                    $transport = new Sendmail();
                    $transport->send($mail);
                    $count += (int)$email['count'];
                } catch (\Exception $ex) {
                    $model['status'] = -1;
                    $model['message'] = ___exception_trace($ex);
                }
                $this->getTable()->remove($email['id']);

                unset($model['queued']);
                $model['sendTime'] = time();
                $model['id'] = 0;
                $model['to'] = serialize($email['to']);
                $model['from'] = serialize($email['from']);
                $this->getArchiveTable()->save($model);
                $this->getSendTable()->setCount($count);
            }
        }
    }

    /**
     * @return MailTable
     */
    private function getTable()
    {
        return getSM('mail_queue_table');
    }

    /**
     * @return MailArchiveTable
     */
    private function getArchiveTable()
    {
        return getSM('mail_archive_table');
    }

    /**
     * @return SendTable
     */
    private function getSendTable()
    {
        return getSM('mail_send_table');
    }
}