<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/12/14
 * Time: 1:36 PM
 */
return array(
    'Ads' => array(
        'ads_new' => array(
            'label' => 'ADS_NEW',
            'description' => 'ADS_NOTIFY_NEW_DESC',
            'notify_with' => array(
                'sms' => 'ADS_NOTIFY_SMS_NEW',
                'email' => 'ads/notify/new',
                'internal' => 'ADS_NOTIFY_SMS_NEW'
            ),
        ),
        'ads_approved' => array(
            'label' => 'ADS_APPROVED',
            'description' => 'ADS_APPROVED_DESC',
            'notify_with' => array(
                'sms' => 'ADS_NOTIFY_SMS_APPROVED',
                'email' => 'ads/notify/approved',
                'internal' => 'ADS_NOTIFY_SMS_APPROVED'
            ),
        ),
        'ads_not_approved' => array(
            'label' => 'ADS_NOT_APPROVE',
            'description' => 'ADS_NOT_APPROVE_DESC',
            'notify_with' => array(
                'sms' => 'ADS_NOTIFY_SMS_NOT_APPROVED',
                'email' => 'ads/notify/not-approved',
                'internal' => 'ADS_NOTIFY_SMS_NOT_APPROVED'
            ),
        ),
        'ads_new_validate' => array(
            'label' => 'ADS_PAYMENT_NEW',
            'description' => 'ADS_PAYMENT_NEW_DESC',
            'notify_with' => array(
                'sms' => 'ADS_NOTIFY_NEW_VALIDATE',
                'email' => 'ads/notify/new-validate',
            ),
        ),
        'ads_will_expire' => array(
            'label' => 'ADS_WILLING_EXPIRE',
            'description' => 'ADS_WILLING_EXPIRE_DESC',
            'notify_with' => array(
                'sms' => 'ADS_NOTIFY_WILLING_EXPIRE_SMS',
                'email' => 'ads/notify/will-expire',
            ),
        ),
        'ads_expired' => array(
            'label' => 'ADS_EXPIRED_AD',
            'description' => 'ADS_EXPIRED_AD_DESC',
            'notify_with' => array(
                'sms' => 'ADS_NOTIFY_EXPIRED_SMS',
                'email' => 'ads/notify/expired',
            ),
        ),
        'ads_view_data_validate' => array(
            'label' => 'ADS_PAYMENT_VIEW_ALL',
            'description' => 'ADS_PAYMENT_VIEW_ALL_DESC',
            'notify_with' => array(
                'sms' => 'ADS_NOTIFY_VIEW_DATA_VALIDATE',
                'email' => 'ads/notify/view-data-validate',
            ),
        ),
        'ads_send_like_request' => array(
            'label' => 'ADS_LIKE_REQUEST',
            'description' => 'ADS_LIKE_REQUEST_DESC',
            'notify_with' => array(
                'sms' => 'ADS_NOTIFY_LIKE_REQUEST_VALIDATE',
                'email' => 'ads/notify/like-request-validate',
            ),
        ),
    )
);