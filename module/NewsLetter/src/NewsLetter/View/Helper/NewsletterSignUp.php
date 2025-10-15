<?php
namespace NewsLetter\View\Helper;


class NewsletterSignUp extends \Zend\View\Helper\AbstractHelper
{
    public function __invoke()
    {
        $html = '';
        $html = $this->view->render('news-letter/news-letter/sign-up');
        $form = new \RSS\Form\NewsletterSignUp();
        $form->prepare();
        $html .= $this->view->form()->openTag($form);
        $html .= $this->view->formRow($form->get('email'));
        $html .= $this->view->formRow($form->get('newsletter_sign_up_Csrf'));
        $html .= $this->view->formRow($form->get('submit'));
        $html .= $this->view->form()->closeTag();
        return $html;
    }

}