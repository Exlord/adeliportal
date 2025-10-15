<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/20/14
 * Time: 8:58 AM
 */
return array(
    'User' => array(
        'account_not_approved' => array(
            'label' => 'Account Not Approved',
            'description' => 'a new user account need approval from admin and it has NOT been approved',
            'notify_with' => array(
                'sms' => 'Dear __USERNAME__, your account has not been approved.',
                'email' => 'user/template/account-not-approved-email'
            ),
        ),
        'account_approved' => array(
            'label' => 'Account Approved',
            'description' => 'a new user account need approval from admin and it has been approved',
            'notify_with' => array(
                'sms' => 'Dear __USERNAME__, your account has been approved.',
                'email' => 'user/template/account-approved-email',
            ),
        ),
        'status_changed' => array(
            'label' => 'Status Changed',
            'description' => 'when admin changes user status (approve, disapprove, ban ...)',
            'notify_with' => array(
                'sms' => 'Dear __USERNAME__,your account status has changed to __STATUS__.',
                'email' => 'user/template/account-status-changed-email'
            ),
        ),
        'password_recovery' => array(
            'label' => 'Password Recovery',
            'description' => 'password recovery by admin or user itself',
            'notify_with' => array(
                'sms' => 'Your new Password : __PASS__ ,\n __SITE__',
                'email' => 'user/template/password-recovery-email'
            ),
        ),
        'user_registered' => array(
            'label' => 'Register',
            'description' => 'a new user has been registered in the site',
            'notify_with' => array(
                'sms' => 'welcome to __SITE__\n username:__USERNAME__ \n password:__PASS__',
                'email' => 'user/template/register-email'
            ),
        ),
    ),
);