<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 3:13 PM
 */

namespace NewsLetter\API;


use Application\Model\Config;
use Cron\API\Cron;
use Mail\API\Mail;
use Menu\Form\MenuItem;
use Zend\EventManager\Event;

class EventManager
{
    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $form->menuTypes['newsletter-sign-up'] = array(
            'label' => 'Newsletter Sign-up',
            'note' => 'allows users to signup for your newsletter',
            'params' => array(array('route' => 'app/newsletter-sign-up'),),
        );
    }

    public function onCronRun(Config $last_run)
    {
        $output = '';
        $config = getConfig('newsLetter_config')->varValue;
        if (isset($config['during']) && $config['during'])
            $intervalTime = $config['during'];
        else
            $intervalTime = 1;
        $start = microtime(true);
        $interval = '+ ' . $intervalTime . ' day';
        $last = @$last_run->varValue['NewsLetter_last_run'];
        if (Cron::ShouldRun($interval, $last)) {
            $notify = getNotifyApi();
            if ($notify) {
                $config = getConfig('newsLetter_config_more')->varValue;
                $html = '';
                foreach ($config as $key => $row) {
                    $dataArray = null;
                    if (isset($row['select']))
                        foreach ($row['select'] as $val)
                            $dataArray[$val['catId']] = $val['count'];
                    if (getSM()->has($key)) {
                        $api = getSM($key);
                        $html[$key] = $api->getNewsLetter($dataArray);
                    }
                }
                if (is_array($html)) {
                    //get email
                    $emails = getSM('news_letter_sign_up_table')->getEmails(1);
                    //end
                    if (is_array($emails)) {
                        foreach ($emails as $email => $emailConfig) {
                            foreach ($html as $rowHtml) {
                                if (is_array($emailConfig)) {
                                    foreach ($emailConfig as $catId) {
                                        if (isset($rowHtml[$catId]))
                                            $output .= $rowHtml[$catId];
                                    }
                                } else {
                                    if (is_array($rowHtml))
                                        foreach ($rowHtml as $valHtml)
                                            $output .= $valHtml;
                                }
                            }
                            if ($email) {
                                $emailNotify = $notify->getEmail();
                                $emailNotify->to = $email;
                                $emailNotify->from = Mail::getFrom();
                                $emailNotify->subject = t('NEWSLETTER_SUBJECT');
                                $emailNotify->entityType = \NewsLetter\Module::ENTITY_TYPE;
                                $emailNotify->queued = 0;
                            }
                            $params = array(
                                '__Content__' => $html,
                            );
                            $notify->notify('NewsLetter', 'NEWSLETTER_NOTIFY', $params);
                        }
                    }
                }
            }
            //end
            db_log_info(sprintf(t('Newsletter send cron was run in %s at %s'), Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
            $last_run->varValue['NewsLetter_last_run'] = time();
        }
    }
} 