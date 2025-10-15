<?php
namespace Mail\View\Helper;

use Application\API\App;
use System\View\Helper\BaseHelper;

class QuickSendMail extends BaseHelper
{
    public function __invoke($email)
    {
        $form = new \Mail\Form\QuickSendMail($email);
        return $this->view->render('mail/mail/quick-send-mail', array(
            'form' => $form
        ));
    }
}