<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Company: AzarIPT
 * Date: 6/25/12
 * Time: 2:45 PM
 */

namespace Sms\API;

use Sms\API\Service\Panel;

class SMS
{
    private $username;
    private $password;
    private $from;

    /**
     * @var Panel
     */
    private $sender = null;
    private $allowedPanels = array('PaneleSmsCom', 'SmsIR');

    public $hasError = false;

    public function __construct()
    {
        $config = getConfig('sms')->varValue;
        $this->username = @$config['username'];
        $this->password = @$config['password'];
        $this->from = @$config['from'];
        $panel = @$config['panel'];
        if (!in_array($panel, $this->allowedPanels))
            $panel = 'PaneleSmsCom';

        $panel = '\Sms\API\Service\\' . $panel;
        if ($this->username && $this->password && $this->from) {
            $this->sender = new $panel($this);
        }
    }

    public function send_sms($to, $msg)
    {
        if ($this->sender) {
            return $this->sender->Send($this->username, $this->password, $this->from, $to, $msg);
        }
        return false;
    }
}